<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Album;
use app\models\Image;
use app\models\ImageQuery;
use app\models\ImageUploadForm;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Viewing, uploading, moving and deleting album images. Only the owner can change images;
 * anything the viewer may not see answers 404.
 */
class ImageController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => ['view'],
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
                    'move' => ['post'],
                    'delete' => ['post'],
                    'avatar' => ['post'],
                    'caption' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(string $username, int $id): string
    {
        $owner = User::findByUsername($username)
            ?? throw new NotFoundHttpException('Käyttäjää ei löytynyt.');
        $image = Image::findOne(['id' => $id, 'user_id' => $owner->id]);
        /** @var User|null $viewer guests may view public images */
        $viewer = Yii::$app->user->identity;

        if ($image === null || !$image->isVisibleTo($viewer)) {
            throw new NotFoundHttpException('Kuvaa ei löytynyt.');
        }

        $isOwner = $image->isOwnedBy($viewer);

        return $this->render('view', [
            'owner' => $owner,
            'image' => $image,
            'previous' => $image->sibling(false),
            'next' => $image->sibling(true),
            'isOwner' => $isOwner,
            'moveOptions' => $isOwner ? AlbumController::moveOptions($owner, $image->album_id) : [],
        ]);
    }

    public function actionUpload(): Response|string
    {
        $user = $this->viewer();
        $form = new ImageUploadForm($user);
        $form->album_id = $this->request->get('album');

        if ($this->request->isPost) {
            $form->load($this->request->post());
            $form->files = UploadedFile::getInstances($form, 'files');

            if ($form->upload()) {
                Yii::$app->session->setFlash('success', Yii::$app->i18n->format(
                    '{n, plural, =1{1 kuva ladattu.} other{# kuvaa ladattu.}}',
                    ['n' => count($form->uploaded)],
                    'fi-FI',
                ));

                return $this->redirect($form->album_id === null
                    ? ['album/unsorted', 'username' => $user->username]
                    : ['album/view', 'username' => $user->username, 'id' => $form->album_id]);
            }
        }

        return $this->render('upload', ['model' => $form]);
    }

    /**
     * Moves the selected images (ids[]) or one image (id) to another album, or to the
     * unsorted images when album_id is empty.
     *
     * @throws BadRequestHttpException if the target album is not the user's own
     */
    public function actionMove(): Response
    {
        $user = $this->viewer();
        $target = $this->request->post('album_id');
        $targetId = $target === null || $target === '' ? null : (int) $target;

        if ($targetId !== null && !Album::find()->where(['id' => $targetId, 'user_id' => $user->id])->exists()) {
            throw new BadRequestHttpException('Tuntematon albumi.');
        }

        // Images already in the target stay where they are.
        $images = array_filter(
            $this->findOwnImages()->all(),
            static fn (Image $image): bool => $image->album_id !== $targetId,
        );

        $free = Album::maxImages() - Image::countIn($user->id, $targetId);

        if ($images === []) {
            Yii::$app->session->setFlash('warning', 'Valitse siirrettävät kuvat.');
        } elseif (count($images) > $free) {
            Yii::$app->session->setFlash('error', Yii::$app->i18n->format(
                'Kohteeseen mahtuu enintään {max} kuvaa. Tilaa on vielä {free, plural, =0{0 kuvalle} =1{1 kuvalle} other{# kuvalle}}.',
                ['max' => Album::maxImages(), 'free' => max(0, $free)],
                'fi-FI',
            ));
        } else {
            Image::updateAll(
                ['album_id' => $targetId, 'updated_at' => time()],
                ['id' => array_map(static fn (Image $image): int => $image->id, $images)],
            );
            Yii::$app->session->setFlash('success', Yii::$app->i18n->format(
                '{n, plural, =1{Kuva siirretty} other{# kuvaa siirretty}} kohteeseen {target}.',
                ['n' => count($images), 'target' => $targetId === null ? 'Lajittelemattomat' : Album::findOne($targetId)->name],
                'fi-FI',
            ));
        }

        return $this->goBackTo();
    }

    /**
     * Deletes the selected images (ids[]) or one image (id), files included.
     */
    public function actionDelete(): Response
    {
        $images = $this->findOwnImages()->all();

        foreach ($images as $image) {
            $image->delete();
        }

        if ($images === []) {
            Yii::$app->session->setFlash('warning', 'Valitse poistettavat kuvat.');
        } else {
            Yii::$app->session->setFlash('success', Yii::$app->i18n->format(
                '{n, plural, =1{Kuva poistettu.} other{# kuvaa poistettu.}}',
                ['n' => count($images)],
                'fi-FI',
            ));
        }

        return $this->goBackTo();
    }

    /**
     * Uses the image as the profile picture (a separate copy, so the album image can still
     * be moved or deleted).
     *
     * @throws NotFoundHttpException
     */
    public function actionAvatar(int $id): Response
    {
        $image = $this->findOwnImage($id);
        $source = Yii::getAlias('@uploads/' . $image->file_path);

        if ($this->viewer()->profile->replaceAvatar($source, pathinfo($source, PATHINFO_EXTENSION))) {
            Yii::$app->session->setFlash('success', 'Profiilikuva vaihdettu.');
        } else {
            Yii::$app->session->setFlash('error', 'Profiilikuvan vaihtaminen epäonnistui.');
        }

        return $this->goBackTo();
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCaption(int $id): Response
    {
        $image = $this->findOwnImage($id);

        if ($image->load($this->request->post()) && $image->save()) {
            Yii::$app->session->setFlash('success', 'Kuvateksti tallennettu.');
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $image->getFirstErrors()));
        }

        return $this->goBackTo();
    }

    private function viewer(): User
    {
        /** @var User $viewer */
        $viewer = Yii::$app->user->identity;

        return $viewer;
    }

    /**
     * The viewer's images named by `id` or `ids[]`; other users' ids are simply not found.
     */
    private function findOwnImages(): ImageQuery
    {
        $id = $this->request->post('id');
        $ids = $id !== null ? [(int) $id] : array_map('intval', (array) $this->request->post('ids', []));

        return Image::find()->where(['id' => $ids === [] ? [0] : $ids, 'user_id' => $this->viewer()->id]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findOwnImage(int $id): Image
    {
        return Image::findOne(['id' => $id, 'user_id' => $this->viewer()->id])
            ?? throw new NotFoundHttpException('Kuvaa ei löytynyt.');
    }

    /**
     * Back to the page given in `returnUrl` (a local path from our own forms), otherwise
     * to the user's albums.
     */
    private function goBackTo(): Response
    {
        $url = (string) $this->request->post('returnUrl', '');

        return self::isLocalPath($url)
            ? $this->redirect($url)
            : $this->redirect(['album/index', 'username' => $this->viewer()->username]);
    }

    /**
     * Whether $url is a path on this site, safe to redirect to: no "//host", no backslashes and
     * no whitespace or control characters (browsers drop tabs/newlines, so "/\t/evil.example"
     * would turn into "//evil.example").
     */
    public static function isLocalPath(string $url): bool
    {
        return preg_match('~^/(?:[^/\\\\\s\x00-\x1f][^\\\\\s\x00-\x1f]*)?$~', $url) === 1;
    }
}
