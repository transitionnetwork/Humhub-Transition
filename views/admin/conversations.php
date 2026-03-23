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
use humhub\modules\mail\models\Message;
use humhub\modules\user\widgets\Image;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var string $title
 * @var ActiveDataProvider $dataProvider
 */

AdminMenu::markAsActive(['/admin/user/index']);
?>

<div class="panel-body">
    <h4>
        <?= Html::encode($title) ?>
    </h4>

    <?php Pjax::begin(['id' => 'transition-conversations-pjax']) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            [
                'label' => Yii::t('TransitionModule.config', 'Title'),
                'format' => 'raw',
                'value' => function (Message $message) {
                    $title = Html::encode($message->title ?: Yii::t('TransitionModule.config', '(no title)'));
                    $url = Url::to(['/transition/admin/conversation-detail', 'id' => $message->id]);
                    return Html::a(
                        $title,
                        $url,
                        [
                            'data-action-click' => 'ui.modal.load',
                            'data-action-url' => $url,
                        ]
                    );
                },
            ],
            [
                'label' => Yii::t('TransitionModule.config', 'Created At'),
                'value' => fn(Message $message) => Yii::$app->formatter->asDatetime($message->created_at, 'medium'),
                'options' => ['style' => 'width:160px; white-space:nowrap;'],
            ],
            [
                'label' => Yii::t('TransitionModule.config', 'Created By'),
                'format' => 'raw',
                'options' => ['style' => 'width:200px;'],
                'value' => function (Message $message) {
                    $user = $message->originator;
                    if ($user === null) {
                        return '<em>' . Yii::t('TransitionModule.config', 'Deleted user') . '</em>';
                    }
                    return Image::widget([
                        'user' => $user,
                        'width' => 24,
                        'link' => true,
                        'showTooltip' => true,
                    ]) . ' ' . Html::encode($user->displayName);
                },
            ],
            [
                'label' => Yii::t('TransitionModule.config', 'Participants'),
                'format' => 'raw',
                'value' => function (Message $message) {
                    $names = [];
                    foreach ($message->users as $user) {
                        $names[] = Html::encode($user->displayName);
                    }
                    return implode(', ', $names);
                },
            ],
        ],
    ]) ?>

    <?php Pjax::end() ?>
</div>
