<?php

namespace humhub\modules\requestSupport\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;

class ManageSupportRequests extends BasePermission
{
    /**
     * @inheritdoc
     */
    protected $moduleId = 'requestSupport';

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_DENY;

    /**
     * @inheritdoc
     */
    protected $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
    ];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Manage Support Requests';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'Allow user to manage and respond to support requests in this space';
    }
} 