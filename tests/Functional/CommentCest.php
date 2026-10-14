<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\models\Comment;
use app\models\User;
use app\tests\Support\Factory;
use app\tests\Support\FunctionalTester;
use yii\helpers\Url;

final class CommentCest
{
    private User $_matti;
    private User $_liisa;

    public function _before(FunctionalTester $I)
    {
        $this->_matti = Factory::user('matti');
        $this->_liisa = Factory::user('liisa');
    }

    private function commentUrl(string $username): string
    {
        return Url::to(['/comment/create', 'username' => $username]);
    }

    public function guestCannotComment(FunctionalTester $I)
    {
        $I->amOnPage('/matti');
        $I->dontSeeElement('#comment-form');

        $I->sendAjaxPostRequest($this->commentUrl('matti'), ['Comment' => ['body' => 'Hei']]);
        // Sent to the login page (comments.js asks for JSON and gets 403 instead).
        $I->seeResponseCodeIsRedirection();
        $I->dontSeeRecord(Comment::class, ['body' => 'Hei']);
    }

    public function postCommentWithForm(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->_liisa);
        $I->amOnPage('/matti');
        $I->submitForm('#comment-form', ['Comment[body]' => 'Moi Matti!']);
        $I->see('Kommentti lähetetty.');
        $I->see('Moi Matti!', '.comment-body');
        $I->seeRecord(Comment::class, [
            'body' => 'Moi Matti!',
            'author_id' => $this->_liisa->id,
            'recipient_id' => $this->_matti->id,
        ]);
    }

    public function authorCannotBeSpoofed(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->_liisa);
        $I->sendAjaxPostRequest($this->commentUrl('matti'), [
            'Comment' => ['body' => 'Väärennetty?', 'author_id' => $this->_matti->id],
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeRecord(Comment::class, ['body' => 'Väärennetty?', 'author_id' => $this->_liisa->id]);
    }

    public function commentHtmlIsEscaped(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->_liisa);
        $I->sendAjaxPostRequest($this->commentUrl('matti'), [
            'Comment' => ['body' => '<script>alert(1)</script>'],
        ]);
        $I->seeResponseCodeIs(200);
        $I->amOnPage('/matti');
        $I->seeInSource('&lt;script&gt;alert(1)&lt;/script&gt;');
        $I->dontSeeInSource('<script>alert(1)</script>');
    }

    public function emptyAjaxCommentIsRejected(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->_liisa);
        $I->sendAjaxPostRequest($this->commentUrl('matti'), ['Comment' => ['body' => ' ']]);
        $I->seeResponseCodeIs(422);
        $I->dontSeeRecord(Comment::class, ['author_id' => $this->_liisa->id]);
    }

    public function privateCommentsAreHiddenFromOthers(FunctionalTester $I)
    {
        Factory::comment($this->_liisa, $this->_matti, 'Salainen viesti', true);

        $I->amOnPage('/matti');
        $I->dontSee('Salainen viesti');

        $I->amLoggedInAs(Factory::user('pekka'));
        $I->amOnPage('/matti');
        $I->dontSee('Salainen viesti');

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage('/matti');
        $I->see('Salainen viesti');
    }

    public function visitingOwnWallClearsUnreadBadge(FunctionalTester $I)
    {
        Factory::comment($this->_liisa, $this->_matti, 'Uusi kommentti');

        Factory::comment($this->_liisa, $this->_matti, 'Toinen uusi');

        $I->amLoggedInAs($this->_matti);
        $I->amOnPage('/');
        $I->see('(2)', '.navbar .amk-unread');
        $I->see('2 uutta kommenttia', '.amk-unread-summary');

        $I->amOnPage('/matti');
        $I->dontSeeElement('.navbar .amk-unread');
        $I->amOnPage('/');
        $I->see('Ei uusia kommentteja.', '.amk-unread-summary');
        $I->assertNotNull(Comment::findOne(['body' => 'Uusi kommentti'])->read_at);
    }
}
