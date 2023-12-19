<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */


namespace humhub\modules\transition\controllers;

use humhub\components\Controller;
use humhub\modules\notification\models\forms\FilterForm;
use humhub\modules\notification\models\Notification;
use humhub\modules\notification\widgets\OverviewWidget;
use humhub\modules\user\models\User;
use Yii;
use yii\db\IntegrityException;
use yii\web\ForbiddenHttpException;

class EmbedController extends Controller
{
    const PAGINATION_PAGE_SIZE = 1000;

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
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
        return $this->renderPartial('mail-conversation-sidebar');
    }

    public function actionNotificationOverview()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }

        $filterForm = new FilterForm();
        $filterForm->load(Yii::$app->request->get());

        return $this->renderPartial('notification-overview', [
            'overview' => OverviewWidget::widget([
                'pagination' => $filterForm->getPagination(self::PAGINATION_PAGE_SIZE),
                'notifications' => $this->prepareNotifications($filterForm->createQuery()->all()),
            ]),
        ]);
    }


    /**
     * Validates given notifications and returns a list of notification models of all valid notifications.
     *
     * @param $notifications Notification[]
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function prepareNotifications($notifications)
    {
        $result = [];
        foreach ($notifications as $notificationRecord) {
            /* @var $notificationRecord \humhub\modules\notification\models\Notification */

            try {
                $baseModel = $notificationRecord->getBaseModel();

                if ($baseModel->validate()) {
                    $result[] = $baseModel;
                } else {
                    throw new IntegrityException('Invalid base model (' . $notificationRecord->class . ') found for notification');
                }

            } catch (IntegrityException $ex) {
                $notificationRecord->delete();
                Yii::warning('Deleted inconsistent notification with id ' . $notificationRecord->id . '. ' . $ex->getMessage());
            }
        }
        return $result;
    }
}
