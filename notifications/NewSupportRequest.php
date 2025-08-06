<?php

namespace humhub\modules\requestSupport\notifications;

use Yii;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\requestSupport\models\SupportRequest;
use humhub\libs\Html;

class NewSupportRequest extends BaseNotification
{
    public $moduleId = 'requestSupport';
    
    /**
     * @var bool do not send this notification also to the originator
     */
    public $suppressSendToOriginator = false;

    /**
     * @inheritdoc
     */
    public $viewName = 'newSupportRequest';

    public function __construct(...$args)
    {
        \Yii::info('Loaded NewSupportRequest notification class');
        parent::__construct(...$args);
    }

    public function category()
    {
        return new \humhub\modules\requestSupport\notifications\SupportRequestNotificationCategory();
    }

    public function html()
    {
        /** @var SupportRequest $request */
        $request = $this->source;

        if (!$request || !$this->originator) {
            return '';
        }

        return Yii::t('RequestSupportModule.notifications', '{userName} created a new support request in {spaceName}', [
            'userName' => Html::tag('strong', Html::encode($this->originator->displayName)),
            'spaceName' => Html::tag('strong', Html::encode($request->space->name))
        ]);
    }

    public function text()
    {
        /** @var SupportRequest $request */
        $request = $this->source;

        if (!$request || !$this->originator) {
            return '';
        }

        return Yii::t('RequestSupportModule.notifications', '{userName} created a new support request in {spaceName}', [
            'userName' => $this->originator->displayName,
            'spaceName' => $request->space->name
        ]);
    }

    public function getUrl()
    {
        return $this->source->getUrl();
    }

    public function getMailSubject()
    {
        return Yii::t('RequestSupportModule.notifications', 'New support request: {subject}', [
            'subject' => $this->source->subject
        ]);
    }

    public function getMailMessage()
    {
        return Yii::t('RequestSupportModule.notifications', 'A new support request has been created by {user} in {space}.', [
            'user' => $this->originator->displayName,
            'space' => $this->source->space->name
        ]) . "\n\n" .
        Yii::t('RequestSupportModule.notifications', 'Subject: {subject}', [
            'subject' => $this->source->subject
        ]) . "\n" .
        Yii::t('RequestSupportModule.notifications', 'Category: {category}', [
            'category' => $this->source->category
        ]) . "\n\n" .
        Yii::t('RequestSupportModule.notifications', 'Description: {description}', [
            'description' => $this->source->description
        ]);
    }

    public function getTitle()
    {
        return Yii::t('RequestSupportModule.notifications', 'New Support Request');
    }

    public function getDescription()
    {
        return Yii::t('RequestSupportModule.notifications', 'A new support request has been created.');
    }
} 