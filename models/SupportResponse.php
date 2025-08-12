<?php

namespace humhub\modules\requestSupport\models;

use humhub\modules\user\models\User;
use Yii;

/**
 * @property int $id
 * @property int $request_id
 * @property string $message
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property-read SupportRequest $request
 * @property-read User $author
 */
class SupportResponse extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'requestsupport_response';
    }

    public function rules()
    {
        return [
            [['request_id', 'message'], 'required'],
            [['request_id', 'created_by'], 'integer'],
            [['message'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'message' => 'Response',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getRequest()
    {
        return $this->hasOne(SupportRequest::class, ['id' => 'request_id']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
                $this->created_by = Yii::$app->user->id;
            }
            $this->updated_at = date('Y-m-d H:i:s');
            $this->updated_by = Yii::$app->user->id;
            return true;
        }
        return false;
    }
}
