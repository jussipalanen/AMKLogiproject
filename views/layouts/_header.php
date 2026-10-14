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

// Menu labels follow the original AMKLog menu (Etusivu, Oma, Selaa, ...).
$items = [
    [
        // New comments on your wall, shown first in red like "(1)" in the original menu.
        'label' => '(' . $unread . ')',
        'url' => ['/user/view', 'username' => $identity?->username, '#' => 'comments'],
        'visible' => $unread > 0,
        'linkOptions' => [
            'class' => 'nav-link amk-unread',
            'title' => Yii::$app->i18n->format(
                '{n, plural, =1{# uusi kommentti} other{# uutta kommenttia}}',
                ['n' => $unread],
                'fi-FI',
            ),
        ],
    ],
    [
        'label' => 'Etusivu',
        'url' => ['/site/index'],
    ],
    [
        'label' => 'Oma',
        'url' => ['/user/view', 'username' => $identity?->username],
        'visible' => $identity !== null,
    ],
    [
        'label' => 'Selaa',
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
        'label' => 'Asetukset',
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
    <?php NavBar::begin([
        'brandLabel' => false,
        'options' => ['class' => 'navbar-expand-md amk-topbar', 'aria-label' => 'Päävalikko'],
        'innerContainerOptions' => ['class' => 'amk-container'],
        'togglerOptions' => ['class' => 'navbar-toggler amk-toggler'],
        'screenReaderToggleText' => 'Näytä valikko',
    ]) ?>
    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'encodeLabels' => false,
        'items' => $items,
    ]) ?>
    <?php NavBar::end() ?>
</header>
