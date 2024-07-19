<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

use humhub\assets\AppAsset;
use humhub\modules\cleanTheme\assets\CleanThemeAsset;
use humhub\modules\ui\view\components\View;

AppAsset::register($this);
CleanThemeAsset::register($this);

/**
 * @var $this View
 */

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

<?= $this->render('@mail/views/mail/_conversation_sidebar') ?>

<?php
$this->endBody();
$this->endPage(true);
