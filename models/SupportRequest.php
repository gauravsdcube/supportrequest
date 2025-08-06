<?php

namespace humhub\modules\requestSupport\models;

use Yii;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\search\interfaces\Searchable;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\requestSupport\models\SupportCategory;

class SupportRequest extends ContentActiveRecord implements Searchable
{
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    public static function tableName()
    {
        return 'requestsupport_request';
    }

    public function rules()
    {
        return [
            [['subject', 'description', 'category'], 'required'],
            [['subject'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['category'], 'string', 'max' => 100],
            [['status'], 'in', 'range' => [self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_RESOLVED, self::STATUS_CLOSED]],
            [['status'], 'default', 'value' => self::STATUS_OPEN],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'subject' => 'Subject',
            'description' => 'Description',
            'category' => 'Category',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getContentName()
    {
        return 'Support Request';
    }

    public function getContentDescription()
    {
        return $this->subject;
    }

    public function getSearchAttributes()
    {
        return [
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->status,
            'category' => $this->category,
        ];
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed'
        ];
    }

    public function getRequester()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getSpace()
    {
        return $this->content->getContainer();
    }

    public function getResponses()
    {
        return $this->hasMany(SupportResponse::class, ['request_id' => 'id'])->orderBy(['created_at' => SORT_ASC]);
    }

    public function getCategoryName()
    {
        if (!$this->category) {
            return '';
        }
        
        $category = SupportCategory::findOne($this->category);
        return $category ? $category->name : $this->category;
    }

    public function canView($user = null)
    {
        if (!$user) {
            $user = Yii::$app->user->identity;
        }

        // System admin can view all
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Requester can view their own
        if ($this->created_by == $user->id) {
            return true;
        }

        // Space admin/moderator can view all in their space
        $space = $this->space;
        if ($space) {
            $userGroup = $space->getUserGroup($user);
            if (in_array($userGroup, [Space::USERGROUP_OWNER, Space::USERGROUP_ADMIN, Space::USERGROUP_MODERATOR])) {
                return true;
            }
        }

        return false;
    }

    public function canManage($user = null)
    {
        if (!$user) {
            $user = Yii::$app->user->identity;
        }

        // System admin can manage all
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Space admin/moderator can manage all in their space
        $space = $this->space;
        if ($space) {
            $userGroup = $space->getUserGroup($user);
            if (in_array($userGroup, [Space::USERGROUP_OWNER, Space::USERGROUP_ADMIN, Space::USERGROUP_MODERATOR])) {
                return true;
            }
        }

        return false;
    }

    public function canCreate($user = null)
    {
        if (!$user) {
            $user = Yii::$app->user->identity;
        }

        // System admin can create anywhere
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Space members can create in their spaces
        $space = $this->space;
        if ($space && $space->isMember($user)) {
            return true;
        }

        return false;
    }

    public function canAddResponse($user = null)
    {
        if (!$user) {
            $user = Yii::$app->user->identity;
        }

        // System admin can always add responses
        if ($user->isSystemAdmin()) {
            return true;
        }

        // If request is closed, only admins/moderators can add responses
        if ($this->status === self::STATUS_CLOSED) {
            $space = $this->space;
            if ($space) {
                $userGroup = $space->getUserGroup($user);
                return in_array($userGroup, [Space::USERGROUP_OWNER, Space::USERGROUP_ADMIN, Space::USERGROUP_MODERATOR]);
            }
            return false;
        }

        // For open requests, anyone who can view can add responses
        return $this->canView($user);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
                $this->created_by = Yii::$app->user->id;
                if ($this->status === null) {
                    $this->status = self::STATUS_OPEN;
                }
            }
            $this->updated_at = date('Y-m-d H:i:s');
            $this->updated_by = Yii::$app->user->id;
            return true;
        }
        return false;
    }

    public function getUrl()
    {
        return \yii\helpers\Url::to(['/requestSupport/request/view', 'id' => $this->id, 'contentContainer' => $this->content->container]);
    }
} 