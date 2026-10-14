<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Kirjaudu sisään';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-3"><?= Html::encode($this->title) ?></h1>

                <?php $form = ActiveForm::begin(['id' => 'login-form']) ?>
                    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'autocomplete' => 'username']) ?>
                    <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'current-password']) ?>
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                    <div class="d-grid">
                        <?= Html::submitButton('Kirjaudu', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    </div>
                <?php ActiveForm::end() ?>

                <p class="small text-body-secondary text-center mt-3 mb-0">
                    Eikö sinulla ole tunnusta? <?= Html::a('Rekisteröidy', ['/site/signup']) ?>
                </p>
            </div>
        </div>
    </div>
</div>
