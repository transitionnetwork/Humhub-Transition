<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\user\widgets\Image;

/**
 * @var View $this
 * @var Message $message
 * @var MessageEntry[] $entries
 * @var string $url
 */
?>

<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

        <div class="modal-header">
            <h4 class="modal-title">
                <?= Html::encode($message->title ?: Yii::t('TransitionModule.config', '(no title)')) ?>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= Yii::t('base', 'Close') ?>"></button>
        </div>

        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

            <?php if (empty($entries)): ?>
                <p class="text-muted"><?= Yii::t('TransitionModule.config', 'No messages in this conversation.') ?></p>
            <?php else: ?>
                <div class="list-group list-group-flush mb-3">
                    <?php foreach ($entries as $entry): ?>
                        <?php $author = $entry->user; ?>
                        <div class="list-group-item px-0 py-2">
                            <div class="d-flex gap-2 align-items-start">
                                <?php if ($author): ?>
                                    <?= Image::widget([
                                        'user' => $author,
                                        'width' => 32,
                                        'link' => false,
                                        'showTooltip' => true,
                                    ]) ?>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= $author ? Html::encode($author->displayName) : '<em>' . Yii::t('TransitionModule.config', 'Deleted user') . '</em>' ?></strong>
                                        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($entry->created_at, 'medium') ?></small>
                                    </div>
                                    <div class="markdown-render">
                                        <?= RichText::output($entry->content) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <div class="modal-footer">
            <a href="<?= Html::encode($url) ?>" class="btn btn-primary" target="_blank">
                <?= Yii::t('TransitionModule.config', 'Open conversation') ?>
            </a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <?= Yii::t('base', 'Close') ?>
            </button>
        </div>

    </div>
</div>
