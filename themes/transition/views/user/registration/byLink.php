<?php
require Yii::$app->getModule('user')->viewPath . '/registration/byLink.php';

// TODO when https://github.com/humhub/humhub/pull/6250 is in the code, do something better to redirect users to the invited space after registration (see https://helpdesk.transition-space.org/conversation/96?folder_id=23)
$spaceId = Yii::$app->request->get('spaceId');
if ($spaceId) {
    $space = \humhub\modules\space\models\Space::findOne($spaceId);
    if ($space) {
        Yii::$app->user->setReturnUrl($space->createUrl());
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