<?php

declare(strict_types=1);

namespace app\tests\Support;

use app\models\Album;
use app\models\Comment;
use app\models\Image;
use app\models\User;
use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

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

    public static function album(User $owner, string $name = 'Kesä', bool $isPrivate = false): Album
    {
        $album = new Album(['user_id' => $owner->id, 'name' => $name, 'is_private' => $isPrivate]);
        $album->save(false);

        return $album;
    }

    /**
     * An image row with a real (tiny PNG) file under @uploads.
     */
    public static function image(User $owner, ?Album $album = null, ?string $caption = null): Image
    {
        $relative = $owner->id . '/images/' . Yii::$app->security->generateRandomString(8) . '.png';
        FileHelper::createDirectory(dirname(Yii::getAlias('@uploads/' . $relative)));
        copy(self::png(), Yii::getAlias('@uploads/' . $relative));

        $image = new Image([
            'user_id' => $owner->id,
            'album_id' => $album?->id,
            'file_path' => $relative,
            'thumb_path' => $relative,
            'width' => 40,
            'height' => 30,
            'caption' => $caption,
        ]);
        $image->save(false);

        return $image;
    }

    public static function uploadedPng(string $name = 'kuva.png', int $width = 40, int $height = 30): UploadedFile
    {
        $path = self::png($width, $height);

        return new UploadedFile([
            'name' => $name,
            'tempName' => $path,
            'type' => 'image/png',
            'size' => filesize($path),
            'error' => UPLOAD_ERR_OK,
        ]);
    }

    /**
     * Writes a solid-colour PNG without GD and returns its temporary path.
     */
    public static function png(int $width = 40, int $height = 30): string
    {
        $chunk = static fn (string $type, string $data): string
            => pack('N', strlen($data)) . $type . $data . pack('N', crc32($type . $data));
        $rows = str_repeat("\0" . str_repeat("\xC8\x50\x28", $width), $height);
        $png = "\x89PNG\r\n\x1a\n"
            . $chunk('IHDR', pack('NNCCCCC', $width, $height, 8, 2, 0, 0, 0))
            . $chunk('IDAT', (string) gzcompress($rows))
            . $chunk('IEND', '');

        $path = tempnam(sys_get_temp_dir(), 'amk') . '.png';
        file_put_contents($path, $png);

        return $path;
    }
}
