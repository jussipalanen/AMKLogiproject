<?php

declare(strict_types=1);

namespace app\tests\Support;

use app\models\Comment;
use app\models\User;

/**
 * Creates records for tests. Codeception's Yii2 module rolls them back after each test.
 */
final class Factory
{
    public const PASSWORD = 'salasana123';

    public static function user(string $username, string $role = User::ROLE_STUDENT): User
    {
        $user = new User([
            'username' => $username,
            'email' => "$username@example.com",
            'role' => $role,
        ]);
        $user->setPassword(self::PASSWORD);
        $user->generateAuthKey();
        $user->save(false);

        return $user;
    }

    public static function comment(User $author, User $recipient, string $body, bool $isPrivate = false): Comment
    {
        $comment = new Comment([
            'author_id' => $author->id,
            'recipient_id' => $recipient->id,
            'body' => $body,
            'is_private' => $isPrivate,
        ]);
        $comment->save(false);

        return $comment;
    }
}
