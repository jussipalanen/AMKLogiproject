<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Comment;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Posting comments on a user's wall. Works as a normal form post and, from
 * web/js/comments.js, as an AJAX request that returns the refreshed list.
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
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException if the wall owner does not exist
     */
    public function actionCreate(string $username): Response|array
    {
        $recipient = User::findByUsername($username)
            ?? throw new NotFoundHttpException('Käyttäjää ei löytynyt.');
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

            return [
                'html' => $this->renderPartial('/user/_comments', [
                    'comments' => UserController::wallComments($recipient, $author),
                ]),
            ];
        }

        if ($saved) {
            Yii::$app->session->setFlash('success', 'Kommentti lähetetty.');
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $comment->getFirstErrors()));
        }

        return $this->redirect(['user/view', 'username' => $recipient->username, '#' => 'comments']);
    }
}
