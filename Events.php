<?php

/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

namespace humhub\modules\transition;

use humhub\helpers\ControllerHelper;
use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\admin\widgets\UserMenu;
use humhub\modules\content\widgets\WallEntryControls;
use humhub\modules\legal\Module;
use humhub\modules\membersMap\models\MembersMap;
use humhub\modules\reportcontent\widgets\ReportContentLink;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\transition\helpers\MembershipHelper;
use humhub\modules\transition\jobs\SyncAllSpaceHosts;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\WidgetMenuEntry;
use humhub\modules\user\models\Profile;
use humhub\modules\user\models\ProfileField;
use humhub\modules\user\models\User;
use Throwable;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\helpers\BaseInflector;

class Events
{
    /**
     * @param Event $event
     * @throws Throwable
     * @throws InvalidConfigException
     */
    public static function onAdminUserMenuInit($event)
    {
        /** @var UserMenu $menu */
        $menu = $event->sender;

        if (!Yii::$app->user->can(ManageUsers::class)) { // Don't move in 'isVisible' as it doesn't work in all cases and because the "if" costs less
            return;
        }

        if (ProfileField::findOne(['internal_name' => 'region'])) {
            $menu->addEntry(new MenuLink([
                'label' => Yii::t('TransitionModule.config', 'Default spaces'),
                'url' => ['/transition/admin/default-spaces'],
                'sortOrder' => 2000,
                'isActive' => ControllerHelper::isActivePath('transition', 'admin', 'default-spaces'),
                'isVisible' => true,
            ]));
        }

        $menu->addEntry(new MenuLink([
            'label' => Yii::t('TransitionModule.config', 'Conversations'),
            'url' => ['/transition/admin/conversations'],
            'sortOrder' => 2010,
            'isActive' => ControllerHelper::isActivePath('transition', 'admin', 'conversations')
                || ControllerHelper::isActivePath('transition', 'admin', 'conversation-detail'),
            'isVisible' => true,
        ]));
    }


    /**
     * Add user to space depending on the Region field selected on the registration form
     * @param Yii\web\UserEvent $event
     * @return void
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public static function onFormAfterRegistration(yii\web\UserEvent $event)
    {
        // Do not store on console request
        if (Yii::$app->request->isConsoleRequest) {
            return;
        }

        if (!isset($event->identity)) {
            return;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('transition');
        $settings = $module->settings;
        $defaultSpaces = (array)$settings->getSerialized('defaultSpaces');

        // Get user from event because Yii::$app->user->id doesnt work here
        $user = User::findOne(['id' => $event->identity->id]);

        if (!$user->profile->hasAttribute('region')) {
            return;
        }

        $defaultSpaceIds = $defaultSpaces[BaseInflector::slug($user->profile->region)] ?? null;
        if (!$defaultSpaceIds) {
            return;
        }

        foreach (Space::findAll($defaultSpaceIds) as $space) {
            $space->addMember($user->id);
        }
    }

    /**
     * @param $event
     */
    public static function onModelSpaceMembershipMemberRemoved($event)
    {
        if (!isset($event)) {
            return;
        }

        /** @var Membership $membership */
        $membership = $event; // not $event->sender as it is executed by queue/run
        $user = $membership->user;

        MembershipHelper::updateUserTagsAndMembershipToSpaceHostsGroup($user);
    }

    /**
     * @param $event
     */
    public static function onModelSpaceMembershipMemberAdded($event)
    {
        if (!isset($event)) {
            return;
        }

        /** @var Membership $membership */
        $membership = $event; // not $event->sender as it is executed by queue/run
        $user = $membership->user;

        MembershipHelper::updateUserTagsAndMembershipToSpaceHostsGroup($user);
    }

    /**
     * @param $event
     */
    public static function onModelSpaceMembershipUpdate($event)
    {
        if (
            !isset($event->sender, $event->changedAttributes)
            || !array_key_exists('group_id', $event->changedAttributes)
        ) {
            return;
        }

        /** @var Membership $membership */
        $membership = $event->sender;
        $user = $membership->user;

        MembershipHelper::updateUserTagsAndMembershipToSpaceHostsGroup($user);
    }

