<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $owner */
/** @var app\models\Album[] $albums */
/** @var bool $isOwner */
/** @var int $unsortedCount */

use app\helpers\Icon;
use app\models\Album;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Albumit – ' . $owner->username;
?>
<article class="amk-box album-index">
    <?= $this->render('/user/_subnav', ['user' => $owner, 'active' => 'albums']) ?>

    <div class="amk-box-body">
        <h1 class="amk-box-title">Albumit</h1>

        <?php if ($isOwner): ?>
            <p class="d-flex flex-wrap gap-2">
                <?= Html::a('Lataa kuvia', ['/image/upload'], ['class' => 'btn btn-primary btn-sm']) ?>
                <?= Html::a('Uusi albumi', ['/album/create'], ['class' => 'btn btn-outline-primary btn-sm']) ?>
            </p>
        <?php endif ?>

        <?php if ($albums === [] && $unsortedCount === 0): ?>
            <p class="amk-empty">
                <?= $isOwner ? 'Sinulla ei ole vielä albumeja.' : 'Käyttäjällä ei ole albumeja.' ?>
            </p>
        <?php else: ?>
            <div class="amk-album-grid">
                <?php foreach ($albums as $album): ?>
                    <?php $cover = $album->getCoverImage() ?>
                    <a class="amk-album-card" href="<?= Html::encode(Url::to(['/album/view', 'username' => $owner->username, 'id' => $album->id])) ?>">
                        <span class="amk-album-cover">
                            <?php if ($cover !== null): ?>
                                <?= Html::img($cover->thumbUrl, ['alt' => '', 'loading' => 'lazy']) ?>
                            <?php else: ?>
                                <?= Icon::images() ?>
                            <?php endif ?>
                        </span>
                        <strong><?= Html::encode($album->name) ?></strong>
                        <span class="amk-album-meta">
                            <?= Yii::$app->i18n->format('{n, plural, =0{Ei kuvia} =1{1 kuva} other{# kuvaa}}', ['n' => (int) $album->imageCount], 'fi-FI') ?>
                            <?php if ($album->is_private): ?>
                                · <span class="amk-private-tag"><?= Icon::lock() ?> Yksityinen</span>
                            <?php endif ?>
                        </span>
                    </a>
                <?php endforeach ?>

                <?php if ($unsortedCount > 0): ?>
                    <a class="amk-album-card amk-album-unsorted" href="<?= Html::encode(Url::to(['/album/unsorted', 'username' => $owner->username])) ?>">
                        <span class="amk-album-cover"><?= Icon::images() ?></span>
                        <strong>Lajittelemattomat</strong>
                        <span class="amk-album-meta">
                            <?= Yii::$app->i18n->format('{n, plural, =1{1 kuva} other{# kuvaa}}', ['n' => $unsortedCount], 'fi-FI') ?>
                            · <span class="amk-private-tag"><?= Icon::lock() ?> Vain sinulle</span>
                        </span>
                    </a>
                <?php endif ?>
            </div>
        <?php endif ?>

        <?php if ($isOwner): ?>
            <p class="text-body-secondary small mt-3 mb-0">
                Albumiin mahtuu enintään <?= Album::maxImages() ?> kuvaa.
            </p>
        <?php endif ?>
    </div>
</article>
