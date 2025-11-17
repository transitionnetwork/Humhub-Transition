<?php

use humhub\helpers\Html;
use humhub\modules\user\models\forms\AccountSettings;

require Yii::$app->getModule('user')->viewPath . '/account/editSettings.php';

/* @var AccountSettings $model */
/* @var array $languages */
?>
<script <?= Html::nonce() ?>>
    $(function () {
        $('.field-accountsettings-tags').hide();
    });
</script>
