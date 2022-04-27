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
use humhub\modules\ui\menu\MenuLink;
use Throwable;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;

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


    }
}