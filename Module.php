<?php

namespace humhub\modules\requestSupport;

use Yii;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;

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
        return [
            new permissions\CreateSupportRequest(),
            new permissions\ManageSupportRequests(),
            new permissions\ManageCategories(),
        ];
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
} 