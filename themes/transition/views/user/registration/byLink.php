<?php
require Yii::$app->getModule('user')->viewPath . '/registration/byLink.php';

// TODO when https://github.com/humhub/humhub/pull/6250 is in the code, do something better to redirect users to the invited space after registration (see https://helpdesk.transition-space.org/conversation/96?folder_id=23)
$inviteSpaceId = Yii::$app->request->get('spaceId');
if ($inviteSpaceId) {
    $inviteSpace = \humhub\modules\space\models\Space::findOne($inviteSpaceId);
    if ($inviteSpace) {
        Yii::$app->user->setReturnUrl($inviteSpace->createUrl());
        if (Yii::$app->session->isActive) {
            Yii::$app->session->set('invite_space_id', $inviteSpaceId);
        }
    }
}
?>

<style>
    #registration-form, .or-container {
        display: none !important;
    }

    .authChoice {
        text-align: center;
    }
</style>