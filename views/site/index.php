<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User[] $newestUsers */

use yii\helpers\Html;

$this->title = Yii::$app->name;
$this->params['meta_description'] = 'AMKLogi – sosiaalinen mediapalvelu ammattikorkeakoulun opiskelijoille.';
/** @var app\models\User|null $identity */
$identity = Yii::$app->user->identity;
?>
<div class="site-index">
    <section class="hero p-4 p-lg-5 mb-4 rounded-4">
        <h1 class="display-5 fw-bold">Tervetuloa AMKLogiin</h1>
        <p class="lead col-lg-8">
            Sosiaalinen mediapalvelu ammattikorkeakoululle: luo profiili, lisää profiilikuva
            ja kirjoita kommentteja muiden opiskelijoiden seinille &ndash; julkisesti tai yksityisesti.
        </p>
        <?php if ($identity === null): ?>
            <?= Html::a('Rekisteröidy', ['/site/signup'], ['class' => 'btn btn-primary btn-lg me-2']) ?>
            <?= Html::a('Kirjaudu sisään', ['/site/login'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
        <?php else: ?>
            <?= Html::a('Siirry omalle sivullesi', ['/user/view', 'username' => $identity->username], ['class' => 'btn btn-primary btn-lg']) ?>
        <?php endif ?>
    </section>

    <section aria-labelledby="newest-title">
        <div class="d-flex align-items-baseline justify-content-between mb-3">
            <h2 class="h4 mb-0" id="newest-title">Uusimmat käyttäjät</h2>
            <?= Html::a('Kaikki käyttäjät &rarr;', ['/user/index']) ?>
        </div>
        <?php if ($newestUsers === []): ?>
            <p class="text-body-secondary">Ei vielä käyttäjiä &ndash; ole ensimmäinen!</p>
        <?php else: ?>
            <div class="d-flex flex-wrap gap-3">
                <?php foreach ($newestUsers as $user): ?>
                    <?= Html::a(
                        $this->render('/user/_avatar', ['user' => $user, 'size' => 64])
                            . Html::tag('span', Html::encode($user->username), ['class' => 'small d-block mt-1 text-truncate']),
                        ['/user/view', 'username' => $user->username],
                        ['class' => 'newest-user text-center text-decoration-none'],
                    ) ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>
</div>
