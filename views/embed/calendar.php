<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

/**
 * @var $this View
 */

use humhub\modules\calendar\helpers\Url;
use humhub\modules\calendar\widgets\FullCalendar;

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

<?= FullCalendar::widget([
    'canWrite' => false,
    'loadUrl' => Url::toAjaxLoad(),
]) ?>

<?php
$this->endBody();
$this->endPage(true);
