<?php

/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

use humhub\modules\admin\widgets\UserMenu;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\transition\Events;
use humhub\modules\user\models\forms\Registration;

return [
    'id' => 'transition',
    'class' => humhub\modules\transition\Module::class,
    'namespace' => 'humhub\modules\transition',
    'events' => [
        [
            'class' => UserMenu::class,
            'event' => UserMenu::EVENT_INIT,
            'callback' => [Events::class, 'onAdminUserMenuInit'],
        ],
        [
            'class' => Registration::class,
            'event' => Registration::EVENT_AFTER_REGISTRATION,
            'callback' => [Events::class, 'onFormAfterRegistration'],
        ],
        [
            'class' => Membership::class,
            'event' => Membership::EVENT_MEMBER_ADDED,
            'callback' => [Events::class, 'onModelSpaceMembershipMemberAdded'],
        ],
        [
            'class' => Membership::class,
            'event' => Membership::EVENT_MEMBER_REMOVED,
            'callback' => [Events::class, 'onModelSpaceMembershipMemberRemoved'],
        ],
        [
            'class' => Membership::class,
            'event' => Membership::EVENT_AFTER_UPDATE,
            'callback' => [Events::class, 'onModelSpaceMembershipUpdate'],
        ],
        [
            'class' => Space::class,
            'event' => Space::EVENT_BEFORE_DELETE,
            'callback' => [Events::class, 'onModelSpaceBeforeDelete'],
        ],
        [
            'class' => Space::class,
            'event' => Space::EVENT_AFTER_UPDATE,
            'callback' => [Events::class, 'onModelSpaceAfterUpdate'],
        ],
    ],
];
