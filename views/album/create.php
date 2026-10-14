<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Album $album */

use yii\helpers\Html;

$this->title = 'Uusi albumi';
/** @var app\models\User $user */
$user = Yii::$app->user->identity;
?>
<article class="amk-box album-create">
    <?= $this->render('/user/_subnav', ['user' => $user, 'active' => 'albums']) ?>
    <div class="amk-box-body amk-narrow">
        <h1 class="amk-box-title"><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_form', ['album' => $album]) ?>
    </div>
</article>
