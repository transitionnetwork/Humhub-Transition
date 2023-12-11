<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */


namespace humhub\modules\transition\controllers;

use humhub\components\Controller;
use humhub\modules\user\models\User;
use Yii;

class EmbedController extends Controller
{
    public function actionCalendar()
    {
        return $this->renderPartial('calendar');
    }

    public function actionProfile($username = null, $email = null)
    {
        $user = null;
        if ($username) {
            $user = User::findOne(['username' => $username]);
        }
        if ($email) {
            $user = User::findOne(['email' => $email]);
        }

        if ($user) {
            Yii::$app->user->setReturnUrl($user->createUrl(null, [], true));
        }

        return $this->renderPartial('profile', [
            'user' => $user,
        ]);
    }

    public function actionMailConversationSidebar()
    {
        return $this->renderPartial('mail-conversation-sidebar');
    }
}
