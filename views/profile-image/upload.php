<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

/**
 * @var $this \humhub\modules\ui\view\components\View
 * @var $user \humhub\modules\user\models\User
 */

use humhub\modules\content\widgets\ContainerProfileHeader;
use humhub\widgets\Button;

?>

<style>
    #transition-upload-profile-image .panel-profile-controls {
        display: none;
    }
</style>

<div id="transition-upload-profile-image" class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('TransitionModule.profile', 'Last step !') ?></div>
    <div class="panel-body">
        <div><?= Yii::t('TransitionModule.profile', 'Upload your profile picture.') ?></div>
        <div><strong><?= Yii::t('TransitionModule.profile', 'Mouse over the image and click on the cloud') ?></strong>
        </div>

        <br>
        <?= ContainerProfileHeader::widget(['container' => $user]) ?>
        <br>
        <br>

        <?= Button::primary(Yii::$app->user->getReturnUrl() ?
            Yii::t('TransitionModule.profile', 'I\'m done!') :
            Yii::t('TransitionModule.profile', 'I\'m done, show me the list of spaces I can join!')
        )
            ->link(Yii::$app->user->getReturnUrl() ?: ['/spaces']) ?>
    </div>
</div>
