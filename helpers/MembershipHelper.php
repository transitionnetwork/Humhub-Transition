<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

namespace humhub\modules\transition\helpers;

use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\transition\Module;
use humhub\modules\user\models\Group;
use humhub\modules\user\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;

class MembershipHelper
{
    /**
     * @param User|null $user
     * @return void
     */
    public static function updateMembershipToSpaceAdminsGroup(?User $user)
    {
        if (!$user) {
            return;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('transition');
        if (!$module->spaceAdminsGroupId) {
            return;
        }
        $spaceAdminsGroup = Group::findOne($module->spaceAdminsGroupId);
        if ($spaceAdminsGroup === null) {
            return;
        }

        $isSpaceAdmin = (bool)Membership::find()->where([
            'group_id' => Space::USERGROUP_ADMIN,
            'user_id' => $user->id,
        ])->count();

        if (
            $isSpaceAdmin
            && !$spaceAdminsGroup->isMember($user)
        ) {
            try {
                $spaceAdminsGroup->addUser($user);
            } catch (InvalidConfigException $e) {
            }
        }

        if (
            !$isSpaceAdmin
            && $spaceAdminsGroup->isMember($user)
        ) {
            try {
                $spaceAdminsGroup->removeUser($user);
            } catch (StaleObjectException|\Throwable $e) {
            }
        }
    }
}