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
?>
<?php $this->beginBlock('sidebar') ?>
    <section class="amk-box" aria-labelledby="avatar-title">
        <div class="amk-box-body">
            <h2 class="amk-box-title" id="avatar-title">Profiilikuva</h2>
            <div class="mb-3">
                <?= $this->render('/user/_avatar', ['user' => $user, 'size' => 120]) ?>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'avatar-form',
                'action' => ['/profile/avatar'],
                'options' => ['enctype' => 'multipart/form-data'],
            ]) ?>
                <?= $form->field($avatarForm, 'avatar')
                    ->fileInput(['accept' => 'image/png,image/jpeg,image/gif,image/webp', 'class' => 'form-control form-control-sm'])
                    ->label(false)
                    ->hint('JPG, PNG, GIF tai WebP, enintään 5 Mt.') ?>
                <?= Html::submitButton('Lataa kuva', ['class' => 'btn btn-primary btn-sm']) ?>
            <?php ActiveForm::end() ?>
        </div>
    </section>

    <section class="amk-box" aria-labelledby="settings-actions-title">
        <div class="amk-box-body">
            <h2 class="amk-box-title" id="settings-actions-title">Toiminnot</h2>
            <ul class="amk-actions">
                <li><?= Html::a('Näytä oma profiili', ['/user/view', 'username' => $user->username]) ?></li>
            </ul>
        </div>
    </section>
<?php $this->endBlock() ?>

<article class="amk-box profile-update">
    <nav aria-label="Profiilin valikko">
        <ul class="amk-subnav">
            <li><?= Html::a('Profiili', ['/user/view', 'username' => $user->username]) ?></li>
            <li><?= Html::a('Asetukset', ['/profile/update'], ['aria-current' => 'page']) ?></li>
        </ul>
    </nav>
    <div class="amk-box-body">
        <h1 class="amk-box-title"><?= Html::encode($this->title) ?></h1>
        <p class="text-body-secondary">Kaikki kentät ovat vapaaehtoisia ja näkyvät profiilisivullasi.</p>

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
        <?php ActiveForm::end() ?>
    </div>
</article>
