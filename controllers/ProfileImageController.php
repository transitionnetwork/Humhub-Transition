<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */


namespace humhub\modules\transition\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\user\models\User;
use Yii;

class ProfileImageController extends ContentContainerController
{

    public function getAccessRules()
    {
        return [
            ['login']
        ];
    }

    public function actionUpload()
    {
        if (Yii::$app->user->identity->id != $this->contentContainer->id) {
            $this->goHome();
        }

        /** @var User $user */
        $user = $this->contentContainer;

        $user->settings->set('hasSeenProfileImageUploadPage', true);

        $this->subLayout = null;

        return $this->render('upload', [
            'user' => $user,
        ]);
    }
}
