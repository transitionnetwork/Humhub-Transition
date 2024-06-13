<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

/**
 * @var $this View
 * @var $space Space
 */

use humhub\modules\space\models\Space;
use humhub\modules\stream\widgets\StreamViewer;
use humhub\modules\ui\view\components\View;

$this->beginPage();
$this->head();
$this->beginBody();
?>
    <style>
        body {
            padding: 15px !important;
            background-color: transparent;
        }
    </style>

<?= StreamViewer::widget([
    'contentContainer' => $space,
    'streamAction' => '/space/space/stream',
    'messageStreamEmpty' => Yii::t('SpaceModule.base', '<b>This space is still empty!</b>'),
]) ?>

<?php
$this->endBody();
$this->endPage(true);
