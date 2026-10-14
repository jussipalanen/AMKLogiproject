<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\Comment[] $comments */
/** @var int[] $newCommentIds */
/** @var app\models\Comment $newComment */

use app\assets\AppAsset;
use app\models\Comment;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = $user->username;
$this->registerJsFile('@web/js/comments.js', ['depends' => AppAsset::class]);

$profile = $user->profile;
$isOwner = !Yii::$app->user->isGuest && Yii::$app->user->id === $user->id;

// Labels as in the original "Tiedot" box.
$details = array_filter([
    'Etunimi' => $profile->firstname === null ? null : Html::encode($profile->firstname),
    'Sukunimi' => $profile->surname === null ? null : Html::encode($profile->surname),
    'Sukupuoli' => $profile->genderLabel,
    'Syntymäaika' => $profile->birthday === null ? null : Yii::$app->formatter->asDate($profile->birthday),
    'Sähköposti' => $profile->email === null ? null : Html::mailto(Html::encode($profile->email), $profile->email),
    'Kaupunki' => $profile->city === null ? null : Html::encode($profile->city),
    'Parisuhdetilanne' => $profile->relationship === null ? null : Html::encode($profile->relationship),
    'Kotisivu' => $profile->homepage === null
        ? null
        : Html::a(Html::encode($profile->homepage), $profile->homepage, ['rel' => 'nofollow noopener', 'target' => '_blank']),
    'Skype' => $profile->skype === null ? null : Html::encode($profile->skype),
], static fn (?string $value): bool => $value !== null);
?>
<?php $this->beginBlock('sidebar') ?>
    <section class="amk-box" aria-labelledby="details-title">
        <div class="amk-box-body">
            <h2 class="amk-box-title" id="details-title">Tiedot</h2>
            <?php if ($details === []): ?>
                <p class="amk-empty">Käyttäjä ei ole lisännyt tietoja.</p>
            <?php else: ?>
                <dl class="amk-facts profile-details">
                    <?php foreach ($details as $label => $value): ?>
                        <div><dt><?= $label ?></dt><dd><?= $value ?></dd></div>
                    <?php endforeach ?>
                </dl>
            <?php endif ?>
            <p class="amk-facts text-body-secondary mt-2 mb-0">
                Jäsen <?= Yii::$app->formatter->asDate($user->created_at) ?> alkaen
            </p>
        </div>
    </section>

    <?php if ($isOwner): ?>
        <section class="amk-box" aria-labelledby="actions-title">
            <div class="amk-box-body">
                <h2 class="amk-box-title" id="actions-title">Toiminnot</h2>
                <ul class="amk-actions">
                    <li><?= Html::a('Lataa profiilikuva', ['/profile/update', '#' => 'avatar-form']) ?></li>
                    <li><?= Html::a('Lataa kuvia albumiin', ['/image/upload']) ?></li>
                    <li><?= Html::a('Omat albumit', ['/album/index', 'username' => $user->username]) ?></li>
                    <li><?= Html::a('Muokkaa profiilia', ['/profile/update']) ?></li>
                </ul>
            </div>
        </section>
    <?php endif ?>
<?php $this->endBlock() ?>

<article class="amk-box user-view">
    <?= $this->render('_subnav', ['user' => $user, 'active' => 'profile']) ?>

    <div class="amk-box-body">
        <h1 class="amk-box-title"><?= Html::encode($user->username) ?></h1>

        <?= Html::img($profile->avatarUrl ?? '@web/images/avatar-placeholder.svg', [
            'alt' => $profile->avatarUrl === null ? 'Ei profiilikuvaa' : 'Käyttäjän ' . $user->username . ' profiilikuva',
            'class' => 'amk-portrait',
        ]) ?>

        <section id="comments" aria-labelledby="comments-title">
            <h2 id="comments-title">Kommentit</h2>

            <?php if (!Yii::$app->user->isGuest): ?>
                <?= Html::beginForm(
                    ['/comment/delete', 'username' => $user->username],
                    'post',
                    ['id' => 'comment-delete-form', 'data-comment-delete-form' => true],
                ) ?>
                <?= Html::endForm() ?>
                <p class="small amk-delete-status" data-delete-status role="status" hidden></p>
            <?php endif ?>

            <div data-comment-list>
                <?= $this->render('_comments', [
                    'owner' => $user,
                    'comments' => $comments,
                    'newCommentIds' => $newCommentIds,
                ]) ?>
            </div>

            <div class="amk-comment-form">
                <?php if (Yii::$app->user->isGuest): ?>
                    <p class="mb-0">
                        <?= Html::a('Kirjaudu sisään', ['/site/login']) ?> kommentoidaksesi.
                    </p>
                <?php else: ?>
                    <?php $form = ActiveForm::begin([
                        'id' => 'comment-form',
                        'action' => ['/comment/create', 'username' => $user->username],
                        'options' => ['data-comment-form' => true],
                        // web/js/comments.js sends the form. Without this, yii.activeForm's own submit
                        // handler also posts the (by then cleared) form and reloads the page.
                        'enableClientScript' => false,
                    ]) ?>
                        <?= $form->field($newComment, 'body')
                            ->textarea([
                                'rows' => 2,
                                'maxlength' => Comment::MAX_LENGTH,
                                'placeholder' => 'Kirjoita kommentti…',
                                'aria-describedby' => 'comment-hint',
                            ])
                            ->label(false)
                            ->hint('Enter lähettää, Shift + Enter tekee rivinvaihdon.', ['id' => 'comment-hint']) ?>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <?= Html::submitButton('Kommentoi', ['class' => 'btn btn-primary']) ?>
                            <?= $form->field($newComment, 'is_private', ['options' => ['class' => 'mb-0']])
                                ->checkbox() ?>
                            <span class="small text-body-secondary" data-comment-status role="status" hidden></span>
                        </div>
                    <?php ActiveForm::end() ?>
                <?php endif ?>
            </div>
        </section>
    </div>
</article>
