<?php

use humhub\modules\user\models\forms\AccountSettings;

require Yii::$app->getModule('user')->viewPath . '/account/editSettings.php';

/* @var AccountSettings $model */
/* @var array $languages */
?>
<script>
    $(function () {
        $('.form-group.field-accountsettings-tags').hide();
    });
</script>