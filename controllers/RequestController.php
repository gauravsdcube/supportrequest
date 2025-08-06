<?php

namespace humhub\modules\requestSupport\controllers;

use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\requestSupport\models\SupportRequest;
use humhub\modules\requestSupport\models\SupportCategory;
use humhub\modules\requestSupport\models\SupportResponse;
use humhub\modules\requestSupport\notifications\NewSupportRequest;
use humhub\modules\requestSupport\notifications\SupportRequestResponse;
use humhub\modules\user\models\User;

class RequestController extends ContentContainerController
{
    public function actionCreate()
    {
        error_log('=== REQUEST CONTROLLER ACTION CREATE STARTED ===');
        Yii::info('=== REQUEST CONTROLLER ACTION CREATE STARTED ===');
        
        $space = $this->contentContainer;
        $model = new SupportRequest($this->contentContainer);

        // Create default categories if they don't exist
        \humhub\modules\requestSupport\Events::createDefaultCategories($space);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            error_log('Support request saved successfully, ID: ' . $model->id);
            Yii::info('Support request saved successfully, ID: ' . $model->id);
            
            // Send notifications
            error_log('About to call sendNotifications for request ID: ' . $model->id);
            Yii::info('About to call sendNotifications for request ID: ' . $model->id);
            $this->sendNotifications($model);
            error_log('sendNotifications completed for request ID: ' . $model->id);
            Yii::info('sendNotifications completed for request ID: ' . $model->id);
            
            error_log('=== REQUEST CONTROLLER ACTION CREATE COMPLETED ===');
            Yii::info('=== REQUEST CONTROLLER ACTION CREATE COMPLETED ===');
            
            return $this->redirect(['default/index', 'contentContainer' => $this->contentContainer]);
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => \yii\helpers\ArrayHelper::map(
                SupportCategory::find()->where(['space_id' => $space->id, 'is_active' => true])->all(),
                'id',
                'name'
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
                throw new \yii\web\ForbiddenHttpException('You are not allowed to update this support request.');
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
                throw new \yii\web\ForbiddenHttpException('You are not allowed to add responses to this request.');
            }
            
            if ($response->save()) {
                Yii::info('=== RESPONSE ADDED SUCCESSFULLY ===');
                Yii::info('Response ID: ' . $response->id);
                Yii::info('Request ID: ' . $model->id);
                Yii::info('Response Created By: ' . Yii::$app->user->identity->username);
                
                // Send notification to requester
                Yii::info('About to call sendResponseNotification...');
                $this->sendResponseNotification($model, $response);
                Yii::info('sendResponseNotification called successfully');
                
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
            throw new \yii\web\ForbiddenHttpException('You are not allowed to update this support request.');
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

    private function sendNotifications($request)
    {
        error_log('=== SEND NOTIFICATIONS METHOD CALLED ===');
        Yii::info('=== SEND NOTIFICATIONS METHOD CALLED ===');
        error_log('Request ID: ' . $request->id);
        Yii::info('Request ID: ' . $request->id);
        error_log('Request Subject: ' . $request->subject);
        Yii::info('Request Subject: ' . $request->subject);
        error_log('Current User: ' . Yii::$app->user->identity->username);
        Yii::info('Current User: ' . Yii::$app->user->identity->username);
        error_log('=== METHOD ENTRY COMPLETED ===');
        Yii::info('=== METHOD ENTRY COMPLETED ===');
        
        // Write to a test file to confirm method is called
        file_put_contents('/tmp/notification_test.log', date('Y-m-d H:i:s') . ' - sendNotifications called for request ID: ' . $request->id . "\n", FILE_APPEND);
        
        Yii::info('=== STARTING NOTIFICATION PROCESS ===');
        try {
            $space = $this->contentContainer;
            $recipients = [];

            Yii::info('Space ID: ' . $space->id);
            Yii::info('Space Name: ' . $space->name);

            // Get space admins
            $admins = $space->getAdmins();
            error_log('Found ' . count($admins) . ' admins');
            Yii::info('Found ' . count($admins) . ' admins');
            foreach ($admins as $admin) {
                $recipients[] = $admin;
                error_log('Added admin: ' . $admin->username);
                Yii::info('Added admin: ' . $admin->username);
            }

            // Get space moderators by querying the membership table
            $moderators = \humhub\modules\space\models\Membership::find()
                ->joinWith('user')
                ->where([
                    'space_id' => $space->id,
                    'group_id' => \humhub\modules\space\models\Space::USERGROUP_MODERATOR,
                    'space_membership.status' => \humhub\modules\space\models\Membership::STATUS_MEMBER
                ])
                ->all();

            error_log('Found ' . count($moderators) . ' moderators');
            Yii::info('Found ' . count($moderators) . ' moderators');
            foreach ($moderators as $membership) {
                $recipients[] = $membership->user;
                error_log('Added moderator: ' . $membership->user->username);
                Yii::info('Added moderator: ' . $membership->user->username);
            }

            error_log('Total recipients: ' . count($recipients));
            Yii::info('Total recipients: ' . count($recipients));
            
            if (count($recipients) == 0) {
                error_log('No admins or moderators found, trying to get all space members');
                Yii::info('No admins or moderators found, trying to get all space members');
                
                // Fallback: get all space members
                $members = \humhub\modules\space\models\Membership::find()
                    ->joinWith('user')
                    ->where([
                        'space_id' => $space->id,
                        'space_membership.status' => \humhub\modules\space\models\Membership::STATUS_MEMBER
                    ])
                    ->all();
                
                error_log('Found ' . count($members) . ' space members');
                Yii::info('Found ' . count($members) . ' space members');
                
                foreach ($members as $membership) {
                    $recipients[] = $membership->user;
                    error_log('Added member: ' . $membership->user->username);
                    Yii::info('Added member: ' . $membership->user->username);
                }
                
                if (count($recipients) == 0) {
                    error_log('No recipients found at all - space has no members');
                    Yii::info('No recipients found at all - space has no members');
                    return;
                }
            }

            // Send notification to each recipient
            foreach ($recipients as $recipient) {
                try {
                    error_log('Attempting to send notification to: ' . $recipient->username);
                    Yii::info('Attempting to send notification to: ' . $recipient->username);
                    
                    // Simplified notification creation - go directly to database insertion
                    error_log('Creating notification for: ' . $recipient->username);
                    $notificationRecord = new \humhub\modules\notification\models\Notification();
                    $notificationRecord->class = 'humhub\modules\requestSupport\notifications\NewSupportRequest';
                    $notificationRecord->user_id = $recipient->id;
                    $notificationRecord->source_class = get_class($request);
                    $notificationRecord->source_pk = $request->id;
                    $notificationRecord->originator_user_id = Yii::$app->user->id;
                    $notificationRecord->created_at = date('Y-m-d H:i:s');
                    
                    error_log('Attempting to save notification for: ' . $recipient->username);
                    if ($notificationRecord->save()) {
                        error_log('Notification saved successfully for: ' . $recipient->username);
                        Yii::info('Notification saved successfully for: ' . $recipient->username);
                    } else {
                        error_log('Failed to save notification for: ' . $recipient->username);
                        error_log('Notification errors: ' . print_r($notificationRecord->errors, true));
                        Yii::error('Failed to save notification for: ' . $recipient->username);
                        Yii::error('Notification errors: ' . print_r($notificationRecord->errors, true));
                    }
                } catch (\Exception $e) {
                    error_log('Failed to send notification to ' . $recipient->username . ': ' . $e->getMessage());
                    error_log('Exception trace: ' . $e->getTraceAsString());
                    Yii::error('Failed to send notification to ' . $recipient->username . ': ' . $e->getMessage());
                    Yii::error('Exception trace: ' . $e->getTraceAsString());
                }
            }
        } catch (\Exception $e) {
            Yii::error('Failed to send notifications: ' . $e->getMessage());
            Yii::error('Exception trace: ' . $e->getTraceAsString());
        }
        Yii::info('=== NOTIFICATION PROCESS COMPLETED ===');
    }

    private function sendResponseNotification($request, $response)
    {
        Yii::info('=== STARTING RESPONSE NOTIFICATION PROCESS ===');
        try {
            $requester = User::findOne($request->created_by);
            Yii::info('Looking for requester with ID: ' . $request->created_by);
            
            if ($requester) {
                Yii::info('Found requester: ' . $requester->username);
                Yii::info('Current user ID: ' . Yii::$app->user->id);
                
                if ($requester->id !== Yii::$app->user->id) {
                    error_log('Sending response notification to: ' . $requester->username);
                    Yii::info('Sending response notification to: ' . $requester->username);
                    
                    // Simplified response notification creation - go directly to database insertion
                    error_log('Creating response notification for: ' . $requester->username);
                    $notificationRecord = new \humhub\modules\notification\models\Notification();
                    $notificationRecord->class = 'humhub\modules\requestSupport\notifications\SupportRequestResponse';
                    $notificationRecord->user_id = $requester->id;
                    $notificationRecord->source_class = get_class($request);
                    $notificationRecord->source_pk = $request->id;
                    $notificationRecord->originator_user_id = Yii::$app->user->id;
                    $notificationRecord->created_at = date('Y-m-d H:i:s');
                    
                    error_log('Attempting to save response notification for: ' . $requester->username);
                    if ($notificationRecord->save()) {
                        error_log('Response notification saved successfully for: ' . $requester->username);
                        Yii::info('Response notification saved successfully for: ' . $requester->username);
                    } else {
                        error_log('Failed to save response notification for: ' . $requester->username);
                        error_log('Notification errors: ' . print_r($notificationRecord->errors, true));
                        Yii::error('Failed to save response notification for: ' . $requester->username);
                        Yii::error('Notification errors: ' . print_r($notificationRecord->errors, true));
                    }
                } else {
                    error_log('No response notification sent - same user (self-response)');
                    Yii::info('No response notification sent - same user (self-response)');
                }
            } else {
                Yii::info('No response notification sent - requester not found');
            }
        } catch (\Exception $e) {
            Yii::error('Failed to send response notification: ' . $e->getMessage());
            Yii::error('Exception trace: ' . $e->getTraceAsString());
        }
        Yii::info('=== RESPONSE NOTIFICATION PROCESS COMPLETED ===');
    }
} 