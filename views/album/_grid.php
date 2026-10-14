<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $owner */
/** @var app\models\Image[] $images */
/** @var bool $selectable */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<ul class="amk-image-grid">
    <?php foreach ($images as $image): ?>
        <li class="amk-image-tile">
            <a href="<?= Html::encode(Url::to(['/image/view', 'username' => $owner->username, 'id' => $image->id])) ?>">
                <?= Html::img($image->thumbUrl, ['alt' => $image->caption ?? 'Kuva', 'loading' => 'lazy']) ?>
            </a>
            <?php if ($selectable): ?>
                <input type="checkbox" class="form-check-input amk-image-select" name="ids[]" value="<?= $image->id ?>"
                       aria-label="Valitse kuva<?= $image->caption === null ? '' : ' ' . Html::encode($image->caption) ?>">
            <?php endif ?>
            <?php if ($image->caption !== null): ?>
                <span class="amk-image-caption"><?= Html::encode($image->caption) ?></span>
            <?php endif ?>
        </li>
    <?php endforeach ?>
</ul>
