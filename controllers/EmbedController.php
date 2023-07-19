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

class EmbedController extends Controller
{
    public function actionCalendar()
    {
        return $this->render('calendar');
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
        return $this->render('profile', [
            'user' => $user,
        ]);
    }
}
