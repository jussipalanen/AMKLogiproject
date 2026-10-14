<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\models\Profile;
use app\models\User;
use app\tests\Support\Factory;

final class UserTest extends \Codeception\Test\Unit
{
    public function testPasswordIsHashedWithBcrypt(): void
    {
        $user = Factory::user('matti');

        verify($user->password_hash)->stringStartsWith('$2y$');
        verify($user->validatePassword(Factory::PASSWORD))->true();
        verify($user->validatePassword('wrong'))->false();
    }

    public function testProfileIsCreatedWithUser(): void
    {
        $user = Factory::user('matti');

        verify($user->profile)->instanceOf(Profile::class);
        verify(Profile::find()->where(['user_id' => $user->id])->count())->equals(1);
    }

    public function testIdentityLookup(): void
    {
        $user = Factory::user('matti');

        verify(User::findIdentity($user->id)?->username)->equals('matti');
        verify(User::findIdentity(999999))->null();
        verify(User::findByUsername('matti')?->id)->equals($user->id);
        verify($user->validateAuthKey($user->auth_key))->true();
        verify($user->validateAuthKey('other'))->false();
    }

    public function testUsernameTakenIgnoresCase(): void
    {
        Factory::user('Matti');

        verify(User::usernameTaken('matti'))->true();
        verify(User::usernameTaken('MATTI'))->true();
        verify(User::usernameTaken('liisa'))->false();
    }

    public function testUnreadCommentsAreCountedAndMarkedRead(): void
    {
        $matti = Factory::user('matti');
        $liisa = Factory::user('liisa');
        Factory::comment($liisa, $matti, 'Moi!');
        Factory::comment($liisa, $matti, 'Yksityinen', true);
        Factory::comment($matti, $liisa, 'Ei Matin seinällä');

        verify($matti->getUnreadCommentCount())->equals(2);

        $matti->markCommentsRead();

        verify($matti->getUnreadCommentCount())->equals(0);
        verify($liisa->getUnreadCommentCount())->equals(1);
    }
}
