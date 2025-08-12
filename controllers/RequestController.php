<?php

namespace humhub\modules\requestSupport\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\requestSupport\models\SupportCategory;
use humhub\modules\requestSupport\models\SupportRequest;
use humhub\modules\requestSupport\models\SupportResponse;
use humhub\modules\requestSupport\notifications\NewSupportRequest;
use humhub\modules\requestSupport\notifications\SupportRequestResponse;
use humhub\modules\requestSupport\permissions\ManageSupportRequests;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;

class RequestController extends ContentContainerController
{
    public function actionCreate()
    {
        $space = $this->contentContainer;
        $model = new SupportRequest($this->contentContainer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->sendCreateNotifications($model);

            return $this->redirect(['default/index', 'contentContainer' => $this->contentContainer]);
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => \yii\helpers\ArrayHelper::map(
                SupportCategory::find()->where(['space_id' => $space->id, 'is_active' => true])->all(),
                'id',
                'name',
            ),
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionView($id)
    {
        $model = SupportRequest::findOne($id);

        if (!$model || !$model->canView()) {
            throw new \yii\web\NotFoundHttpException('Support request not found.');
        }

        // Handle status update
        if (Yii::$app->request->post('update_status')) {
            if (!$model->canManage()) {
                throw new ForbiddenHttpException('You are not allowed to update this support request.');
            }

            $newStatus = Yii::$app->request->post('new_status');
            if (in_array($newStatus, array_keys(SupportRequest::getStatusOptions()))) {
                $model->status = $newStatus;
                if ($model->save()) {
                    $this->view->success(Yii::t('RequestSupportModule.base', 'Status updated successfully.'));
                } else {
                    $this->view->error(Yii::t('RequestSupportModule.base', 'Failed to update status.'));
                }
            }
            return $this->redirect(['view', 'id' => $id, 'contentContainer' => $this->contentContainer]);
        }

        $response = new SupportResponse();
        $response->request_id = $model->id;

        if ($response->load(Yii::$app->request->post())) {
            // Check if user can add responses to this request
            if (!$model->canAddResponse()) {
                throw new ForbiddenHttpException('You are not allowed to add responses to this request.');
            }

            if ($response->save()) {
                $this->sendResponseNotification($model);

                $this->view->success(Yii::t('RequestSupportModule.base', 'Response added successfully.'));
                return $this->redirect(['view', 'id' => $id, 'contentContainer' => $this->contentContainer]);
            }
        }

        return $this->render('view', [
            'model' => $model,
            'response' => $response,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = SupportRequest::findOne($id);

        if (!$model || !$model->canManage()) {
            throw new ForbiddenHttpException('You are not allowed to update this support request.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->success(Yii::t('RequestSupportModule.base', 'Support request updated successfully.'));
            return $this->redirect(['view', 'id' => $id, 'contentContainer' => $this->contentContainer]);
        }

        return $this->render('update', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    private function sendCreateNotifications($request)
    {
        /** @var Space $space */
        $space = $this->contentContainer;

        /** @var User $spaceMember */
        foreach (Membership::getSpaceMembersQuery($space)->each() as $spaceMember) {
            try {
                if ($space->getPermissionManager($spaceMember)->can(ManageSupportRequests::class)) {
                    NewSupportRequest::instance()
                        ->from(Yii::$app->user->identity)
                        ->about($request)
                        ->send($spaceMember);
                }
            } catch (InvalidConfigException $e) {
                Yii::error('Error sendCreateNotifications: ' . $e->getMessage(), 'supportRequest');
            }
        }
    }

    private function sendResponseNotification(SupportRequest $request)
    {
        $recipients = [];

        // Add Requester
        $requester = $request->requester;
        if ($request->requester && $requester->id !== Yii::$app->user->id) {
            $recipients[$requester->id] = $requester;
        }

        // Add all authors of responses (= participants in the discussion)
        foreach ($request->responses as $response) {
            $author = $response->author;
            if ($author && $author->id !== Yii::$app->user->id) {
                $recipients[$author->id] = $author;
            }
        }

        foreach ($recipients as $recipient) {
            SupportRequestResponse::instance()
                ->from(Yii::$app->user->identity)
                ->about($request)
                ->send($recipient);
        }
    }
}
