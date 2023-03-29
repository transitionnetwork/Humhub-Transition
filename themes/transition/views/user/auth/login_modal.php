<?php
require Yii::$app->getModule('user')->viewPath . '/auth/login_modal.php';
?>

<style>
    #tabs, .or-container, form[action="/user/auth/login"] {
        display: none !important;
    }

    .authChoice {
        text-align: center;
    }
</style>