<?php
/**
 * https://helpdesk.transition-space.org/conversation/1427?folder_id=23
 */

use humhub\modules\user\models\User;
use yii\web\View;

/* @var $this View */
/* @var $user User */

$cardHeaderBgColors = [
    0 => "#E40303",
    1 => "#FF8C00",
    2 => "#FFED00",
    3 => "#008026",
    4 => "#004CFF",
    5 => "#732982",
    6 => "#f4aec8",
    7 => "#f4aec8",
    8 => "#945516",
    9 => "#000000",
];

$userCardHeaderBgColorId = (int)substr((string)$user->id, -1, 1); // Last ID digit
$userCardHeaderBgColor = $cardHeaderBgColors[$userCardHeaderBgColorId];

ob_start();
require Yii::$app->getModule('user')->basePath . '/widgets/views/peopleCard.php';
$content = ob_get_clean();
?>

<?= str_replace('class="card-bg-image"', 'class="card-bg-image" style="background-color: '.$userCardHeaderBgColor.';"', $content) ?>
