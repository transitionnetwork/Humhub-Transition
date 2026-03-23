<?php

/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

namespace humhub\modules\transition\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\content\components\ContentContainerModuleManager;
use humhub\modules\space\models\Space;
use humhub\modules\mail\helpers\Url as MailUrl;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\transition\jobs\SyncAllSpaceHosts;
use humhub\modules\transition\Module;
use humhub\modules\user\models\fieldtype\Select;
use humhub\modules\user\models\ProfileField;
use humhub\modules\user\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseInflector;
use yii\web\HttpException;

class AdminController extends Controller
{
    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            ['permission' => ManageUsers::class],
        ];
    }

    /**
     * @return string
     */
    public function actionDefaultSpaces()
    {
        ContentContainerModuleManager::setDefaultState(User::class, 'transition', 1); //TODO: to remove
        $title = Yii::t('TransitionModule.config', 'Default spaces');
        $this->subLayout = '@admin/views/layouts/user';
        $this->appendPageTitle($title);

        // Get `region` field list
        $profileField = ProfileField::findOne(['internal_name' => 'region']);
        if ($profileField === null) {
            throw new HttpException(403, 'Error, the profile field "region" does not exists!');
        }
        if (!$profileField->fieldType instanceof Select) {
            throw new HttpException(403, 'Error, the profile field "region" is not a "select" type field!');
        }
        $select = $profileField->fieldType;
        $regionItems = $select->getSelectItems();

        // Get settings
        /** @var Module $module */
        $module = Yii::$app->getModule('transition');
        $settings = $module->settings;

        if ($post = Yii::$app->request->post()) {
            $defaultSpaceIds = [];
            foreach ($regionItems as $regionKey => $regionLabel) {
                $regionKey = BaseInflector::slug($regionKey);
                if (!array_key_exists('space-' . $regionKey, $post)) {
                    continue;
                }
                // Convert Space Guids to IDs
                $defaultSpaceIds[$regionKey] = Space::find()->where(['guid' => $post['space-' . $regionKey]])->select('id')->column();
            }
            $settings->setSerialized('defaultSpaces', $defaultSpaceIds);
            $this->view->saved();
        }

        // Get default spaces
        $defaultSpaces = [];
        foreach ((array)$settings->getSerialized('defaultSpaces') as $region => $regionDefaultSpaceIds) {
            $defaultSpaces[$region] = Space::find()->where(['id' => $regionDefaultSpaceIds])->all();
        }

        return $this->render('default-spaces', [
            'title' => $title,
            'regionItems' => $regionItems,
            'defaultSpaces' => $defaultSpaces,
        ]);
    }

    /**
     * Lists all mail conversations with pagination.
     * URL: /transition/admin/conversations
     */
    public function actionConversations()
    {
        $title = Yii::t('TransitionModule.config', 'Conversations');
        $this->subLayout = '@admin/views/layouts/user';
        $this->appendPageTitle($title);

        $dataProvider = new ActiveDataProvider([
            'query' => Message::find()
                ->with(['originator', 'users'])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('conversations', [
            'title' => $title,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Returns modal content for a single conversation.
     * URL: /transition/admin/conversation-detail?id=X
     */
    public function actionConversationDetail(int $id)
    {
        $message = Message::findOne($id);
        if ($message === null) {
            throw new HttpException(404, 'Conversation not found.');
        }

        $entries = MessageEntry::find()
            ->where(['message_id' => $id])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        $url = MailUrl::toMessenger($message, true);

        return $this->renderAjax('conversation-detail', [
            'message' => $message,
            'entries' => $entries,
            'url' => $url,
        ]);
    }

    /**
     * Hidden action
     * URL: /transition/admin/sync-all-space-admins
     */
    public function actionSyncAllSpaceAdmins()
    {
        Yii::$app->queue->push(new SyncAllSpaceHosts());
        return 'Space admins sync added to cron jobs!';
    }
}
