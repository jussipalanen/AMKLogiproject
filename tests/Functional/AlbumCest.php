<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\models\Album;
use app\models\Image;
use app\models\User;
use app\tests\Support\Factory;
use app\tests\Support\FunctionalTester;
use Yii;
use yii\helpers\Url;

final class AlbumCest
{
    private User $_matti;
    private User $_liisa;
    private array $_params = [];

    public function _before(FunctionalTester $I)
    {
        $this->_matti = Factory::user('matti');
        $this->_liisa = Factory::user('liisa');
        $this->_params = Yii::$app->params;
    }

    public function _after(FunctionalTester $I)
    {
        Yii::$app->params = $this->_params;
    }

    public function createPrivateAlbum(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->_matti);
        $I->amOnRoute('album/create');
        $I->submitForm('#album-form', [
            'Album[name]' => 'Salaiset',
            'Album[description]' => 'Vain minulle',
            'Album[is_private]' => 1,
        ]);
        $I->see('Albumi luotu.');
        $I->see('Lataa kuvia', 'h1');
        $I->seeRecord(Album::class, ['name' => 'Salaiset', 'user_id' => $this->_matti->id, 'is_private' => true]);

        $I->amOnPage('/matti/albums');
        $I->see('Salaiset', '.amk-album-card');
        $I->see('Yksityinen', '.amk-album-card');
    }

    public function privateAlbumIsHiddenFromOthers(FunctionalTester $I)
    {
        $private = Factory::album($this->_matti, 'Salaiset', true);
        $public = Factory::album($this->_matti, 'Kesä');
        $privateImage = Factory::image($this->_matti, $private);
        $publicImage = Factory::image($this->_matti, $public, 'Rannalla');

        foreach ([null, $this->_liisa] as $viewer) {
            if ($viewer !== null) {
                $I->amLoggedInAs($viewer);
            }
            $I->amOnPage('/matti/albums');
            $I->see('Kesä', '.amk-album-card');
            $I->dontSee('Salaiset');

            $I->amOnPage("/matti/albums/{$private->id}");
            $I->seeResponseCodeIs(404);
            $I->amOnPage("/matti/images/{$privateImage->id}");
            $I->seeResponseCodeIs(404);

            $I->amOnPage("/matti/images/{$publicImage->id}");
            $I->seeResponseCodeIs(200);
            $I->see('Rannalla', 'figcaption');
            $I->dontSee('Aseta profiilikuvaksi');
        }

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage("/matti/albums/{$private->id}");
        $I->seeResponseCodeIs(200);
        $I->see('Salaiset', 'h1');
        $I->seeElement('.amk-image-select');
    }

    public function unsortedImagesAreOnlyForTheOwner(FunctionalTester $I)
    {
        Factory::image($this->_matti);

        $I->amOnPage('/matti/albums/unsorted');
        $I->seeResponseCodeIs(404);

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage('/matti/albums');
        $I->see('Lajittelemattomat', '.amk-album-card');
        $I->amOnPage('/matti/albums/unsorted');
        $I->seeResponseCodeIs(200);
        $I->seeNumberOfElements('.amk-image-tile', 1);
    }

    public function moveSelectedImagesToAnotherAlbum(FunctionalTester $I)
    {
        $summer = Factory::album($this->_matti, 'Kesä');
        $winter = Factory::album($this->_matti, 'Talvi');
        $a = Factory::image($this->_matti, $summer);
        $b = Factory::image($this->_matti, $summer);
        $c = Factory::image($this->_matti, $summer);

        $I->amLoggedInAs($this->_matti);
        $I->sendAjaxPostRequest(Url::to(['/image/move']), ['ids' => [$a->id, $b->id], 'album_id' => $winter->id]);
        $I->seeRecord(Image::class, ['id' => $a->id, 'album_id' => $winter->id]);
        $I->seeRecord(Image::class, ['id' => $b->id, 'album_id' => $winter->id]);
        $I->seeRecord(Image::class, ['id' => $c->id, 'album_id' => $summer->id]);

        // a single image to the unsorted images
        $I->sendAjaxPostRequest(Url::to(['/image/move']), ['id' => $c->id, 'album_id' => '']);
        $I->seeRecord(Image::class, ['id' => $c->id, 'album_id' => null]);
    }

    public function moveRespectsAlbumLimit(FunctionalTester $I)
    {
        Yii::$app->params['albumMaxImages'] = 2;
        $full = Factory::album($this->_matti, 'Täysi');
        Factory::image($this->_matti, $full);
        $source = Factory::album($this->_matti, 'Lähde');
        $a = Factory::image($this->_matti, $source);
        $b = Factory::image($this->_matti, $source);

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage("/matti/albums/{$source->id}");
        $I->submitForm('#image-bulk-form', ['ids' => [$a->id, $b->id], 'album_id' => $full->id]);
        $I->see('Kohteeseen mahtuu enintään 2 kuvaa. Tilaa on vielä 1 kuvalle.');
        $I->seeRecord(Image::class, ['id' => $a->id, 'album_id' => $source->id]);
    }

    public function cannotTouchOtherUsersImagesOrAlbums(FunctionalTester $I)
    {
        $liisasAlbum = Factory::album($this->_liisa, 'Liisan');
        $liisasImage = Factory::image($this->_liisa, $liisasAlbum);
        $mattisAlbum = Factory::album($this->_matti, 'Matin');
        $mattisImage = Factory::image($this->_matti, $mattisAlbum);

        $I->amLoggedInAs($this->_matti);
        $I->sendAjaxPostRequest(Url::to(['/image/move']), ['ids' => [$liisasImage->id], 'album_id' => $mattisAlbum->id]);
        $I->seeRecord(Image::class, ['id' => $liisasImage->id, 'album_id' => $liisasAlbum->id]);

        $I->sendAjaxPostRequest(Url::to(['/image/move']), ['ids' => [$mattisImage->id], 'album_id' => $liisasAlbum->id]);
        $I->seeResponseCodeIs(400);
        $I->seeRecord(Image::class, ['id' => $mattisImage->id, 'album_id' => $mattisAlbum->id]);

        $I->sendAjaxPostRequest(Url::to(['/image/delete']), ['ids' => [$liisasImage->id]]);
        $I->seeRecord(Image::class, ['id' => $liisasImage->id]);

        $I->sendAjaxPostRequest(Url::to(['/image/avatar', 'id' => $liisasImage->id]));
        $I->seeResponseCodeIs(404);

        $I->amOnRoute('album/update', ['id' => $liisasAlbum->id]);
        $I->seeResponseCodeIs(404);
        $I->sendAjaxPostRequest(Url::to(['/album/delete', 'id' => $liisasAlbum->id]));
        $I->seeResponseCodeIs(404);
        $I->seeRecord(Album::class, ['id' => $liisasAlbum->id]);
    }

    public function deleteSelectedImages(FunctionalTester $I)
    {
        $album = Factory::album($this->_matti);
        $a = Factory::image($this->_matti, $album);
        $b = Factory::image($this->_matti, $album);

        $I->amLoggedInAs($this->_matti);
        $I->sendAjaxPostRequest(Url::to(['/image/delete']), ['ids' => [$a->id]]);
        $I->dontSeeRecord(Image::class, ['id' => $a->id]);
        $I->seeRecord(Image::class, ['id' => $b->id]);
    }

    public function setImageAsProfilePicture(FunctionalTester $I)
    {
        $image = Factory::image($this->_matti, Factory::album($this->_matti, 'Salaiset', true));

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage("/matti/images/{$image->id}");
        $I->click('Aseta profiilikuvaksi');
        $I->see('Profiilikuva vaihdettu.');

        $this->_matti->profile->refresh();
        $I->assertNotNull($this->_matti->profile->avatar_path);
        $I->amOnPage('/matti');
        $I->seeElement('img.amk-portrait[src*="/uploads/"]');
    }

    public function deleteAlbumMovesImagesToUnsorted(FunctionalTester $I)
    {
        $album = Factory::album($this->_matti, 'Poistuva');
        $image = Factory::image($this->_matti, $album);

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage("/matti/albums/{$album->id}");
        $I->submitForm('form[action$="/delete"]', []);
        $I->see('Albumi poistettu. Sen kuva siirrettiin lajittelemattomiin.');
        $I->dontSeeRecord(Album::class, ['id' => $album->id]);
        $I->seeRecord(Image::class, ['id' => $image->id, 'album_id' => null]);
    }

    public function editCaption(FunctionalTester $I)
    {
        $image = Factory::image($this->_matti, Factory::album($this->_matti));

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage("/matti/images/{$image->id}");
        $I->fillField('#image-caption', 'Auringonlasku');
        $I->click('Tallenna');
        $I->see('Kuvateksti tallennettu.');
        $I->see('Auringonlasku', 'figcaption');
    }

    public function guestIsSentToLoginForUpload(FunctionalTester $I)
    {
        $I->amOnRoute('image/upload');
        $I->see('Kirjaudu sisään', 'h1');
    }
}
