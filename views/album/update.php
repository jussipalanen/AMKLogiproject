<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Album $album */

use yii\helpers\Html;

$this->title = 'Muokkaa albumia';
/** @var app\models\User $user */
$user = Yii::$app->user->identity;
?>
<article class="amk-box album-update">
    <?= $this->render('/user/_subnav', ['user' => $user, 'active' => 'albums']) ?>
    <div class="amk-box-body amk-narrow">
        <p class="small mb-2">
            <?= Html::a('« ' . Html::encode($album->name), ['/album/view', 'username' => $user->username, 'id' => $album->id]) ?>
        </p>
        <h1 class="amk-box-title"><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_form', ['album' => $album]) ?>
    </div>
</article>
