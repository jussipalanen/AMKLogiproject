<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\models\Album;
use app\models\Image;
use app\models\ImageUploadForm;
use app\tests\Support\Factory;
use Yii;

final class AlbumTest extends \Codeception\Test\Unit
{
    private array $_params = [];

    protected function _before(): void
    {
        $this->_params = Yii::$app->params;
    }

    protected function _after(): void
    {
        Yii::$app->params = $this->_params;
    }

    public function testUploadSeveralImagesIntoAlbum(): void
    {
        $owner = Factory::user('matti');
        $album = Factory::album($owner);
        $form = new ImageUploadForm($owner, ['album_id' => (string) $album->id]);
        $form->files = [Factory::uploadedPng('a.png'), Factory::uploadedPng('b.png'), Factory::uploadedPng('c.png', 2000, 1000)];

        verify($form->upload())->true();
        verify($form->uploaded)->arrayCount(3);
        verify(Image::countIn($owner->id, $album->id))->equals(3);

        foreach ($form->uploaded as $image) {
            verify(is_file(Yii::getAlias('@uploads/' . $image->file_path)))->true();
            verify(is_file(Yii::getAlias('@uploads/' . $image->thumb_path)))->true();
            verify($image->width)->notNull();
        }

        if (extension_loaded('gd')) {
            // re-encoded and scaled to params['imageMaxSize'], with a separate thumbnail
            $large = $form->uploaded[2];
            verify($large->file_path)->stringEndsWith('.jpg');
            verify($large->width)->equals(Yii::$app->params['imageMaxSize']);
            verify($large->thumb_path)->notEquals($large->file_path);
        }
    }

    public function testUploadWithoutAlbumGoesToUnsorted(): void
    {
        $owner = Factory::user('matti');
        $form = new ImageUploadForm($owner, ['album_id' => '']);
        $form->files = [Factory::uploadedPng()];

        verify($form->upload())->true();
        verify(Image::countIn($owner->id, null))->equals(1);
    }

    public function testCannotUploadIntoAnotherUsersAlbum(): void
    {
        $owner = Factory::user('matti');
        $othersAlbum = Factory::album(Factory::user('liisa'));
        $form = new ImageUploadForm($owner, ['album_id' => (string) $othersAlbum->id]);
        $form->files = [Factory::uploadedPng()];

        verify($form->upload())->false();
        verify($form->errors)->arrayHasKey('album_id');
        verify(Image::find()->count())->equals(0);
    }

    public function testAlbumCapacityComesFromConfig(): void
    {
        Yii::$app->params['albumMaxImages'] = 3;
        $owner = Factory::user('matti');
        $album = Factory::album($owner);
        Factory::image($owner, $album);
        Factory::image($owner, $album);

        $form = new ImageUploadForm($owner, ['album_id' => (string) $album->id]);
        $form->files = [Factory::uploadedPng(), Factory::uploadedPng()];

        verify($form->upload())->false();
        verify($form->getFirstError('files'))->stringContainsString('enintään 3 kuvaa');
        verify(Image::countIn($owner->id, $album->id))->equals(2);
    }

    public function testFilesPerUploadComeFromConfig(): void
    {
        Yii::$app->params['uploadMaxFiles'] = 2;
        $owner = Factory::user('matti');
        $form = new ImageUploadForm($owner);
        $form->files = [Factory::uploadedPng(), Factory::uploadedPng(), Factory::uploadedPng()];

        verify($form->upload())->false();
        verify($form->errors)->arrayHasKey('files');
    }

    public function testNonImagesAreRejected(): void
    {
        $owner = Factory::user('matti');
        $form = new ImageUploadForm($owner);
        $path = tempnam(sys_get_temp_dir(), 'amk');
        file_put_contents($path, 'ei kuva');
        $form->files = [new \yii\web\UploadedFile([
            'name' => 'kuva.png', 'tempName' => $path, 'type' => 'image/png', 'size' => 7, 'error' => UPLOAD_ERR_OK,
        ])];

        verify($form->upload())->false();
    }

    public function testDeletingAlbumKeepsImagesAsUnsorted(): void
    {
        $owner = Factory::user('matti');
        $album = Factory::album($owner);
        $image = Factory::image($owner, $album);

        $album->delete();

        $image->refresh();
        verify($image->album_id)->null();
    }

    public function testDeletingImageRemovesFile(): void
    {
        $owner = Factory::user('matti');
        $image = Factory::image($owner);
        $file = Yii::getAlias('@uploads/' . $image->file_path);

        verify(is_file($file))->true();
        $image->delete();
        verify(is_file($file))->false();
    }

    public function testVisibility(): void
    {
        $owner = Factory::user('matti');
        $other = Factory::user('liisa');
        $public = Factory::album($owner, 'Julkinen');
        $private = Factory::album($owner, 'Salainen', true);

        verify($public->isVisibleTo(null))->true();
        verify($private->isVisibleTo(null))->false();
        verify($private->isVisibleTo($other))->false();
        verify($private->isVisibleTo($owner))->true();

        verify(Factory::image($owner, $public)->isVisibleTo($other))->true();
        verify(Factory::image($owner, $private)->isVisibleTo($other))->false();
        verify(Factory::image($owner)->isVisibleTo($other))->false();
        verify(Factory::image($owner)->isVisibleTo($owner))->true();

        $titles = Album::find()->where(['{{%album}}.[[user_id]]' => $owner->id])->visibleTo($other)->select('name')->column();
        verify($titles)->equals(['Julkinen']);
    }

    public function testImageCanBecomeProfilePicture(): void
    {
        $owner = Factory::user('matti');
        $image = Factory::image($owner);

        verify($owner->profile->replaceAvatar(Yii::getAlias('@uploads/' . $image->file_path), 'png'))->true();

        $avatar = Yii::getAlias('@uploads/' . $owner->profile->avatar_path);
        verify(is_file($avatar))->true();
        verify($owner->profile->avatar_path)->notEquals($image->file_path);
    }
}
