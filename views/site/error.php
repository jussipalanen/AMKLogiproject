<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;
use yii\web\HttpException;

$this->title = $name;
$statusCode = $exception instanceof HttpException ? $exception->statusCode : 500;
?>
<article class="amk-box site-error">
    <div class="amk-box-body">
        <h1 class="amk-box-title">Virhe <?= Html::encode($statusCode) ?></h1>
        <p class="fs-6"><?= Html::encode($message) ?></p>
        <p>Pyynnön käsittelyssä tapahtui virhe. Jos uskot vian olevan palvelimessa, ota meihin yhteyttä.</p>
        <?= Html::a('Etusivulle', Yii::$app->homeUrl, ['class' => 'btn btn-primary']) ?>
    </div>
</article>
