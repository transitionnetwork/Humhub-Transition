<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

/**
 * @var $this View
 * @var $overview OverviewWidget
 */

use humhub\assets\AppAsset;
use humhub\modules\cleanTheme\assets\CleanThemeAsset;
use humhub\modules\notification\widgets\OverviewWidget;
use humhub\modules\ui\view\components\View;

AppAsset::register($this);
CleanThemeAsset::register($this);

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

    <div class="container">
        <div class="row">
            <div class="col-md-12 layout-content-container">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Yii::t('NotificationModule.base', '<strong>Notification</strong> Overview') ?>
                    </div>
                    <div class="panel-body">
                        <?= $overview ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php
$this->endBody();
$this->endPage(true);
