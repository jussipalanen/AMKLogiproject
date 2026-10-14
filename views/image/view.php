<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $owner */
/** @var app\models\Image $image */
/** @var app\models\Image|null $previous */
/** @var app\models\Image|null $next */
/** @var bool $isOwner */
/** @var array<string|int, string> $moveOptions */

use app\assets\AppAsset;
use app\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;

$album = $image->album;
$albumName = $album?->name ?? 'Lajittelemattomat';
$albumUrl = $album === null
    ? ['/album/unsorted', 'username' => $owner->username]
    : ['/album/view', 'username' => $owner->username, 'id' => $album->id];
$this->title = ($image->caption ?? 'Kuva') . ' – ' . $albumName;
$this->registerJsFile('@web/js/albums.js', ['depends' => AppAsset::class]);
$imageUrl = static fn (app\models\Image $other): array => ['/image/view', 'username' => $owner->username, 'id' => $other->id];
?>
<article class="amk-box image-view">
    <?= $this->render('/user/_subnav', ['user' => $owner, 'active' => 'albums']) ?>

    <div class="amk-box-body">
        <p class="small mb-2"><?= Html::a('« ' . Html::encode($albumName), $albumUrl) ?></p>

        <figure class="amk-figure">
            <?= Html::a(
                Html::img($image->url, ['alt' => $image->caption ?? 'Kuva', 'class' => 'amk-figure-img']),
                $image->url,
                ['title' => 'Avaa täysikokoisena'],
            ) ?>
            <?php if ($image->caption !== null): ?>
                <figcaption><?= Html::encode($image->caption) ?></figcaption>
            <?php endif ?>
        </figure>

        <nav class="amk-pager" aria-label="Albumin kuvat">
            <?= $previous === null ? '<span></span>' : Html::a('« Edellinen', $imageUrl($previous), ['rel' => 'prev']) ?>
            <span class="small text-body-secondary"><?= Yii::$app->formatter->asDate($image->created_at) ?></span>
            <?= $next === null ? '<span></span>' : Html::a('Seuraava »', $imageUrl($next), ['rel' => 'next']) ?>
        </nav>

        <?php if ($isOwner): ?>
            <section class="amk-image-tools" aria-label="Kuvan toiminnot">
                <?= Html::beginForm(['/image/caption', 'id' => $image->id], 'post', ['class' => 'amk-inline-form']) ?>
                    <?= Html::hiddenInput('returnUrl', Url::current()) ?>
                    <label class="form-label small mb-0" for="image-caption">Kuvateksti</label>
                    <?= Html::activeTextInput($image, 'caption', [
                        'id' => 'image-caption',
                        'class' => 'form-control form-control-sm',
                        'maxlength' => 255,
                    ]) ?>
                    <?= Html::submitButton('Tallenna', ['class' => 'btn btn-outline-primary btn-sm']) ?>
                <?= Html::endForm() ?>

                <?php if ($moveOptions !== []): ?>
                    <?= Html::beginForm(['/image/move'], 'post', ['class' => 'amk-inline-form']) ?>
                        <?= Html::hiddenInput('id', $image->id) ?>
                        <?= Html::hiddenInput('returnUrl', Url::current()) ?>
                        <label class="form-label small mb-0" for="image-move">Siirrä</label>
                        <?= Html::dropDownList('album_id', null, $moveOptions, [
                            'id' => 'image-move',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                        <?= Html::submitButton('Siirrä', ['class' => 'btn btn-outline-primary btn-sm']) ?>
                    <?= Html::endForm() ?>
                <?php endif ?>

                <div class="d-flex flex-wrap gap-2">
                    <?= Html::beginForm(['/image/avatar', 'id' => $image->id], 'post') ?>
                        <?= Html::hiddenInput('returnUrl', Url::current()) ?>
                        <?= Html::submitButton('Aseta profiilikuvaksi', ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= Html::endForm() ?>

                    <?= Html::beginForm(['/image/delete'], 'post', ['data-confirm-form' => 'Poistetaanko kuva pysyvästi?']) ?>
                        <?= Html::hiddenInput('id', $image->id) ?>
                        <?= Html::hiddenInput('returnUrl', Url::to($albumUrl)) ?>
                        <?= Html::submitButton(Icon::trash() . ' Poista kuva', ['class' => 'btn btn-outline-danger btn-sm']) ?>
                    <?= Html::endForm() ?>
                </div>
            </section>
        <?php endif ?>
    </div>
</article>
