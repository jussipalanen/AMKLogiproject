<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\Comment[] $comments */
/** @var app\models\Comment $newComment */

use app\assets\AppAsset;
use app\models\Comment;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = $user->username;
$this->params['breadcrumbs'][] = ['label' => 'Käyttäjät', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/comments.js', ['depends' => AppAsset::class]);

$profile = $user->profile;
$isOwner = !Yii::$app->user->isGuest && Yii::$app->user->id === $user->id;

$details = array_filter([
    'Nimi' => $profile->fullName !== '' ? Html::encode($profile->fullName) : null,
    'Sukupuoli' => $profile->genderLabel,
    'Syntymäpäivä' => $profile->birthday === null ? null : Yii::$app->formatter->asDate($profile->birthday),
    'Paikkakunta' => $profile->city === null ? null : Html::encode($profile->city),
    'Parisuhde' => $profile->relationship === null ? null : Html::encode($profile->relationship),
    'Sähköposti' => $profile->email === null ? null : Html::mailto(Html::encode($profile->email), $profile->email),
    'Kotisivu' => $profile->homepage === null
        ? null
        : Html::a(Html::encode($profile->homepage), $profile->homepage, ['rel' => 'nofollow noopener', 'target' => '_blank']),
    'Skype' => $profile->skype === null ? null : Html::encode($profile->skype),
], static fn (?string $value): bool => $value !== null);
?>
<div class="user-view row g-4">
    <aside class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <?= $this->render('_avatar', ['user' => $user, 'size' => 160]) ?>
                </div>
                <h1 class="h3 mb-1"><?= Html::encode($user->username) ?></h1>
                <p class="text-body-secondary small mb-0">
                    Jäsen <?= Yii::$app->formatter->asDate($user->created_at) ?> alkaen
                </p>
                <?php if ($isOwner): ?>
                    <?= Html::a('Muokkaa profiilia', ['/profile/update'], ['class' => 'btn btn-outline-primary btn-sm mt-3']) ?>
                <?php endif ?>
            </div>
            <?php if ($details !== []): ?>
                <dl class="list-group list-group-flush mb-0 profile-details">
                    <?php foreach ($details as $label => $value): ?>
                        <div class="list-group-item">
                            <dt class="small text-body-secondary fw-normal"><?= $label ?></dt>
                            <dd class="mb-0 text-break"><?= $value ?></dd>
                        </div>
                    <?php endforeach ?>
                </dl>
            <?php elseif ($isOwner): ?>
                <div class="card-footer small text-body-secondary">
                    Profiilisi on vielä tyhjä. <?= Html::a('Lisää tietoja', ['/profile/update']) ?>.
                </div>
            <?php endif ?>
        </div>
    </aside>

    <section class="col-lg-8" id="comments" aria-labelledby="comments-title">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3" id="comments-title">Kommentit</h2>

                <div data-comment-list>
                    <?= $this->render('_comments', ['comments' => $comments]) ?>
                </div>
            </div>
            <div class="card-footer">
                <?php if (Yii::$app->user->isGuest): ?>
                    <p class="mb-0 small">
                        <?= Html::a('Kirjaudu sisään', ['/site/login']) ?> kommentoidaksesi.
                    </p>
                <?php else: ?>
                    <?php $form = ActiveForm::begin([
                        'id' => 'comment-form',
                        'action' => ['/comment/create', 'username' => $user->username],
                        'options' => ['data-comment-form' => true],
                        // web/js/comments.js handles submit; yii.activeForm would submit the page itself.
                        'enableClientValidation' => false,
                    ]) ?>
                        <?= $form->field($newComment, 'body')
                            ->textarea([
                                'rows' => 2,
                                'maxlength' => Comment::MAX_LENGTH,
                                'placeholder' => 'Kirjoita kommentti…',
                            ])
                            ->label(false) ?>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <?= Html::submitButton('Kommentoi', ['class' => 'btn btn-primary']) ?>
                            <?= $form->field($newComment, 'is_private', ['options' => ['class' => 'mb-0']])
                                ->checkbox() ?>
                            <span class="small text-body-secondary" data-comment-status role="status" hidden></span>
                        </div>
                    <?php ActiveForm::end() ?>
                <?php endif ?>
            </div>
        </div>
    </section>
</div>
