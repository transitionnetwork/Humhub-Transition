<?php

/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

namespace humhub\modules\transition\jobs;

use humhub\modules\queue\ActiveJob;
use humhub\modules\transition\Events;
use humhub\modules\user\models\User;

/**
 * Iterates over all active users and syncs the lat/lng profile field
 * from their members_map records.
 */
class SyncAllUsersLatLng extends ActiveJob
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        /** @var User $user */
        foreach (User::find()->active()->each(500) as $user) {
            Events::syncLatLngProfileField($user->id);
        }
    }
}
