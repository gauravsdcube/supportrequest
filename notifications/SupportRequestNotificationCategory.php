<?php

namespace humhub\modules\requestSupport\notifications;

use humhub\modules\notification\components\NotificationCategory;

class SupportRequestNotificationCategory extends NotificationCategory
{
    /**
     * @inheritdoc
     */
    public $id = 'requestSupport';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return \Yii::t('RequestSupportModule.base', 'Support Requests');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return \Yii::t('RequestSupportModule.base', 'Notifications for support requests and responses.');
    }

    /**
     * @inheritdoc
     */
    public function getIcon()
    {
        return 'fa-life-ring';
    }
} 