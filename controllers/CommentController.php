<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Comment;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Posting and deleting comments on a user's wall. Both work as normal form posts and,
 * from web/js/comments.js, as AJAX requests that return the refreshed list.
 */
class CommentController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException if the wall owner does not exist
     */
    public function actionCreate(string $username): Response|array
    {
        $recipient = $this->findWallOwner($username);
        /** @var User $author */
        $author = Yii::$app->user->identity;

        $comment = new Comment();
        $comment->load($this->request->post());
        // Never taken from the request: the author is whoever is logged in.
        $comment->author_id = $author->id;
        $comment->recipient_id = $recipient->id;

        if ($author->id === $recipient->id) {
            $comment->read_at = time();
        }

        $saved = $comment->save();

        if ($this->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if (!$saved) {
                Yii::$app->response->statusCode = 422;

                return ['errors' => $comment->getFirstErrors()];
            }

            return ['html' => $this->renderWall($recipient)];
        }

        if ($saved) {
            Yii::$app->session->setFlash('success', 'Kommentti lähetetty.');
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $comment->getFirstErrors()));
        }

        return $this->redirect(['user/view', 'username' => $recipient->username, '#' => 'comments']);
    }

    /**
     * Deletes comments from a wall: one with `id` (a comment's own delete button), the
     * selected ones with `ids[]`, or all of them with `all=1`.
     *
     * @throws NotFoundHttpException if the wall owner does not exist
     * @throws ForbiddenHttpException if the user may not delete the requested comments
     */
    public function actionDelete(string $username): Response|array
    {
        $owner = $this->findWallOwner($username);
        /** @var User $viewer */
        $viewer = Yii::$app->user->identity;
        $query = Comment::find()->where(['recipient_id' => $owner->id]);

        if ($this->request->post('all')) {
            if (!$owner->canManageWall($viewer)) {
                throw new ForbiddenHttpException('Vain seinän omistaja voi poistaa kaikki kommentit.');
            }
        } elseif ($this->request->post('id') !== null) {
            $query->andWhere(['id' => (int) $this->request->post('id')]);
        } else {
            $ids = array_map('intval', (array) $this->request->post('ids', []));
            // Only comments on this wall, whatever ids were sent.
            $query->andWhere(['id' => $ids === [] ? [0] : $ids]);
        }

        $comments = $query->all();

        // All or nothing: check every comment before deleting any of them.
        foreach ($comments as $comment) {
            if (!$comment->canBeDeletedBy($viewer)) {
                throw new ForbiddenHttpException('Sinulla ei ole oikeutta poistaa tätä kommenttia.');
            }
        }

        $deleted = $comments === []
            ? 0
            : Comment::deleteAll(['id' => array_map(static fn (Comment $comment): int => $comment->id, $comments)]);

        $message = $deleted === 0
            ? 'Valitse poistettavat kommentit.'
            : Yii::$app->i18n->format(
                '{n, plural, =1{Kommentti poistettu.} other{# kommenttia poistettu.}}',
                ['n' => $deleted],
                'fi-FI',
            );

        if ($this->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ['html' => $this->renderWall($owner), 'deleted' => $deleted, 'message' => $message];
        }

        Yii::$app->session->setFlash($deleted === 0 ? 'warning' : 'success', $message);

        return $this->redirect(['user/view', 'username' => $owner->username, '#' => 'comments']);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findWallOwner(string $username): User
    {
        return User::findByUsername($username)
            ?? throw new NotFoundHttpException('Käyttäjää ei löytynyt.');
    }

    private function renderWall(User $owner): string
    {
        /** @var User $viewer */
        $viewer = Yii::$app->user->identity;

        return $this->renderPartial('/user/_comments', [
            'owner' => $owner,
            'comments' => UserController::wallComments($owner, $viewer),
        ]);
    }
}
