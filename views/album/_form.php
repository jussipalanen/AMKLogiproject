<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Album $album */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin(['id' => 'album-form']) ?>
    <?= $form->field($album, 'name')->textInput(['maxlength' => 80, 'autofocus' => true]) ?>
    <?= $form->field($album, 'description')->textarea(['rows' => 3, 'maxlength' => 500]) ?>
    <?= $form->field($album, 'is_private')->checkbox()
        ->hint('Yksityisen albumin ja sen kuvat näet vain sinä.') ?>
    <?= Html::submitButton($album->isNewRecord ? 'Luo albumi' : 'Tallenna', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>
