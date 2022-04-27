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
use humhub\modules\space\models\Space;
use humhub\modules\ui\menu\MenuLink;
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

        $menu->addEntry(new MenuLink([
            'label' => Yii::t('TransitionModule.config', 'Default spaces'),
            'url' => ['/transition/admin/default-spaces'],
            'sortOrder' => 2000,
            'isActive' => MenuLink::isActiveState('transition', 'admin', 'default-spaces'),
            'isVisible' => Yii::$app->user->can(ManageUsers::class),
        ]));
    }


    /**
     * @param Yii\web\UserEvent $event
     * @return void
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
}