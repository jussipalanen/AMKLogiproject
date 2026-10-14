<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var int $size */

use yii\helpers\Html;

$url = $user->profile?->avatarUrl;
$style = "width: {$size}px; height: {$size}px;";

if ($url !== null) {
    echo Html::img($url, [
        'alt' => $user->username,
        'class' => 'avatar rounded-circle object-fit-cover',
        'style' => $style,
        'loading' => 'lazy',
    ]);
} else {
    echo Html::tag('span', Html::encode(mb_strtoupper(mb_substr($user->username, 0, 1))), [
        'class' => 'avatar avatar-placeholder rounded-circle',
        'style' => $style . ' font-size: ' . round($size * 0.42) . 'px;',
        'aria-hidden' => 'true',
    ]);
}
