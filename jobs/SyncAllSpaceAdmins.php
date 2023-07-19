<?php
/**
 * Module Model
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

namespace humhub\modules\transition\jobs;


use humhub\modules\queue\ActiveJob;
use humhub\modules\transition\helpers\MembershipHelper;
use humhub\modules\transition\Module;
use humhub\modules\user\models\User;
use Yii;

class SyncAllSpaceAdmins extends ActiveJob
{
    public $tagFieldToRemove;

    /**
     * @inheritdoc
     */
    public function run()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('transition');
        if (!$module->spaceAdminsGroupId) {
            return;
        }

        /** @var User $user */
        foreach (User::find()->each(500) as $user) {
            MembershipHelper::updateMembershipToSpaceAdminsGroup($user, $this->tagFieldToRemove);
        }
    }
}
