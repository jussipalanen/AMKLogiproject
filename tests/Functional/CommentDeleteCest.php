<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\models\Comment;
use app\models\User;
use app\tests\Support\Factory;
use app\tests\Support\FunctionalTester;
use yii\helpers\Url;

final class CommentDeleteCest
{
    private User $_matti;
    private User $_liisa;
    private User $_pekka;

    public function _before(FunctionalTester $I)
    {
        $this->_matti = Factory::user('matti');
        $this->_liisa = Factory::user('liisa');
        $this->_pekka = Factory::user('pekka');
    }

    private function deleteUrl(string $username): string
    {
        return Url::to(['/comment/delete', 'username' => $username]);
    }

    public function ownerSeesBulkControlsAndAuthorOnlyOwnDeleteButton(FunctionalTester $I)
    {
        $byLiisa = Factory::comment($this->_liisa, $this->_matti, 'Liisan kommentti');
        $byPekka = Factory::comment($this->_pekka, $this->_matti, 'Pekan kommentti');

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage('/matti');
        $I->seeElement('[data-select-all]');
        $I->see('Poista kaikki');
        $I->seeNumberOfElements('.amk-comment-select', 2);
        $I->seeNumberOfElements('.amk-delete-btn', 2);

        $I->amLoggedInAs($this->_liisa);
        $I->amOnPage('/matti');
        $I->dontSeeElement('[data-select-all]');
        $I->dontSeeElement('.amk-comment-select');
        $I->seeElement("#comment-{$byLiisa->id} .amk-delete-btn");
        $I->dontSeeElement("#comment-{$byPekka->id} .amk-delete-btn");

        $I->amOnPage('/');
        $I->amLoggedInAs($this->_pekka);
        $I->amOnPage('/liisa');
        $I->dontSeeElement('.amk-delete-btn');
    }

    public function ownerDeletesOneComment(FunctionalTester $I)
    {
        $a = Factory::comment($this->_liisa, $this->_matti, 'Poistettava');
        $b = Factory::comment($this->_liisa, $this->_matti, 'Jää');

        $I->amLoggedInAs($this->_matti);
        // the clicked button's id wins over any checked boxes
        $I->sendAjaxPostRequest($this->deleteUrl('matti'), ['id' => $a->id, 'ids' => [$b->id]]);
        $I->seeResponseCodeIs(200);
        $I->dontSeeRecord(Comment::class, ['id' => $a->id]);
        $I->seeRecord(Comment::class, ['id' => $b->id]);
    }

    public function ownerDeletesSelectedComments(FunctionalTester $I)
    {
        $a = Factory::comment($this->_liisa, $this->_matti, 'Yksi');
        $b = Factory::comment($this->_pekka, $this->_matti, 'Kaksi');
        $c = Factory::comment($this->_liisa, $this->_matti, 'Kolme');

        $I->amLoggedInAs($this->_matti);
        $I->sendAjaxPostRequest($this->deleteUrl('matti'), ['ids' => [$a->id, $b->id]]);
        $I->seeResponseCodeIs(200);
        $I->dontSeeRecord(Comment::class, ['id' => $a->id]);
        $I->dontSeeRecord(Comment::class, ['id' => $b->id]);
        $I->seeRecord(Comment::class, ['id' => $c->id]);
    }

    public function ownerDeletesAllCommentsOnOwnWallOnly(FunctionalTester $I)
    {
        Factory::comment($this->_liisa, $this->_matti, 'Yksi');
        Factory::comment($this->_pekka, $this->_matti, 'Kaksi', true);
        $elsewhere = Factory::comment($this->_matti, $this->_liisa, 'Liisan seinällä');

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage('/matti');
        $I->submitForm('#comment-delete-form', ['all' => 1]);
        $I->see('2 kommenttia poistettu.');
        $I->dontSeeRecord(Comment::class, ['recipient_id' => $this->_matti->id]);
        $I->seeRecord(Comment::class, ['id' => $elsewhere->id]);
    }

    public function authorDeletesOwnCommentOnAnotherWall(FunctionalTester $I)
    {
        $own = Factory::comment($this->_liisa, $this->_matti, 'Hups');

        $I->amLoggedInAs($this->_liisa);
        $I->sendAjaxPostRequest($this->deleteUrl('matti'), ['id' => $own->id]);
        $I->seeResponseCodeIs(200);
        $I->dontSeeRecord(Comment::class, ['id' => $own->id]);
    }

    public function otherUsersCannotDeleteComments(FunctionalTester $I)
    {
        $comment = Factory::comment($this->_liisa, $this->_matti, 'Ei sinun');

        $I->amLoggedInAs($this->_pekka);
        $I->sendAjaxPostRequest($this->deleteUrl('matti'), ['id' => $comment->id]);
        $I->seeResponseCodeIs(403);
        $I->seeRecord(Comment::class, ['id' => $comment->id]);
    }

    public function authorCannotDeleteAllOrOthersComments(FunctionalTester $I)
    {
        $own = Factory::comment($this->_liisa, $this->_matti, 'Oma');
        $others = Factory::comment($this->_pekka, $this->_matti, 'Pekan');

        $I->amLoggedInAs($this->_liisa);
        $I->sendAjaxPostRequest($this->deleteUrl('matti'), ['all' => 1]);
        $I->seeResponseCodeIs(403);

        // all or nothing: her own comment is not deleted either
        $I->sendAjaxPostRequest($this->deleteUrl('matti'), ['ids' => [$own->id, $others->id]]);
        $I->seeResponseCodeIs(403);
        $I->seeRecord(Comment::class, ['id' => $own->id]);
        $I->seeRecord(Comment::class, ['id' => $others->id]);
    }

    public function idsFromAnotherWallAreIgnored(FunctionalTester $I)
    {
        $onLiisasWall = Factory::comment($this->_pekka, $this->_liisa, 'Liisan seinällä');

        $I->amLoggedInAs($this->_matti);
        $I->sendAjaxPostRequest($this->deleteUrl('matti'), ['ids' => [$onLiisasWall->id]]);
        $I->seeResponseCodeIs(200);
        $I->seeRecord(Comment::class, ['id' => $onLiisasWall->id]);
    }

    public function guestCannotDelete(FunctionalTester $I)
    {
        $comment = Factory::comment($this->_liisa, $this->_matti, 'Pysyy');

        $I->sendAjaxPostRequest($this->deleteUrl('matti'), ['id' => $comment->id]);
        $I->seeResponseCodeIsRedirection();
        $I->seeRecord(Comment::class, ['id' => $comment->id]);
    }
}
