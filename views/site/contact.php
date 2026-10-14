<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\ContactForm $model */

use app\models\ContactForm;
use yii\bootstrap5\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

$this->title = 'Ota yhteyttä';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-2"><?= Html::encode($this->title) ?></h1>
                <p class="text-body-secondary">
                    Kysyttävää tai palautetta? Täytä alla oleva lomake, niin vastaamme mahdollisimman pian.
                </p>

                <?php if (YII_DEBUG && Yii::$app->mailer->useFileTransport): ?>
                    <p class="small text-body-tertiary">
                        Kehitystila: viestit tallennetaan hakemistoon
                        <code><?= Html::encode(Yii::getAlias(Yii::$app->mailer->fileTransportPath)) ?></code>.
                    </p>
                <?php endif ?>

                <?php $form = ActiveForm::begin(['id' => 'contact-form']) ?>
                    <div class="row">
                        <div class="col-sm-6"><?= $form->field($model, 'name')->textInput(['autocomplete' => 'name']) ?></div>
                        <div class="col-sm-6"><?= $form->field($model, 'email')->input('email', ['autocomplete' => 'email']) ?></div>
                    </div>
                    <?= $form->field($model, 'subject') ?>
                    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>
                    <?php if (ContactForm::captchaEnabled()): ?>
                        <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                            'template' => '<div class="d-flex align-items-center gap-2">{image}{input}</div>',
                        ])->hint('Kirjoita kuvassa näkyvät kirjaimet.') ?>
                    <?php endif ?>
                    <?= Html::submitButton('Lähetä', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
