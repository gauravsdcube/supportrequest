<?php

namespace humhub\modules\requestSupport\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;

class CreateSupportRequest extends BasePermission
{
    /**
     * @inheritdoc
     */
    protected $moduleId = 'requestSupport';

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_ALLOW;

    /**
     * @inheritdoc
     */
    protected $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_MEMBER,
    ];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Create Support Request';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'Allow user to create support requests in this space';
    }
} 