<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = 'Käyttäjät';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <h1 class="h2 mb-4"><?= Html::encode($this->title) ?></h1>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3'],
        'itemOptions' => ['class' => 'col'],
        'layout' => "{items}\n<div class=\"col-12\">{pager}</div>",
        'emptyText' => 'Ei vielä käyttäjiä.',
        'emptyTextOptions' => ['class' => 'col-12 text-body-secondary'],
        'pager' => ['class' => \yii\bootstrap5\LinkPager::class],
        'itemView' => static fn (app\models\User $user): string => Html::a(
            Yii::$app->view->render('/user/_avatar', ['user' => $user, 'size' => 48])
                . Html::tag(
                    'span',
                    Html::tag('span', Html::encode($user->username), ['class' => 'fw-semibold d-block'])
                        . Html::tag('span', Html::encode($user->profile?->fullName ?? ''), ['class' => 'small text-body-secondary']),
                    ['class' => 'min-w-0 text-truncate'],
                ),
            ['/user/view', 'username' => $user->username],
            ['class' => 'card card-body d-flex flex-row align-items-center gap-3 text-decoration-none user-card shadow-sm'],
        ),
    ]) ?>
</div>
