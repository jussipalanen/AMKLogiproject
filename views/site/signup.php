<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Rekisteröidy';
?>
<article class="amk-box site-signup amk-narrow">
    <div class="amk-box-body">
        <h1 class="amk-box-title"><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['id' => 'signup-form']) ?>
            <?= $form->field($model, 'username')
                ->textInput(['autofocus' => true, 'autocomplete' => 'username', 'maxlength' => 30])
                ->hint('3–30 merkkiä: kirjaimet a–z, numerot, _ ja -. Profiilisi osoite on /tunnus.') ?>
            <?= $form->field($model, 'email')->input('email', ['autocomplete' => 'email']) ?>
            <?= $form->field($model, 'password')
                ->passwordInput(['autocomplete' => 'new-password'])
                ->hint('Vähintään 8 merkkiä.') ?>
            <?= $form->field($model, 'password_repeat')->passwordInput(['autocomplete' => 'new-password']) ?>
            <?= Html::submitButton('Rekisteröidy', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            <?= Html::resetButton('Tyhjennä', ['class' => 'btn btn-outline-primary ms-1']) ?>
        <?php ActiveForm::end() ?>

        <p class="mt-3 mb-0">Onko sinulla jo tunnus? <?= Html::a('Kirjaudu sisään', ['/site/login']) ?></p>
    </div>
</article>
