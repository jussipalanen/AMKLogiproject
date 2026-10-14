<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use app\widgets\Alert;
use yii\helpers\Html;

$this->render('_head');
// Views can fill a left column with `$this->beginBlock('sidebar')`, as in the original layout.
$sidebar = $this->blocks['sidebar'] ?? null;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" data-bs-theme="dark">
<head>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title === Yii::$app->name ? $this->title : $this->title . ' – ' . Yii::$app->name) ?></title>
</head>
<body>
<?php $this->beginBody() ?>

<?= $this->render('_header') ?>

<div class="amk-page">
    <header class="amk-banner">
        <?= Html::a(Html::encode(Yii::$app->name), Yii::$app->homeUrl, ['class' => 'amk-banner-title']) ?>
        <p class="amk-banner-subtitle">Sosiaaliverkostoitumista ammattikorkeakouluille</p>
        <span class="amk-banner-version">(Versio 2.0)</span>
    </header>

    <?= Alert::widget(['options' => ['class' => 'amk-alert']]) ?>

    <?php if ($sidebar !== null): ?>
        <div class="amk-columns">
            <aside class="amk-sidebar"><?= $sidebar ?></aside>
            <main id="main" class="amk-main"><?= $content ?></main>
        </div>
    <?php else: ?>
        <main id="main" class="amk-main"><?= $content ?></main>
    <?php endif ?>

    <?= $this->render('_footer') ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
