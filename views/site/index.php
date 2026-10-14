<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User[] $newestUsers */

use app\helpers\Icon;
use app\models\LoginForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::$app->name;
$this->params['meta_description'] = 'AMKLogi – sosiaalinen mediapalvelu ammattikorkeakoulun opiskelijoille ja opettajille.';
/** @var app\models\User|null $identity */
$identity = Yii::$app->user->identity;
?>
<?php $this->beginBlock('sidebar') ?>
    <section class="amk-box" aria-labelledby="login-box-title">
        <div class="amk-box-body">
            <h2 class="amk-box-title" id="login-box-title">Kirjautuminen</h2>
            <?php if ($identity === null): ?>
                <?php $form = ActiveForm::begin(['id' => 'sidebar-login-form', 'action' => ['/site/login']]) ?>
                    <?= $form->field(new LoginForm(), 'username')->textInput(['autocomplete' => 'username']) ?>
                    <?= $form->field(new LoginForm(), 'password')->passwordInput(['autocomplete' => 'current-password']) ?>
                    <?= Html::submitButton('Kirjaudu', ['class' => 'btn btn-primary btn-sm']) ?>
                <?php ActiveForm::end() ?>
                <p class="mt-3 mb-0">Ei vielä tunnusta? <?= Html::a('Rekisteröidy', ['/site/signup']) ?></p>
            <?php else: ?>
                <?php $unread = $identity->getUnreadCommentCount() ?>
                <p class="mb-2">
                    Hei, <?= Html::a(Html::encode($identity->username), ['/user/view', 'username' => $identity->username]) ?>!
                </p>
                <p class="mb-2 amk-unread-summary">
                    <?php if ($unread > 0): ?>
                        <?= Icon::comment() ?> Sinulla on
                        <?= Html::a(
                            Yii::$app->i18n->format(
                                '{n, plural, =1{# uusi kommentti} other{# uutta kommenttia}}',
                                ['n' => $unread],
                                'fi-FI',
                            ),
                            ['/user/view', 'username' => $identity->username, '#' => 'comments'],
                            ['class' => 'amk-unread'],
                        ) ?>.
                    <?php else: ?>
                        Ei uusia kommentteja.
                    <?php endif ?>
                </p>
                <p class="mb-3">Mitä haluat tehdä?</p>
                <ul class="amk-actions">
                    <li><?= Html::a('Lisää kuva', ['/profile/update']) ?></li>
                    <li><?= Html::a('Muokkaa profiilia', ['/profile/update']) ?></li>
                    <li><?= Html::a('Kirjaudu ulos', ['/site/logout'], ['data-method' => 'post']) ?></li>
                </ul>
            <?php endif ?>
        </div>
    </section>

    <section class="amk-box" aria-labelledby="newest-title">
        <div class="amk-box-body">
            <h2 class="amk-box-title" id="newest-title">Uusimmat käyttäjät</h2>
            <?php if ($newestUsers === []): ?>
                <p class="amk-empty">Ei vielä käyttäjiä.</p>
            <?php else: ?>
                <ol class="amk-list">
                    <?php foreach (array_slice($newestUsers, 0, 5) as $user): ?>
                        <li><?= Html::a(Html::encode($user->username), ['/user/view', 'username' => $user->username]) ?></li>
                    <?php endforeach ?>
                </ol>
            <?php endif ?>
        </div>
    </section>
<?php $this->endBlock() ?>

<article class="amk-box site-index">
    <div class="amk-box-body">
        <h1 class="amk-box-title">Tervetuloa AMKLogiin!</h1>
        <p>
            AMKLogi on ammattikorkeakoululaisille tarkoitettu sosiaalinen mediapalvelu opiskelijoille ja
            opettajille. Palvelussa voi luoda itselleen ilmaiseksi tunnuksen ja henkilökohtaisen profiilin.
            Profiiliin voi lisätä oman kuvan ja tietoja itsestään, ja muut käyttäjät voivat kirjoittaa
            profiilin kommenttiseinälle &ndash; julkisesti tai yksityisesti.
        </p>

        <h2>Mitäs muuta?</h2>
        <p>
            Uusista kommenteista näet ilmoituksen valikossa. Selaa-sivulta löydät kaikki palvelun käyttäjät,
            ja yksityiset kommentit näkyvät vain sinulle ja kommentin kirjoittajalle.
        </p>
        <p>
            Ylläpito toivottaa kaikki uudet käyttäjät tervetulleeksi palveluun. Rekisteröidy ilmaiseksi,
            kommentoi ja tutustu uusiin opiskelijoihin ja opettajiin!
        </p>

        <?php if ($newestUsers !== []): ?>
            <h2>Uusimmat jäsenet</h2>
            <div class="amk-newest">
                <?php foreach ($newestUsers as $user): ?>
                    <?= Html::a(
                        $this->render('/user/_avatar', ['user' => $user, 'size' => 64]),
                        ['/user/view', 'username' => $user->username],
                        ['title' => $user->username],
                    ) ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</article>
