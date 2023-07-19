<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

/**
 * @var $this View
 * @var $user \humhub\modules\user\models\User
 */

use humhub\libs\Html;
use humhub\modules\ui\view\components\View;
use humhub\modules\user\widgets\Image;

?>
<style>
    body {
        padding: 10px !important;
    }

    #topbar, #stories-bar {
        display: none !important;
    }
</style>

<?php if (!$user) : ?>
    <?= Yii::t('TransitionModule.base', 'No user found on Humhub') ?>
<?php else : ?>
    <?= Image::widget([
        'user' => $user,
        'width' => 35,
        'link' => true,
    ]) ?>
    &nbsp;
    <?= Html::containerLink($user) ?>
<?php endif; ?>
