<?php

namespace humhub\modules\requestSupport\notifications;

use Yii;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\requestSupport\models\SupportRequest;
use humhub\libs\Html;

class SupportRequestResponse extends BaseNotification
{
    public $moduleId = 'requestSupport';
    
    /**
     * @var bool do not send this notification also to the originator
     */
    public $suppressSendToOriginator = false;

    /**
     * @inheritdoc
     */
    public $viewName = 'supportRequestResponse';

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

        return Yii::t('RequestSupportModule.notifications', '{userName} responded to your support request in {spaceName}', [
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

        return Yii::t('RequestSupportModule.notifications', '{userName} responded to your support request in {spaceName}', [
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
        return Yii::t('RequestSupportModule.notifications', 'Response to your support request: {subject}', [
            'subject' => $this->source->subject
        ]);
    }

    public function getMailMessage()
    {
        return Yii::t('RequestSupportModule.notifications', 'Your support request has received a response from {user}.', [
            'user' => $this->originator->displayName
        ]) . "\n\n" .
        Yii::t('RequestSupportModule.notifications', 'Request: {subject}', [
            'subject' => $this->source->subject
        ]) . "\n\n" .
        Yii::t('RequestSupportModule.notifications', 'Please log in to view the complete response.');
    }

    public function getTitle()
    {
        return Yii::t('RequestSupportModule.notifications', 'Support Request Response');
    }

    public function getDescription()
    {
        return Yii::t('RequestSupportModule.notifications', 'Your support request has received a response.');
    }
} 