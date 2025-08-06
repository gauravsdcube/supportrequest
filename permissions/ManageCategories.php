<?php

namespace humhub\modules\requestSupport\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;

class ManageCategories extends BasePermission
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
        return 'Manage Support Categories';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'Allow user to manage support request categories in this space';
    }
} 