<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $owner the wall owner */
/** @var app\models\Comment[] $comments */
/** @var int[]|null $newCommentIds comments that were unread when the owner opened the wall */

use app\helpers\Icon;
use app\models\User;
use yii\helpers\Html;

$newCommentIds ??= [];
/** @var User|null $viewer */
$viewer = Yii::$app->user->identity;
// The owner (and admins) get checkboxes and bulk delete; authors can delete their own comments.
$canManage = $owner->canManageWall($viewer);
// Delete buttons belong to the #comment-delete-form in user/view.php (HTML form attribute),
// so this list can be re-rendered after AJAX requests without re-creating the form.
$deleteForm = 'comment-delete-form';
?>
<?php if ($comments === []): ?>
    <p class="amk-empty">Ei vielä kommentteja.</p>
<?php else: ?>
    <?php if ($canManage): ?>
        <div class="amk-comment-tools">
            <label class="amk-select-all">
                <input type="checkbox" class="form-check-input" data-select-all>
                Valitse kaikki
            </label>
            <button type="submit" form="<?= $deleteForm ?>" class="btn btn-outline-danger btn-sm" data-delete-selected>
                <?= Icon::trash() ?> Poista valitut <span data-selected-count></span>
            </button>
            <button type="submit" form="<?= $deleteForm ?>" name="all" value="1" class="btn btn-outline-danger btn-sm"
                    data-confirm-message="<?= Html::encode(Yii::$app->i18n->format(
                        '{n, plural, =1{Poistetaanko seinäsi ainoa kommentti?} other{Poistetaanko kaikki # kommenttia seinältäsi?}}',
                        ['n' => count($comments)],
                        'fi-FI',
                    )) ?>">
                Poista kaikki
            </button>
        </div>
    <?php endif ?>

    <ul class="comment-list">
        <?php foreach ($comments as $comment): ?>
            <?php $isNew = in_array($comment->id, $newCommentIds, true) ?>
            <li id="comment-<?= $comment->id ?>"
                class="comment<?= $comment->is_private ? ' comment-private' : '' ?><?= $isNew ? ' comment-new' : '' ?>">
                <?php if ($canManage): ?>
                    <input type="checkbox" class="form-check-input amk-comment-select" name="ids[]"
                           value="<?= $comment->id ?>" form="<?= $deleteForm ?>"
                           aria-label="Valitse kommentti (<?= Html::encode($comment->author->username) ?>)">
                <?php endif ?>
                <?= Html::a(
                    $this->render('/user/_avatar', ['user' => $comment->author, 'size' => 40]),
                    ['/user/view', 'username' => $comment->author->username],
                    ['tabindex' => -1, 'aria-hidden' => 'true'],
                ) ?>
                <div class="min-w-0 flex-grow-1">
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
                <?php if ($comment->canBeDeletedBy($viewer)): ?>
                    <button type="submit" form="<?= $deleteForm ?>" name="id" value="<?= $comment->id ?>"
                            class="amk-delete-btn" title="Poista kommentti" aria-label="Poista kommentti"
                            data-confirm-message="Poistetaanko kommentti?">
                        <?= Icon::trash() ?>
                    </button>
                <?php endif ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
