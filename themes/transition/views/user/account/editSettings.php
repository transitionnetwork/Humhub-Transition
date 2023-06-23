<?php

use humhub\libs\Html;
use humhub\modules\user\models\forms\AccountSettings;

require Yii::$app->getModule('user')->viewPath . '/account/editSettings.php';

/* @var AccountSettings $model */
/* @var array $languages */
?>
<script <?= Html::nonce() ?>>
    $(function () {
        $('.form-group.field-accountsettings-tags').hide();
    });
</script>