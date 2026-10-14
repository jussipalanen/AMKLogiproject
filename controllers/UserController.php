<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Comment;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Member list and profile pages (/<username>).
 */
class UserController extends Controller
{
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->with('profile'),
            'sort' => [
                'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => ['username', 'created_at'],
            ],
            'pagination' => ['pageSize' => 30],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Profile page with the comment wall.
     *
     * @throws NotFoundHttpException if the user does not exist
     */
    public function actionView(string $username): string
    {
        $user = $this->findUser($username);
        /** @var User|null $viewer */
        $viewer = Yii::$app->user->identity;

        // Visiting your own wall marks new comments as read (and clears the menu count);
        // remember which ones were new so they can still be highlighted on this visit.
        $newCommentIds = [];

        if ($viewer?->id === $user->id) {
            $newCommentIds = $user->getReceivedComments()->unread()->select('id')->column();
            $viewer->markCommentsRead();
        }

        return $this->render('view', [
            'user' => $user,
            'comments' => self::wallComments($user, $viewer),
            'newCommentIds' => array_map('intval', $newCommentIds),
            'newComment' => new Comment(),
        ]);
    }

    /**
     * @return Comment[]
     */
    public static function wallComments(User $owner, ?User $viewer): array
    {
        return $owner->getReceivedComments()
            ->visibleTo($viewer)
            ->with('author')
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findUser(string $username): User
    {
        return User::findByUsername($username)
            ?? throw new NotFoundHttpException('Käyttäjää ei löytynyt.');
    }
}
