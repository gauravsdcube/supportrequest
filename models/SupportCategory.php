<?php

namespace humhub\modules\requestSupport\models;

use humhub\modules\space\models\Space;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $space_id
 * @property int|null $sort_order
 * @property bool $is_active
 *
 * @property-read SupportRequest[] $requests
 * @property-read Space $space
 */
class SupportCategory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'requestsupport_category';
    }

    public function rules()
    {
        return [
            [['name', 'space_id'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['description'], 'string'],
            [['space_id'], 'integer'],
            [['sort_order'], 'integer'],
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Category Name',
            'description' => 'Description',
            'sort_order' => 'Sort Order',
            'is_active' => 'Active',
        ];
    }

    public function getSpace()
    {
        return $this->hasOne(Space::class, ['id' => 'space_id']);
    }

    public function getRequests()
    {
        return $this->hasMany(SupportRequest::class, ['category_id' => 'id']);
    }

    public static function getCategoriesForSpace($spaceId)
    {
        return self::find()
            ->where(['space_id' => $spaceId, 'is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }

    public static function getCategoryOptions($spaceId)
    {
        $categories = self::getCategoriesForSpace($spaceId);
        $options = [];
        foreach ($categories as $category) {
            $options[$category->name] = $category->name;
        }
        return $options;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                if ($this->sort_order === null) {
                    $this->sort_order = 0;
                }
                if ($this->is_active === null) {
                    $this->is_active = true;
                }
            }
            return true;
        }
        return false;
    }


    /**
     * Create default categories for a space if they don't exist
     */
    public static function createDefaultCategories($spaceId)
    {
        $existingCategories = static::find()->where(['space_id' => $spaceId])->exists();
        if ($existingCategories) {
            return;
        }

        $defaultCategories = [
            'Technical Issue',
            'Account Problem',
            'Content Issue',
            'General Question',
            'Bug Report',
            'Feature Request',
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

    public function afterDelete()
    {
        /** @var SupportRequest $request */
        foreach ($this->getRequests()->each() as $request) {
            $request->hardDelete();
        }
        parent::afterDelete();
    }
}
