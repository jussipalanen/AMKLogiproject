<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $owner */
/** @var app\models\Album|null $album null for the unsorted images */
/** @var app\models\Image[] $images */
/** @var bool $isOwner */
/** @var array<string|int, string> $moveOptions */

use app\assets\AppAsset;
use app\helpers\Icon;
use app\models\Album;
use yii\helpers\Html;
use yii\helpers\Url;

$name = $album?->name ?? 'Lajittelemattomat';
$this->title = $name . ' – ' . $owner->username;
$this->registerJsFile('@web/js/albums.js', ['depends' => AppAsset::class]);
$returnUrl = Url::current();
?>
<article class="amk-box album-view">
    <?= $this->render('/user/_subnav', ['user' => $owner, 'active' => 'albums']) ?>

    <div class="amk-box-body">
        <p class="small mb-2"><?= Html::a('« Kaikki albumit', ['/album/index', 'username' => $owner->username]) ?></p>
        <h1 class="amk-box-title">
            <?= Html::encode($name) ?>
            <?php if ($album === null || $album->is_private): ?>
                <span class="amk-private-tag fs-6"><?= Icon::lock() ?> <?= $album === null ? 'Vain sinulle' : 'Yksityinen' ?></span>
            <?php endif ?>
        </h1>

        <?php if ($album?->description !== null): ?>
            <p><?= nl2br(Html::encode($album->description)) ?></p>
        <?php elseif ($album === null): ?>
            <p class="text-body-secondary">Kuvat, joita ei ole vielä siirretty mihinkään albumiin. Näkyvät vain sinulle.</p>
        <?php endif ?>

        <?php if ($isOwner): ?>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?= Html::a('Lataa kuvia tähän', ['/image/upload', 'album' => $album?->id], ['class' => 'btn btn-primary btn-sm']) ?>
                <?php if ($album !== null): ?>
                    <?= Html::a('Muokkaa albumia', ['/album/update', 'id' => $album->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                    <?= Html::beginForm(['/album/delete', 'id' => $album->id], 'post', [
                        'class' => 'd-inline',
                        'data-confirm-form' => 'Poistetaanko albumi? Sen kuvat siirretään lajittelemattomiin.',
                    ]) ?>
                        <?= Html::submitButton('Poista albumi', ['class' => 'btn btn-outline-danger btn-sm']) ?>
                    <?= Html::endForm() ?>
                <?php endif ?>
                <span class="small text-body-secondary align-self-center">
                    <?= count($images) ?> / <?= Album::maxImages() ?> kuvaa
                </span>
            </div>
        <?php endif ?>

        <?php if ($images === []): ?>
            <p class="amk-empty">Ei kuvia.</p>
        <?php elseif ($isOwner): ?>
            <?= Html::beginForm(['/image/move'], 'post', ['id' => 'image-bulk-form', 'data-bulk-form' => true]) ?>
                <?= Html::hiddenInput('returnUrl', $returnUrl) ?>
                <div class="amk-comment-tools">
                    <label class="amk-select-all">
                        <input type="checkbox" class="form-check-input" data-select-all>
                        Valitse kaikki <span data-selected-count></span>
                    </label>
                    <?php if ($moveOptions !== []): ?>
                        <label class="visually-hidden" for="move-target">Siirrä kohteeseen</label>
                        <?= Html::dropDownList('album_id', null, $moveOptions, [
                            'id' => 'move-target',
                            'class' => 'form-select form-select-sm w-auto',
                        ]) ?>
                        <button type="submit" class="btn btn-outline-primary btn-sm" data-needs-selection>Siirrä valitut</button>
                    <?php endif ?>
                    <button type="submit" formaction="<?= Html::encode(Url::to(['/image/delete'])) ?>"
                            class="btn btn-outline-danger btn-sm" data-needs-selection
                            data-confirm-message="Poistetaanko valitut kuvat pysyvästi?">
                        <?= Icon::trash() ?> Poista valitut
                    </button>
                </div>
                <?= $this->render('_grid', ['owner' => $owner, 'images' => $images, 'selectable' => true]) ?>
            <?= Html::endForm() ?>
        <?php else: ?>
            <?= $this->render('_grid', ['owner' => $owner, 'images' => $images, 'selectable' => false]) ?>
        <?php endif ?>
    </div>
</article>
