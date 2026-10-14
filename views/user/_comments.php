<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Comment[] $comments */
/** @var int[]|null $newCommentIds comments that were unread when the owner opened the wall */

use app\helpers\Icon;
use yii\helpers\Html;

$newCommentIds ??= [];

?>
<?php if ($comments === []): ?>
    <p class="amk-empty">Ei vielä kommentteja.</p>
<?php else: ?>
    <ul class="comment-list">
        <?php foreach ($comments as $comment): ?>
            <?php $isNew = in_array($comment->id, $newCommentIds, true) ?>
            <li class="comment<?= $comment->is_private ? ' comment-private' : '' ?><?= $isNew ? ' comment-new' : '' ?>">
                <?= Html::a(
                    $this->render('/user/_avatar', ['user' => $comment->author, 'size' => 40]),
                    ['/user/view', 'username' => $comment->author->username],
                    ['tabindex' => -1, 'aria-hidden' => 'true'],
                ) ?>
                <div class="min-w-0">
                    <div class="comment-meta">
                        <?= Html::a(
                            Html::encode($comment->author->username),
                            ['/user/view', 'username' => $comment->author->username],
                        ) ?>
                        &ndash;
                        <time datetime="<?= date(DATE_ATOM, $comment->created_at) ?>">
                            <?= Yii::$app->formatter->asDatetime($comment->created_at) ?>
                        </time>
                        <?php if ($isNew): ?>
                            <span class="amk-new-badge"><?= Icon::comment() ?> Uusi</span>
                        <?php endif ?>
                        <?php if ($comment->is_private): ?>
                            <span class="comment-private-label">(yksityinen)</span>
                        <?php endif ?>
                    </div>
                    <p class="comment-body"><?= nl2br(Html::encode($comment->body)) ?></p>
                </div>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
