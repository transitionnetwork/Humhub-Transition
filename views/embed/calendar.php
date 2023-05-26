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

use humhub\modules\calendar\widgets\FullCalendar;

?>
    <style>
        body {
            padding-top: 0;
        }

        #topbar {
            display: none;
        }
    </style>

<?= FullCalendar::widget([
    'canWrite' => false,
]) ?>