<?php
/**
 * This view can be deleted when https://github.com/humhub/calendar/issues/371 is resolved
 * When deleted, delete also the events-map/widgets/wallEntry.php view
 */


use humhub\modules\calendar\helpers\CalendarUtils;
use humhub\modules\calendar\helpers\RecurrenceHelper;
use humhub\modules\calendar\models\CalendarEntry;
use humhub\modules\calendar\models\CalendarEntryParticipant;
use humhub\modules\calendar\widgets\EntryParticipants;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\bootstrap\Badge;
use yii\web\View;

/* @var $calendarEntry CalendarEntry */
/* @var $stream boolean */
/* @var $collapse boolean */
/* @var $this View */

$color = $this->theme->variable('text-color-secondary');
?>

<div class="d-flex event calendar-wall-entry" style="margin-top:20px;" data-action-component="calendar.CalendarEntry"
     data-calendar-entry="<?= $calendarEntry->id ?>">
    <div class="event-info-section clearfix" style="margin-bottom:10px">
        <?= Icon::get('file-text')->color($color)->left()->size(Icon::SIZE_LG)->style('margin-top:2px;')->fixedWith() ?>

        <div class="event-info-section-content">
            <h1>
                <?= ($calendarEntry->closed) ? '<s>' : '' ?>


                <?php
                // START - Replacement of `$calendarEntry->getFormattedTime()`

                $formatter = $calendarEntry->formatter;
                if ($calendarEntry->isAllDay()) {
                    echo $formatter->getFormattedStartDate();
                    if ($formatter->getDurationDays() > 1) {
                        echo ' - ' . $formatter->getFormattedEndDate();
                    }
                } else {
                    $result = $formatter->getFormattedStartDate();

                    $startTime = $formatter->getFormattedStartTime();
                    $endTime = $formatter->getFormattedEndTime();
                    $endTime .= ' ' . Badge::light(Yii::$app->formatter->timeZone)->sm();

                    $start = $calendarEntry->getStartDateTime();
                    $end = $calendarEntry->getEndDateTime();
                    $start->setTimezone(CalendarUtils::getUserTimeZone());
                    $end->setTimezone(CalendarUtils::getUserTimeZone());

                    if ($start->format('Y-m-d') !== $end->format('Y-m-d')) {
                        $result .= ', ' . $startTime . ' - ';
                        $result .= $formatter->getFormattedEndDate() . ', ' . $endTime;
                    } else {
                        $result .= ' (' . $startTime . ' - ' . $endTime . ')';
                    }
                    echo $result;
                }

                // END - Replacement of `$calendarEntry->getFormattedTime()`
                ?>


                <?= ($calendarEntry->closed) ? '</s>' : '' ?>
                <?php if (!$calendarEntry->closed && $calendarEntry->isAllDay()) : ?>
                    <small>(<?= Yii::t('CalendarModule.base', 'All Day') ?>)</small>
                <?php endif; ?>
                <?php if (RecurrenceHelper::isRecurrentRoot($calendarEntry)) : ?>
                    <small>(<?= Yii::t('CalendarModule.base', 'Recurring') ?>)</small>
                <?php endif; ?>
                <?php if ($calendarEntry->closed) : ?>
                    &nbsp;<?= Badge::danger(Yii::t('CalendarModule.base', 'canceled')) ?>
                <?php endif; ?>
            </h1>
        </div>

        <?php if (!empty($calendarEntry->description)) : ?>
            <div class="event-info-section-content" data-ui-markdown data-ui-show-more>
                <?= RichText::output($calendarEntry->description) ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($calendarEntry->hasLocation()) : ?>
        <div class="event-info-section clearfix">
            <?= Icon::get('map-marker')->color($color)->left()->size(Icon::SIZE_LG)->style('margin-top:2px;')->fixedWith() ?>
            <div class="event-info-section-content">
                <h1>
                    <?= Yii::t('CalendarModule.base', 'Location') ?>
                </h1>
                <?= $calendarEntry->getLocation(true) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($calendarEntry->participation->isShowParticipationInfo(Yii::$app->user->identity)) : ?>
        <div class="event-info-section clearfix">
            <?= Icon::get('info-circle')->color($color)->left()->size(Icon::SIZE_LG)->style('margin-top:2px;')->fixedWith() ?>
            <div class="event-info-section-content">
                <h1>
                    <?= Yii::t('CalendarModule.views_entry_view', 'Additional information') ?>
                </h1>
                <div data-ui-markdown data-ui-show-more>
                    <?= RichText::output($calendarEntry->participant_info) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (RecurrenceHelper::isRecurrentInstance($calendarEntry)) : ?>

        <?php /* @var $root CalendarEntry */
        $root = $calendarEntry->getRecurrenceQuery()->getRecurrenceRoot() ?>
        <?php if ($root && $root->fileManager->find()->count()) : ?>
            <div class="event-info-section clearfix">
                <?= Icon::get('files-o')->color($color)->left()->size(Icon::SIZE_LG)->style('margin-top:2px;')->fixedWith() ?>
                <div class="event-info-section-content">
                    <h1>
                        <?= Yii::t('CalendarModule.base', 'Files') ?>
                    </h1>
                    <?= FilePreview::widget(['model' => $calendarEntry->getRecurrenceQuery()->getRecurrenceRoot()]) ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!$calendarEntry->closed && $calendarEntry->participation->isEnabled()) : ?>
        <div class="event-info-section clearfix">
            <?= Icon::get('users')->color($color)->left()->size(Icon::SIZE_LG)->style('margin-top:2px;')->fixedWith() ?>
            <div class="event-info-section-content">
                <?= EntryParticipants::widget(['calendarEntry' => $calendarEntry]) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($calendarEntry->participation->canRespond(Yii::$app->user->identity)): ?>
        <div class="event-participation-buttons clearfix">
            <?php if ($calendarEntry->participation->isInvited() && !$calendarEntry->isPast()) : ?>
                <div class="text-warning"><?= Icon::get('warning') . Yii::t('CalendarModule.views_entry_view', 'You are invited, please select your role:') ?></div>
            <?php endif ?>

            <?php if ($calendarEntry->participation->maxParticipantCheck() || $calendarEntry->participation->isParticipant(Yii::$app->user->identity, false)) : ?>
                <?= EntryParticipants::participateButton($calendarEntry, CalendarEntryParticipant::PARTICIPATION_STATE_ACCEPTED, Yii::t('CalendarModule.views_entry_view', "Attend")) ?>
            <?php endif ?>

            <?php if ($calendarEntry->participation->maxParticipantCheck() || $calendarEntry->participation->isParticipant(Yii::$app->user->identity, true)) : ?>
                <?= EntryParticipants::participateButton($calendarEntry, CalendarEntryParticipant::PARTICIPATION_STATE_MAYBE, Yii::t('CalendarModule.views_entry_view', "Maybe")) ?>
            <?php endif ?>

            <?= EntryParticipants::participateButton($calendarEntry, CalendarEntryParticipant::PARTICIPATION_STATE_DECLINED, Yii::t('CalendarModule.views_entry_view', "Decline")) ?>
        </div>
    <?php endif; ?>
</div>
