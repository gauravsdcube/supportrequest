<?php

namespace humhub\modules\requestSupport;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\requestSupport\models\SupportCategory;
use humhub\modules\space\widgets\Menu;

class Events
{
    public static function onSpaceInit($event)
    {
        /** @var Space $space */
        $space = $event->sender;
        
        // Only create default categories for new spaces and if the table exists
        if ($space->isNewRecord && self::tableExists()) {
            $defaultCategories = [
                'Technical Issue',
                'Account Problem', 
                'Content Issue',
                'General Question',
                'Bug Report',
                'Feature Request'
            ];

            foreach ($defaultCategories as $index => $categoryName) {
                $category = new SupportCategory();
                $category->name = $categoryName;
                $category->description = 'Default category for ' . $categoryName;
                $category->space_id = $space->id;
                $category->sort_order = $index;
                $category->is_active = true;
                $category->save();
            }
        }
    }

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
            'sortOrder' => 600,
        ]);
    }

    /**
     * Create default categories for a space if they don't exist
     */
    public static function createDefaultCategories($spaceId)
    {
        // Check if table exists and categories already exist for this space
        if (!self::tableExists()) {
            return;
        }
        
        $existingCategories = SupportCategory::find()->where(['space_id' => $spaceId])->count();
        
        if ($existingCategories > 0) {
            return; // Categories already exist
        }

        $defaultCategories = [
            'Technical Issue',
            'Account Problem', 
            'Content Issue',
            'General Question',
            'Bug Report',
            'Feature Request'
        ];

        foreach ($defaultCategories as $index => $categoryName) {
            $category = new SupportCategory();
            $category->name = $categoryName;
            $category->description = 'Default category for ' . $categoryName;
            $category->space_id = $spaceId;
            $category->sort_order = $index;
            $category->is_active = true;
            $category->save();
        }
    }

    /**
     * Check if the requestsupport_category table exists
     */
    private static function tableExists()
    {
        try {
            $db = Yii::$app->db;
            $tableName = 'requestsupport_category';
            $result = $db->createCommand("SHOW TABLES LIKE '{$tableName}'")->queryOne();
            return $result !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
} 