    /**
     * @param $event
     * @return void
     */
    public static function onModelSpaceBeforeDelete($event)
    {
        if (empty($event->sender)) {
            return;
        }

        /** @var Space $space */
        $space = $event->sender;

        Yii::$app->queue->push(new SyncAllSpaceHosts([
            'tagFieldToRemove' => $space->name,
        ]));
    }

    /**
     * @param $event
     * @return void
     */
    public static function onModelSpaceAfterUpdate($event)
    {
        if (
            !isset($event->sender, $event->changedAttributes)
            || !array_key_exists('name', $event->changedAttributes)
        ) {
            return;
        }

        /** @var Space $space */
        $space = $event->sender;

        Yii::$app->queue->push(new SyncAllSpaceHosts([
            'tagFieldToRemove' => $space->name,
        ]));
    }

    /**
     * Content sub-menu: Move "Report" entry to the top
     * https://helpdesk.transition-space.org/conversation/1661?folder_id=23
     */
    public static function onWallEntryControlsBeforeRun($event)
    {
        /** @var WallEntryControls $menu */
        $menu = $event->sender;

        $reportContentEntry = null;
        foreach ($menu->getEntries() as $entry) {
            if (!$entry instanceof WidgetMenuEntry) {
                continue;
            }
            if ($entry->getEntryClass() === ReportContentLink::class) {
                $reportContentEntry = $entry;
            }
        }

        if ($reportContentEntry) {
            $menu->removeEntry($reportContentEntry);
            $reportContentEntry->setSortOrder(0);
            $menu->addEntry($reportContentEntry);
        }
    }

    /**
     * Sync the user profile lat/lng field after a MembersMap record is inserted or updated.
     *
     * @param Event $event
     * @return void
     */
    public static function onMembersMapAfterSave(Event $event): void
    {
        /** @var MembersMap $membersMap */
        $membersMap = $event->sender;
        static::syncLatLngProfileField($membersMap->user_id);
    }

    /**
     * Sync the user profile lat/lng field after a MembersMap record is deleted.
     * Re-queries the remaining records so the best available coordinates are used.
     *
     * @param Event $event
     * @return void
     */
    public static function onMembersMapAfterDelete(Event $event): void
    {
        /** @var MembersMap $membersMap */
        $membersMap = $event->sender;
        static::syncLatLngProfileField($membersMap->user_id);
    }

    /**
     * Find the best lat/lng coordinates for the given user across all their MembersMap records
     * and persist the value directly to the profile table (bypassing ActiveRecord events to
     * avoid re-triggering the members-map geocoding pipeline).
     *
     * Priority: lat_user / lng_user  →  lat_zip_city / lng_zip_city
     *
     * @param int $userId
     * @return void
     */
    private static function syncLatLngProfileField(int $userId): void
    {
        /** @var \humhub\modules\transition\Module $module */
        $module = Yii::$app->getModule('transition');
        if ($module === null) {
            return;
        }

        $fieldName = $module->profileFieldLatLngInternalName;
        if (empty($fieldName)) {
            return;
        }

        // Bail out early if the profile column does not exist yet
        if (!Profile::columnExists($fieldName)) {
            return;
        }

        // Prefer a record that already has a user-supplied location
        $record = MembersMap::find()
            ->where(['user_id' => $userId])
            ->andWhere(['IS NOT', 'lat_user', null])
            ->andWhere(['IS NOT', 'lng_user', null])
            ->one();

        // Fall back to the geocoded city/zip coordinates
        if ($record === null) {
            $record = MembersMap::find()
                ->where(['user_id' => $userId])
                ->andWhere(['IS NOT', 'lat_zip_city', null])
                ->andWhere(['IS NOT', 'lng_zip_city', null])
                ->one();
        }

        if ($record !== null) {
            if ($record->lat_user !== null && $record->lng_user !== null) {
                $latLng = $record->lat_user . ',' . $record->lng_user;
            } else {
                $latLng = $record->lat_zip_city . ',' . $record->lng_zip_city;
            }
        } else {
            $latLng = null;
        }

        // Write directly to the DB to avoid firing Profile::EVENT_AFTER_UPDATE
        // which would trigger the members-map geocoding pipeline again.
        Yii::$app->db->createCommand()
            ->update(Profile::tableName(), [$fieldName => $latLng], ['user_id' => $userId])
            ->execute();
    }
}
