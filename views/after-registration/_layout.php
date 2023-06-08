<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

/**
 * @var $this View
 * @var $content string
 */

use humhub\modules\membersMap\models\MembersMapUserLocation;
use humhub\modules\ui\view\components\View;
use humhub\modules\ui\view\helpers\ThemeHelper;
use humhub\widgets\FooterMenu;

?>

    <style>
        #transition-upload-profile-image .panel-profile-controls {
            display: none;
        }
    </style>

<div class="container<?= ThemeHelper::isFluid() ? '-fluid' : '' ?>">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>
                        <?php if ($this->context->currentModel === MembersMapUserLocation::class) : ?>
                            <?= Yii::t('TransitionModule.profile', 'Locate yourself so other members can see you on the map!') ?>
                        <?php else : ?>
                            <?= Yii::t('TransitionModule.profile', 'Last step !') ?>
                        <?php endif; ?>
                    </strong>
                </div>

                <div class="panel-body">
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>

<?= FooterMenu::widget(['location' => FooterMenu::LOCATION_FULL_PAGE]) ?>