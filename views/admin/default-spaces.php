<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

use humhub\libs\Html;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\ui\view\components\View;
use yii\helpers\BaseInflector;

/**
 * @var $this View
 * @var $title string
 * @var $regionItems array
 * @var $defaultSpaces array
 * @var $spaceItems array
 */

AdminMenu::markAsActive(['/admin/default-spaces']);
?>

<div class="panel-body">
    <h4>
        <?= $title ?>
    </h4>
    <div class="help-block"><?= Yii::t('TransitionModule.config', 'Default spaces') ?></div>

    <?= Html::beginForm() ?>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col"><strong><?= Yii::t('TransitionModule.config', 'Region') ?></strong></th>
            <th scope="col"><strong><?= Yii::t('TransitionModule.config', 'Default space') ?></strong></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($regionItems as $key => $label): ?>
            <?php $key = BaseInflector::slug($key); ?>
            <tr>
                <th scope="row"><?= $label ?></th>
                <td>
                    <?= Html::dropDownList('space-' . $key, $defaultSpaces[$key] ?? null, $spaceItems, ['prompt' => '']) ?>
                    <?php /* SpacePickerField::widget([
                        'id' => 'space-picker-' . $key,
                        'name' => 'space-' . $key,
                        'maxSelection' => 5,
                        'value' => $defaultSpaces[$key] ?? null,
                    ]) */ ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= Html::saveButton() ?>
    <?= Html::endForm() ?>
</div>