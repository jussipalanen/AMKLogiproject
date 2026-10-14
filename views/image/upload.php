<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\ImageUploadForm $model */

use app\assets\AppAsset;
use app\models\Album;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Lataa kuvia';
$this->registerJsFile('@web/js/albums.js', ['depends' => AppAsset::class]);
$params = Yii::$app->params;
/** @var app\models\User $user */
$user = Yii::$app->user->identity;
?>
<article class="amk-box image-upload">
    <?= $this->render('/user/_subnav', ['user' => $user, 'active' => 'albums']) ?>
    <div class="amk-box-body">
        <h1 class="amk-box-title"><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin([
            'id' => 'upload-form',
            'options' => [
                'enctype' => 'multipart/form-data',
                'data-upload-form' => true,
                'data-max-files' => $params['uploadMaxFiles'],
                'data-max-size-mb' => $params['uploadMaxFileSizeMb'],
            ],
            // albums.js checks the files before sending; the server validates again.
            'enableClientScript' => false,
        ]) ?>
            <?= $form->field($model, 'album_id')->dropDownList($model->albumOptions())
                ->hint(Html::a('Luo uusi albumi', ['/album/create'])) ?>

            <?= $form->field($model, 'files[]')
                ->fileInput(['multiple' => true, 'accept' => 'image/png,image/jpeg,image/gif,image/webp'])
                ->label('Kuvat')
                ->hint(sprintf(
                    'Valitse yksi tai useampi kuva (enintään %d kerralla, %d Mt / kuva). Albumiin mahtuu %d kuvaa.',
                    $params['uploadMaxFiles'],
                    $params['uploadMaxFileSizeMb'],
                    Album::maxImages(),
                )) ?>

            <ul class="amk-upload-list small" data-upload-list hidden></ul>

            <?= Html::submitButton('Lataa', ['class' => 'btn btn-primary', 'data-upload-submit' => true]) ?>
            <span class="small text-body-secondary ms-2" data-upload-status role="status"></span>
        <?php ActiveForm::end() ?>
    </div>
</article>
