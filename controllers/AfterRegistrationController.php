<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */


namespace humhub\modules\transition\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\user\models\forms\AccountSettings;
use humhub\modules\user\models\User;
use Yii;

class AfterRegistrationController extends ContentContainerController
{

    public function getAccessRules()
    {
        return [
            ['login']
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->identity->id != $this->contentContainer->id) {
            $this->goHome();
        }

        $this->subLayout = null;

        /** @var User $user */
        $user = $this->contentContainer;

        $user->settings->set('hasSeenAfterRegistrationPage', true);

        $model = new AccountSettings();
        $model->language = Yii::$app->i18n->getAllowedLanguage($user->language);
        $model->timeZone = $user->time_zone;
        if (empty($model->timeZone)) {
            $model->timeZone = Yii::$app->settings->get('defaultTimeZone');
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->scenario = User::SCENARIO_EDIT_ACCOUNT_SETTINGS;
            $user->language = $model->language;
            $user->time_zone = $model->timeZone;
            $user->save();

            $this->view->saved();
            return $this->redirect(Yii::$app->user->getReturnUrl() ?: ['/spaces']);
        }

        // Sort countries list based on user language
        $languages = Yii::$app->i18n->getAllowedLanguages();
        $col = new \Collator(Yii::$app->language);
        $col->asort($languages);

        return $this->render('index', [
            'user' => $user,
            'model' => $model,
            'languages' => $languages,
        ]);
    }
}
