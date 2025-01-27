<?php

/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

namespace humhub\modules\transition;

use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\admin\widgets\UserMenu;
use humhub\modules\legal\Module;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\transition\helpers\MembershipHelper;
use humhub\modules\transition\jobs\SyncAllSpaceHosts;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\user\models\ProfileField;
use humhub\modules\user\models\User;
use Throwable;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\helpers\BaseInflector;

class Events
{
    /**
     * @param Event $event
     * @throws Throwable
     * @throws InvalidConfigException
     */
    public static function onAdminUserMenuInit($event)
    {
        /** @var UserMenu $menu */
        $menu = $event->sender;

        if (
            Yii::$app->user->can(ManageUsers::class) // Don't move in 'isVisible' as it doesn't work in all cases and because the "if" costs less
            && ProfileField::findOne(['internal_name' => 'region'])
        ) {
            $menu->addEntry(new MenuLink([
                'label' => Yii::t('TransitionModule.config', 'Default spaces'),
                'url' => ['/transition/admin/default-spaces'],
                'sortOrder' => 2000,
                'isActive' => MenuLink::isActiveState('transition', 'admin', 'default-spaces'),
                'isVisible' => true,
            ]));
        }
    }


    /**
     * Add user to space depending on the Region field selected on the registration form
     * @param Yii\web\UserEvent $event
     * @return void
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public static function onFormAfterRegistration(yii\web\UserEvent $event)
    {
        // Do not store on console request
        if (Yii::$app->request->isConsoleRequest) {
            return;
        }

        if (!isset($event->identity)) {
            return;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('transition');
        $settings = $module->settings;
        $defaultSpaces = (array)$settings->getSerialized('defaultSpaces');

        // Get user from event because Yii::$app->user->id doesnt work here
        $user = User::findOne(['id' => $event->identity->id]);

        if (!$user->profile->hasAttribute('region')) {
            return;
        }

        $defaultSpaceIds = $defaultSpaces[BaseInflector::slug($user->profile->region)] ?? null;
        if (!$defaultSpaceIds) {
            return;
        }

        foreach (Space::findAll($defaultSpaceIds) as $space) {
            $space->addMember($user->id);
        }
    }

    /**
     * @param $event
     */
    public static function onModelSpaceMembershipMemberRemoved($event)
    {
        if (!isset($event)) {
            return;
        }

        /** @var Membership $membership */
        $membership = $event; // not $event->sender as it is executed by queue/run
        $user = $membership->user;

        MembershipHelper::updateUserTagsAndMembershipToSpaceHostsGroup($user);
    }

    /**
     * @param $event
     */
    public static function onModelSpaceMembershipMemberAdded($event)
    {
        if (!isset($event)) {
            return;
        }

        /** @var Membership $membership */
        $membership = $event; // not $event->sender as it is executed by queue/run
        $user = $membership->user;

        MembershipHelper::updateUserTagsAndMembershipToSpaceHostsGroup($user);
    }

    /**
     * @param $event
     */
    public static function onModelSpaceMembershipUpdate($event)
    {
        if (
            !isset($event->sender, $event->changedAttributes)
            || !array_key_exists('group_id', $event->changedAttributes)
        ) {
            return;
        }

        /** @var Membership $membership */
        $membership = $event->sender;
        $user = $membership->user;

        MembershipHelper::updateUserTagsAndMembershipToSpaceHostsGroup($user);
    }

    /**
     * @param $event
     * @return void
     */
    public static function onModelSpaceBeforeDelete($event)
    {
        if (empty($event->sender)) {
            return;
        }

        /** @var Space $space */
        $space = $event->sender;

        Yii::$app->queue->push(new SyncAllSpaceHosts([
            'tagFieldToRemove' => $space->name,
        ]));
    }

    /**
     * @param $event
     * @return void
     */
    public static function onModelSpaceAfterUpdate($event)
    {
        if (
            !isset($event->sender, $event->changedAttributes)
            || !array_key_exists('name', $event->changedAttributes)
        ) {
            return;
        }

        /** @var Space $space */
        $space = $event->sender;

        Yii::$app->queue->push(new SyncAllSpaceHosts([
            'tagFieldToRemove' => $space->name,
        ]));
    }
}
