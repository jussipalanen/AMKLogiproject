<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Album;
use app\models\Image;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Photo albums: /<username>/albums, /<username>/albums/<id> and /<username>/albums/unsorted.
 * Private albums and unsorted images answer 404 to everyone but the owner.
 */
class AlbumController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
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
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(string $username): string
    {
        $owner = $this->findUser($username);
        $viewer = $this->viewer();
        $isOwner = $viewer?->id === $owner->id;

        return $this->render('index', [
            'owner' => $owner,
            'isOwner' => $isOwner,
            'albums' => Album::find()
                ->where(['{{%album}}.[[user_id]]' => $owner->id])
                ->visibleTo($viewer)
                ->withImageCount()
                ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
                ->all(),
            'unsortedCount' => $isOwner ? Image::countIn($owner->id, null) : 0,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(string $username, int $id): string
    {
        $owner = $this->findUser($username);
        $album = Album::findOne(['id' => $id, 'user_id' => $owner->id]);

        if ($album === null || !$album->isVisibleTo($this->viewer())) {
            throw new NotFoundHttpException('Albumia ei löytynyt.');
        }

        return $this->renderAlbum($owner, $album, $album->images);
    }

    /**
     * The owner's images that are not in any album.
     *
     * @throws NotFoundHttpException
     */
    public function actionUnsorted(string $username): string
    {
        $owner = $this->findUser($username);

        if ($this->viewer()?->id !== $owner->id) {
            throw new NotFoundHttpException('Sivua ei löytynyt.');
        }

        $images = Image::find()
            ->where(['user_id' => $owner->id, 'album_id' => null])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return $this->renderAlbum($owner, null, $images);
    }

    public function actionCreate(): Response|string
    {
        $album = new Album(['user_id' => $this->viewer()->id]);

        if ($album->load($this->request->post()) && $album->save()) {
            Yii::$app->session->setFlash('success', 'Albumi luotu. Lisää siihen kuvia.');

            return $this->redirect(['image/upload', 'album' => $album->id]);
        }

        return $this->render('create', ['album' => $album]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $album = $this->findOwnAlbum($id);

        if ($album->load($this->request->post()) && $album->save()) {
            Yii::$app->session->setFlash('success', 'Albumi tallennettu.');

            return $this->redirect(['view', 'username' => $this->viewer()->username, 'id' => $album->id]);
        }

        return $this->render('update', ['album' => $album]);
    }

    /**
     * Deletes the album; its images are moved to the unsorted images.
     *
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): Response
    {
        $album = $this->findOwnAlbum($id);
        $count = Image::countIn($album->user_id, $album->id);
        $album->delete();

        Yii::$app->session->setFlash('success', $count === 0
            ? 'Albumi poistettu.'
            : Yii::$app->i18n->format(
                'Albumi poistettu. {n, plural, =1{Sen kuva siirrettiin} other{Sen # kuvaa siirrettiin}} lajittelemattomiin.',
                ['n' => $count],
                'fi-FI',
            ));

        return $this->redirect(['index', 'username' => $this->viewer()->username]);
    }

    /**
     * @param Image[] $images
     */
    private function renderAlbum(User $owner, ?Album $album, array $images): string
    {
        $isOwner = $this->viewer()?->id === $owner->id;

        return $this->render('view', [
            'owner' => $owner,
            'album' => $album,
            'images' => $images,
            'isOwner' => $isOwner,
            'moveOptions' => $isOwner ? self::moveOptions($owner, $album?->id) : [],
        ]);
    }

    /**
     * Targets for "Siirrä": the owner's other albums and the unsorted images.
     *
     * @return array<string|int, string>
     */
    public static function moveOptions(User $owner, ?int $currentAlbumId): array
    {
        $options = $currentAlbumId === null ? [] : ['' => 'Lajittelemattomat'];
        $albums = Album::find()->where(['user_id' => $owner->id])->orderBy(['name' => SORT_ASC])->all();

        foreach ($albums as $album) {
            if ($album->id !== $currentAlbumId) {
                $options[$album->id] = $album->name . ($album->is_private ? ' (yksityinen)' : '');
            }
        }

        return $options;
    }

    private function viewer(): ?User
    {
        /** @var User|null $viewer */
        $viewer = Yii::$app->user->identity;

        return $viewer;
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findUser(string $username): User
    {
        return User::findByUsername($username)
            ?? throw new NotFoundHttpException('Käyttäjää ei löytynyt.');
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findOwnAlbum(int $id): Album
    {
        return Album::findOne(['id' => $id, 'user_id' => $this->viewer()->id])
            ?? throw new NotFoundHttpException('Albumia ei löytynyt.');
    }
}
