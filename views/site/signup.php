<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Rekisteröidy';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-3"><?= Html::encode($this->title) ?></h1>

                <?php $form = ActiveForm::begin(['id' => 'signup-form']) ?>
                    <?= $form->field($model, 'username')
                        ->textInput(['autofocus' => true, 'autocomplete' => 'username', 'maxlength' => 30])
                        ->hint('3–30 merkkiä: kirjaimet a–z, numerot, _ ja -. Profiilisi osoite on /tunnus.') ?>
                    <?= $form->field($model, 'email')->input('email', ['autocomplete' => 'email']) ?>
                    <?= $form->field($model, 'password')
                        ->passwordInput(['autocomplete' => 'new-password'])
                        ->hint('Vähintään 8 merkkiä.') ?>
                    <?= $form->field($model, 'password_repeat')->passwordInput(['autocomplete' => 'new-password']) ?>
                    <div class="d-grid">
                        <?= Html::submitButton('Rekisteröidy', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                    </div>
                <?php ActiveForm::end() ?>

                <p class="small text-body-secondary text-center mt-3 mb-0">
                    Onko sinulla jo tunnus? <?= Html::a('Kirjaudu sisään', ['/site/login']) ?>
                </p>
            </div>
        </div>
    </div>
</div>
