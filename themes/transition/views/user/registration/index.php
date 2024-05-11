<?php
require Yii::$app->getModule('user')->viewPath . '/registration/index.php'; ?>

<?php if (Yii::$app->request->get('token')): // If invited, before returning from the auth app client ?>
    <style>
        #registration-form {
            display: none !important;
        }
    </style>
<?php endif; ?>