<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

?>
<footer id="footer" class="mt-auto py-3 bg-body-tertiary">
    <div class="container">
        <div class="row text-body-secondary small">
            <div class="col-md-6 text-center text-md-start">
                &copy; 2013&ndash;<?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <?= Yii::powered() ?>
            </div>
        </div>
    </div>
</footer>
