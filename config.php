<?php

use humhub\modules\requestSupport\Events;

return [
    'id' => 'requestSupport',
    'class' => 'humhub\modules\requestSupport\Module',
    'namespace' => 'humhub\modules\requestSupport',
    'notifications' => [
        \humhub\modules\requestSupport\notifications\NewSupportRequest::class,
        \humhub\modules\requestSupport\notifications\SupportRequestResponse::class,
    ],
    'events' => [
        [
            'class' => \humhub\modules\space\models\Space::class,
            'event' => \humhub\modules\space\models\Space::EVENT_INIT,
            'callback' => [Events::class, 'onSpaceInit'],
        ],
        [
            'class' => \humhub\modules\space\widgets\Menu::class,
            'event' => \humhub\modules\space\widgets\Menu::EVENT_INIT,
            'callback' => [Events::class, 'onSpaceMenuInit'],
        ],
    ],
]; 