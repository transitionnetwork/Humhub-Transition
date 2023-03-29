<?php
require Yii::$app->getModule('user')->viewPath . '/auth/login.php';
?>

<style>
    #login-form {
        max-width: 500px !important;
    }

    .btn-ac-Keycloak {
        white-space: normal !important;
    }

    #register-form, .or-container, #account-login-form {
        display: none !important;
    }

    .authChoice {
        text-align: center;
    }
</style>