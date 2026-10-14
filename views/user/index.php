<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = 'Selaa käyttäjiä';
?>
<article class="amk-box user-index">
    <div class="amk-box-body">
        <h1 class="amk-box-title"><?= Html::encode($this->title) ?></h1>

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'amk-user-grid'],
            'itemOptions' => ['tag' => false],
            'layout' => "{items}\n<div style=\"grid-column: 1 / -1\">{pager}</div>",
            'emptyText' => 'Ei vielä käyttäjiä.',
            'emptyTextOptions' => ['class' => 'amk-empty'],
            'pager' => ['class' => \yii\bootstrap5\LinkPager::class],
            'itemView' => static fn (app\models\User $user): string => Html::a(
                Yii::$app->view->render('/user/_avatar', ['user' => $user, 'size' => 48])
                    . Html::tag(
                        'span',
                        Html::tag('strong', Html::encode($user->username))
                            . Html::encode($user->profile?->fullName ?? ''),
                        ['class' => 'min-w-0 text-truncate'],
                    ),
                ['/user/view', 'username' => $user->username],
                ['class' => 'amk-user-card'],
            ),
        ]) ?>
    </div>
</article>
