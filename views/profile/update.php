<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Profile $profile */
/** @var app\models\AvatarForm $avatarForm */

use app\models\Profile;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$user = $profile->user;

$this->title = 'Muokkaa profiilia';
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['/user/view', 'username' => $user->username]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-update row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h2 class="h5 mb-3">Profiilikuva</h2>
                <div class="mb-3">
                    <?= $this->render('/user/_avatar', ['user' => $user, 'size' => 160]) ?>
                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'avatar-form',
                    'action' => ['/profile/avatar'],
                    'options' => ['enctype' => 'multipart/form-data'],
                ]) ?>
                    <?= $form->field($avatarForm, 'avatar')
                        ->fileInput(['accept' => 'image/png,image/jpeg,image/gif,image/webp'])
                        ->label(false)
                        ->hint('JPG, PNG, GIF tai WebP, enintään 5 Mt.') ?>
                    <?= Html::submitButton('Lataa kuva', ['class' => 'btn btn-outline-primary']) ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3"><?= Html::encode($this->title) ?></h1>
                <p class="text-body-secondary small">Kaikki kentät ovat vapaaehtoisia ja näkyvät profiilisivullasi.</p>

                <?php $form = ActiveForm::begin(['id' => 'profile-form']) ?>
                    <div class="row">
                        <div class="col-sm-6"><?= $form->field($profile, 'firstname') ?></div>
                        <div class="col-sm-6"><?= $form->field($profile, 'surname') ?></div>
                        <div class="col-sm-6">
                            <?= $form->field($profile, 'gender')->dropDownList(Profile::GENDERS, ['prompt' => '–']) ?>
                        </div>
                        <div class="col-sm-6"><?= $form->field($profile, 'birthday')->input('date') ?></div>
                        <div class="col-sm-6"><?= $form->field($profile, 'city') ?></div>
                        <div class="col-sm-6"><?= $form->field($profile, 'relationship') ?></div>
                        <div class="col-sm-6"><?= $form->field($profile, 'email')->input('email') ?></div>
                        <div class="col-sm-6"><?= $form->field($profile, 'skype') ?></div>
                        <div class="col-12">
                            <?= $form->field($profile, 'homepage')->input('url', ['placeholder' => 'https://']) ?>
                        </div>
                    </div>
                    <?= Html::submitButton('Tallenna', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Näytä profiili', ['/user/view', 'username' => $user->username], ['class' => 'btn btn-link']) ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
