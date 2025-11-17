<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\space\widgets\SpacePickerField;
use yii\helpers\BaseInflector;

/**
 * @var $this View
 * @var $title string
 * @var $regionItems array
 * @var $defaultSpaces array
 */

AdminMenu::markAsActive(['/admin/default-spaces']);
?>

<div class="panel-body">
    <h4>
        <?= $title ?>
    </h4>
    <div class="text-body-secondary"><?= Yii::t('TransitionModule.config', 'Choose a space for each region. When a user registers a new account, depending on the region he choose, he will become a member to the corresponding space.') ?></div>

    <?= Html::beginForm() ?>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col"><strong><?= Yii::t('TransitionModule.config', 'Region') ?></strong></th>
            <th scope="col"><strong><?= Yii::t('TransitionModule.config', 'Default space') ?></strong></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($regionItems as $regionKey => $regionLabel): ?>
            <?php $regionKey = BaseInflector::slug($regionKey); ?>
            <tr>
                <th scope="row"><?= $regionLabel ?></th>
                <td>
                    <?= SpacePickerField::widget([
                        'name' => 'space-' . $regionKey,
                        'maxSelection' => 5,
                        'selection' => $defaultSpaces[$regionKey] ?? [],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= Html::saveButton() ?>
    <?= Html::endForm() ?>
</div>
