<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var int $size */

use yii\helpers\Html;

// Square thumbnail; users without a picture get the grey silhouette like in the original app.
echo Html::img($user->profile?->avatarUrl ?? '@web/images/avatar-placeholder.svg', [
    'alt' => $user->username,
    'class' => 'amk-avatar',
    'width' => $size,
    'height' => $size,
    'loading' => 'lazy',
]);
