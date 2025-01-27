<?php
/**
 * https://helpdesk.transition-space.org/conversation/1427?folder_id=23
 */

use humhub\modules\user\models\User;
use yii\web\View;

/* @var $this View */
/* @var $user User */

$originalPeopleCardPath = Yii::$app->getModule('user')->basePath . '/widgets/views/peopleCard.php';

if ($user->getProfileBannerImage()->hasImage()) {
    require $originalPeopleCardPath;
    
} else {
    $cardHeaderBgColors = [
        0 => "#FFB5B5", // Pastel red
        1 => "#FFD4B2", // Pastel orange
        2 => "#FFF4B5", // Pastel yellow
        3 => "#B5E6C5", // Pastel green
        4 => "#B5D4FF", // Pastel blue
        5 => "#E0B5E6", // Pastel purple
        6 => "#FFE1EC", // Lighter pink
        7 => "#FFE1EC", // Lighter pink (duplicate)
        8 => "#E6C9B5", // Pastel brown
        9 => "#D9D9D9", // Light gray instead of black
    ];

    $userCardHeaderBgColorId = (int)substr((string)$user->id, -1, 1); // Last ID digit
    $userCardHeaderBgColor = $cardHeaderBgColors[$userCardHeaderBgColorId];

    ob_start();
    require $originalPeopleCardPath;
    $content = ob_get_clean();

    echo str_replace('class="card-bg-image"', 'class="card-bg-image" style="background-color: ' . $userCardHeaderBgColor . ';"', $content);
}
