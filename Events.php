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
use humhub\modules\rest\components\BaseController;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\transition\helpers\MembershipHelper;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\user\models\User;
use humhub\widgets\TopMenu;
use Throwable;
use Yii;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\WidgetEvent;
use yii\helpers\BaseInflector;
use yii\helpers\Url;

class Events
{
    /**
     * On build of the TopMenu
     *
     * @param WidgetEvent $event
     */
    public static function onTopMenuBeforeRun($event)
    {
        /** @var TopMenu $menu */
        $menu = $event->sender;

        if ($entry = $menu->getEntryByUrl(Url::to(['/space/spaces']))) {
            $menu->removeEntry($entry);
            $entry->setUrl(['/space/spaces', 'sort' => 'older']);
            $menu->addEntry($entry);
        }
    }

    /**
     * @param Event $event
     * @throws Throwable
     * @throws InvalidConfigException
     */
    public static function onAdminUserMenuInit($event)
    {
        /** @var UserMenu $menu */
        $menu = $event->sender;

        if (Yii::$app->user->can(ManageUsers::class)) { // Don't move in 'isVisible' as it doesn't work in all cases and because the "if" costs less
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
        $user = User::findOne(['id' => $event->identity->getId()]);

        if (empty($user->profile->region)) {
            return;
        }

        $defaultSpaceId = $defaultSpaces[BaseInflector::slug($user->profile->region)] ?? null;
        if (!$defaultSpaceId) {
            return;
        }

        $space = Space::findOne($defaultSpaceId);
        if ($space === null) {
            return;
        }

        $space->addMember($user->id);
    }


    /**
     * Show /transition/profile-image/upload page after registering
     * @param ActionEvent $event
     * @return void
     * @throws \yii\base\Exception
     */
    public static function onBeforeControllerAction(ActionEvent $event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $currentModule = $event->action->controller->module->id;
        $currentController = $event->action->controller->id;
        $currentAction = $event->action->id;

        // Allow some modules actions
        if (
            ($currentModule === 'user' && $currentController === 'account' && $currentAction === 'delete')
            || ($currentModule === 'user' && $currentController === 'must-change-password')
            || ($currentModule === 'user' && $currentController === 'auth')
            || ($currentModule === 'mail' && $currentController === 'mail')
            || $currentController === 'poll'
            || $currentModule === 'legal'
            || $currentModule === 'transition'
            || $event->action->controller instanceof BaseController // REST API request (even if from a module)
            || ($currentModule === 'file' && $currentController === 'file' && $currentAction === 'download')
            || ($currentModule === 'twofa' && $currentController === 'check')
        ) {
            return;
        }

        $user = Yii::$app->user->identity;
        if (
            !$user->settings->get('hasSeenProfileImageUploadPage')
            && !$user->getProfileImage()->hasImage()
        ) {
            $event->isValid = false;
            $event->result = Yii::$app->response->redirect($user->createUrl('/transition/profile-image/upload'));
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

        MembershipHelper::updateMembershipToSpaceAdminsGroup($user);
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

        MembershipHelper::updateMembershipToSpaceAdminsGroup($user);
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

        MembershipHelper::updateMembershipToSpaceAdminsGroup($user);
    }
}