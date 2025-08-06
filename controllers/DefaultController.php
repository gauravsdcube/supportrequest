<?php

namespace humhub\modules\requestSupport\controllers;

use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\requestSupport\models\SupportRequest;
use humhub\modules\requestSupport\models\SupportCategory;

class DefaultController extends ContentContainerController
{
    public function actionIndex()
    {
        // Create default categories if none exist
        \humhub\modules\requestSupport\Events::createDefaultCategories($this->contentContainer->id);

        $query = SupportRequest::find()
            ->contentContainer($this->contentContainer)
            ->orderBy(['created_at' => SORT_DESC]);

        // Filter by user permissions
        if (!$this->contentContainer->can(new \humhub\modules\requestSupport\permissions\ManageSupportRequests())) {
            $query->andWhere(['requestsupport_request.created_by' => Yii::$app->user->id]);
        }

        $requests = $query->all();

        return $this->render('index', [
            'requests' => $requests,
            'contentContainer' => $this->contentContainer,
        ]);
    }
} 