<?php

namespace humhub\modules\requestSupport;

use humhub\modules\space\widgets\Menu;
use Yii;

class Events
{
    /**
     * Add menu items to space navigation menu
     */
    public static function onSpaceMenuInit($event)
    {
        /** @var Menu $menu */
        $menu = $event->sender;
        $space = $menu->space;

        if (!$space->moduleManager->isEnabled('requestSupport')) {
            return;
        }

        // Add Support menu item
        $menu->addItem([
            'label' => Yii::t('RequestSupportModule.base', 'Support'),
            'url' => $space->createUrl('/requestSupport/default'),
            'icon' => '<i class="fa fa-question-circle"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id === 'requestSupport'),
            'sortOrder' => 20002,
        ]);
    }
}
