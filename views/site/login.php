<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Kirjaudu sisään';
?>
<article class="amk-box site-login amk-narrow">
    <div class="amk-box-body">
        <h1 class="amk-box-title"><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['id' => 'login-form']) ?>
            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'autocomplete' => 'username']) ?>
            <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'current-password']) ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
            <?= Html::submitButton('Kirjaudu', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        <?php ActiveForm::end() ?>

        <p class="mt-3 mb-0">Eikö sinulla ole tunnusta? <?= Html::a('Rekisteröidy', ['/site/signup']) ?></p>
    </div>
</article>
