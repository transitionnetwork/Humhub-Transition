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
use humhub\libs\StringHelper;
use humhub\modules\ui\view\components\View;
use humhub\modules\user\widgets\Image;
use humhub\widgets\Label;

$this->beginPage();
$this->head();
$this->beginBody();
?>

    <style>
        body {
            padding: 10px !important;
            background-color: transparent;
        }

        /*.topbar, #stories-bar {*/
        /*    display: none !important;*/
        /*}*/
    </style>

<?php if (!$user) : ?>
    <?= Yii::t('TransitionModule.base', 'No user found on Humhub') ?>
<?php else : ?>

    <a href="<?= $user->createUrl(null, [], true) ?>">
        <div style="float: left; margin-right: 15px;"><?= Image::widget([
                'user' => $user,
                'width' => 60,
                'link' => false,
            ]) ?></div>
        <div style="float: left;">
            <div style="font-weight: bold;"><?= Html::encode($user->getDisplayName()) ?></div>
            <div><?= Label::defaultType(Html::encode($user->getDisplayNameSub())) ?></div>
            <div><?= Html::encode(StringHelper::truncate($user->profile->about, 50)) ?></div>
        </div>
    </a>
<?php endif; ?>

<?php
$this->endBody();
$this->endPage(true);