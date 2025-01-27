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
    public static function updateUserTagsAndMembershipToSpaceHostsGroup(?User $user, $tagFieldToRemove = null)
    {
        if (!$user) {
            return;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('transition');
        if (!$module->spaceHostsGroupId) {
            return;
        }
        $spaceHostsGroup = Group::findOne($module->spaceHostsGroupId);
        if ($spaceHostsGroup === null) {
            return;
        }

        $membershipQuery = Membership::find()->where([
            'group_id' => [Space::USERGROUP_ADMIN, Space::USERGROUP_MODERATOR],
            'user_id' => $user->id,
        ]);
        $isSpaceHost = (bool)$membershipQuery->count();

        // Update user tags (As a Space host user, for each space where the user is an admin or moderator, a tag of the space name is attached to the user account)
        $spaceTags = array_map(static function (Space $space) {
            return $space->name;
        }, Space::findAll(['status' => Space::STATUS_ENABLED]));
        $user->tagsField = array_diff((array)$user->tagsField, $spaceTags, ($tagFieldToRemove ? [] : [$tagFieldToRemove])); // don't remove `(array)` in front of `$user->tagsField` as it could be null
        if ($isSpaceHost) {
            /** @var Membership $membership */
            foreach ($membershipQuery->each() as $membership) {
                $user->tagsField[] = $membership->space->name;
            }
        }
        $user->tagsField = array_unique($user->tagsField); // If 2 Spaces have the same name
        $user->save();

        // Update user membership (group and related default spaces)
        if (
            $isSpaceHost
            && !$spaceHostsGroup->isMember($user)
        ) {
            try {
                $spaceHostsGroup->addUser($user);
            } catch (InvalidConfigException $e) {
            }
        }

        if (
            !$isSpaceHost
            && $spaceHostsGroup->isMember($user)
        ) {
            try {
                $spaceHostsGroup->removeUser($user);
            } catch (StaleObjectException|\Throwable $e) {
            }
            foreach ($spaceHostsGroup->getDefaultSpaces() as $space) {
                if ($space->isMember($user->id)) {
                    try {
                        $space->removeMember($user->id);
                    } catch (InvalidConfigException|\Throwable $e) {
                    }
                }
            }
        }
    }
}
