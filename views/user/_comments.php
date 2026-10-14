<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Comment[] $comments */

use yii\helpers\Html;

?>
<?php if ($comments === []): ?>
    <p class="text-body-secondary mb-0">Ei vielä kommentteja.</p>
<?php else: ?>
    <ul class="list-unstyled comment-list mb-0">
        <?php foreach ($comments as $comment): ?>
            <li class="comment d-flex gap-3<?= $comment->is_private ? ' comment-private' : '' ?>">
                <?= Html::a(
                    $this->render('/user/_avatar', ['user' => $comment->author, 'size' => 40]),
                    ['/user/view', 'username' => $comment->author->username],
                    ['class' => 'flex-shrink-0'],
                ) ?>
                <div class="flex-grow-1 min-w-0">
                    <div class="small">
                        <?= Html::a(
                            Html::encode($comment->author->username),
                            ['/user/view', 'username' => $comment->author->username],
                            ['class' => 'fw-semibold'],
                        ) ?>
                        <time class="text-body-secondary ms-1" datetime="<?= date(DATE_ATOM, $comment->created_at) ?>">
                            <?= Yii::$app->formatter->asDatetime($comment->created_at) ?>
                        </time>
                        <?php if ($comment->is_private): ?>
                            <span class="badge text-bg-secondary ms-1">Yksityinen</span>
                        <?php endif ?>
                    </div>
                    <p class="mb-0 comment-body"><?= nl2br(Html::encode($comment->body)) ?></p>
                </div>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
