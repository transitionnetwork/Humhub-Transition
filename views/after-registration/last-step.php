<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

/**
 * @var $this View
 * @var $user User
 * @var AccountSettings $model
 * @var array $languages
 * @var $layout string
 */

use humhub\libs\TimezoneHelper;
use humhub\modules\content\widgets\ContainerProfileHeader;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\view\components\View;
use humhub\modules\user\models\forms\AccountSettings;
use humhub\modules\user\models\User;
use humhub\widgets\Button;

?>

<?php $this->beginContent($layout) ?>
    <div><?= Yii::t('TransitionModule.profile', 'Upload your profile picture.') ?></div>
    <div>
        <strong><?= Yii::t('TransitionModule.profile', 'Mouse over the image and click on the cloud') ?></strong>
    </div>

    <br>
    <div id="transition-upload-profile-image"><?= ContainerProfileHeader::widget(['container' => $user]) ?></div>
    <br>
    <br>

<?php $form = ActiveForm::begin(['id' => 'basic-settings-form', 'acknowledge' => true]); ?>

<?php if (count($languages) > 1) : ?>
    <?= $form->field($model, 'language')->dropDownList($languages, ['data-ui-select2' => '']); ?>
<?php endif; ?>

<?= $form->field($model, 'timeZone')->dropDownList(TimezoneHelper::generateList(true), ['data-ui-select2' => ''])->hint(Yii::t('TransitionModule.profile', 'This is an important setting to make sure you see event details correctly. Please set it correctly to your location.')) ?>

<?= Button::primary(Yii::$app->user->getReturnUrl() ?
    Yii::t('TransitionModule.profile', 'I\'m done!') :
    Yii::t('TransitionModule.profile', 'I\'m done, show me the list of spaces I can join!')
)->submit() ?>

<?php ActiveForm::end(); ?>
<?php $this->endContent(); ?>