<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var string $active one of: profile, albums, settings */

use yii\helpers\Html;

$isOwner = !Yii::$app->user->isGuest && Yii::$app->user->id === $user->id;
$items = [
    'profile' => ['Profiili', ['/user/view', 'username' => $user->username]],
    'albums' => ['Albumit', ['/album/index', 'username' => $user->username]],
    'comments' => ['Kommentit', ['/user/view', 'username' => $user->username, '#' => 'comments']],
];

if ($isOwner) {
    $items['settings'] = ['Asetukset', ['/profile/update']];
}
?>
<nav aria-label="Profiilin valikko">
    <ul class="amk-subnav">
        <?php foreach ($items as $key => [$label, $url]): ?>
            <li><?= Html::a($label, $url, $key === $active ? ['aria-current' => 'page'] : []) ?></li>
        <?php endforeach ?>
    </ul>
</nav>
