<?php

namespace humhub\modules\requestSupport;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\requestSupport\models\SupportCategory;
use humhub\modules\requestSupport\models\SupportRequest;
use humhub\modules\space\models\Space;
use yii\web\ForbiddenHttpException;

class Module extends ContentContainerModule
{
    public $resourcesPath = 'assets';

    public function getContentContainerTypes()
    {
        return [Space::class];
    }

    public function getName()
    {
        return 'Request Support';
    }

    public function getDescription()
    {
        return 'Allow space members to submit support requests to Space Administrators and Moderators';
    }

    public function getPermissions($contentContainer = null)
    {
        return $contentContainer ? [
            new permissions\CreateSupportRequest(),
            new permissions\ManageSupportRequests(),
            new permissions\ManageCategories(),
        ] : [];
    }

    /**
     * @inheritdoc
     */
    public function getMessageSource()
    {
        return [
            'class' => 'humhub\components\i18n\ModuleMessageSource',
            'moduleId' => 'requestSupport',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerDescription($container)
    {
        return 'Allow members to submit support requests';
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerName($container)
    {
        return 'Request Support';
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerIcon($container)
    {
        return 'fa-question-circle';
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerDefaultVisibility($container)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerImage($container)
    {
        return $this->getImage();
    }

    public function enableContentContainer(ContentContainerActiveRecord $container)
    {
        if (!$container instanceof Space) {
            throw new ForbiddenHttpException('This module can only be enabled in a Space');
        }

        parent::enableContentContainer($container);

        SupportCategory::createDefaultCategories($container->id);
    }

    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::disableContentContainer($container);

        foreach (SupportCategory::findAll(['space_id' => $container->id]) as $category) {
            $category->delete();
        }
    }

    public function disable()
    {
        foreach (SupportRequest::find()->each() as $request) {
            $request->delete();
        }

        foreach (SupportCategory::find()->each() as $category) {
            $category->delete();
        }

        return parent::disable();
    }
}
