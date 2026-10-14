<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use app\models\User;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

/** @var User|null $identity */
$identity = Yii::$app->user->identity;
$unread = $identity?->getUnreadCommentCount() ?? 0;

$items = [
    [
        'label' => 'Etusivu',
        'url' => ['/site/index'],
    ],
    [
        'label' => 'Oma sivu'
            . ($unread > 0
                ? ' ' . Html::tag('span', $unread, [
                    'class' => 'badge rounded-pill text-bg-danger',
                    'title' => 'Uusia kommentteja',
                ])
                : ''),
        'url' => ['/user/view', 'username' => $identity?->username],
        'visible' => $identity !== null,
    ],
    [
        'label' => 'Käyttäjät',
        'url' => ['/user/index'],
    ],
    [
        'label' => 'Tietoa',
        'url' => ['/site/about'],
    ],
    [
        'label' => 'Ota yhteyttä',
        'url' => ['/site/contact'],
    ],
];

$accountItems = [
    [
        'label' => 'Kirjaudu sisään',
        'url' => ['/site/login'],
        'visible' => $identity === null,
    ],
    [
        'label' => 'Rekisteröidy',
        'url' => ['/site/signup'],
        'visible' => $identity === null,
    ],
    [
        'label' => 'Muokkaa profiilia',
        'url' => ['/profile/update'],
        'visible' => $identity !== null,
    ],
    [
        'label' => 'Kirjaudu ulos (' . Html::encode($identity?->username ?? '') . ')',
        'url' => ['/site/logout'],
        'linkOptions' => [
            'data-method' => 'post',
            'class' => 'nav-link logout',
        ],
        'visible' => $identity !== null,
    ],
];

?>
<header id="header">
    <?php NavBar::begin(
        [
            'brandLabel' => Html::encode(Yii::$app->name),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top'],
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false,
            'items' => $items,
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav'],
            'encodeLabels' => false,
            'items' => $accountItems,
        ],
    ) ?>
    <?= Html::button(
        '&#127769;',
        [
            'id' => 'theme-toggle',
            'class' => 'btn btn-link nav-link fs-5',
            'aria-label' => 'Vaihda tummaan teemaan',
        ],
    ) ?>
    <?php NavBar::end() ?>
</header>
