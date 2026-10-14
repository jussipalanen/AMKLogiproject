<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\models\Comment;
use app\models\User;
use app\tests\Support\Factory;

final class CommentQueryTest extends \Codeception\Test\Unit
{
    private User $_owner;
    private User $_author;

    protected function _before(): void
    {
        $this->_owner = Factory::user('matti');
        $this->_author = Factory::user('liisa');
        Factory::comment($this->_author, $this->_owner, 'julkinen');
        Factory::comment($this->_author, $this->_owner, 'yksityinen', true);
    }

    /**
     * @return string[]
     */
    private function visibleBodies(?User $viewer): array
    {
        return $this->_owner->getReceivedComments()
            ->visibleTo($viewer)
            ->select('body')
            ->orderBy('id')
            ->column();
    }

    public function testGuestSeesOnlyPublicComments(): void
    {
        verify($this->visibleBodies(null))->equals(['julkinen']);
    }

    public function testOtherUserSeesOnlyPublicComments(): void
    {
        verify($this->visibleBodies(Factory::user('pekka')))->equals(['julkinen']);
    }

    public function testWallOwnerSeesPrivateComments(): void
    {
        verify($this->visibleBodies($this->_owner))->equals(['julkinen', 'yksityinen']);
    }

    public function testAuthorSeesOwnPrivateComments(): void
    {
        verify($this->visibleBodies($this->_author))->equals(['julkinen', 'yksityinen']);
    }

    public function testAdminSeesEverything(): void
    {
        verify($this->visibleBodies(Factory::user('ylläpito', User::ROLE_ADMIN)))->equals(['julkinen', 'yksityinen']);
    }

    public function testCommentRulesOnlyAcceptBodyAndPrivacyFromInput(): void
    {
        $comment = new Comment();
        $comment->load(['Comment' => ['body' => ' moi ', 'is_private' => '1', 'author_id' => 99, 'recipient_id' => 98]]);

        verify($comment->author_id)->null();
        verify($comment->recipient_id)->null();
        verify($comment->validate())->true();
        verify($comment->body)->equals('moi');
        verify($comment->is_private)->true();
    }

    public function testEmptyAndTooLongCommentsAreRejected(): void
    {
        verify((new Comment(['body' => '   ']))->validate())->false();
        verify((new Comment(['body' => str_repeat('a', Comment::MAX_LENGTH + 1)]))->validate())->false();
    }
}
