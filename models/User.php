<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * A registered user.
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $role
 * @property int|null $last_login_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read Profile|null $profile
 * @property-read Comment[] $receivedComments
 * @property-read Comment[] $authoredComments
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const ROLE_STUDENT = 'student';
    public const ROLE_ADMIN = 'admin';

    /** Allowed username characters; usernames are also URL segments (/<username>). */
    public const USERNAME_PATTERN = '/^[A-Za-z0-9_-]+$/';

    /** Usernames that would collide with application routes. */
    public const RESERVED_USERNAMES = [
        'about', 'admin', 'assets', 'comment', 'comments', 'contact', 'css', 'debug', 'gii',
        'index', 'js', 'login', 'logout', 'profile', 'register', 'signup', 'site', 'uploads',
        'user', 'users',
    ];

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function findIdentity($id): ?static
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?static
    {
        throw new NotSupportedException('Access tokens are not supported.');
    }

    public static function findByUsername(string $username): ?static
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Case-insensitive lookup, used to keep usernames unique regardless of case
     * (MySQL compares case-insensitively, SQLite does not).
     */
    public static function usernameTaken(string $username): bool
    {
        return static::find()
            ->where(['LOWER([[username]])' => mb_strtolower($username)])
            ->exists();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getProfile(): ActiveQuery
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    public function getReceivedComments(): CommentQuery
    {
        return $this->hasMany(Comment::class, ['recipient_id' => 'id']);
    }

    public function getAuthoredComments(): CommentQuery
    {
        return $this->hasMany(Comment::class, ['author_id' => 'id']);
    }

    /**
     * Number of comments on this user's wall that they have not seen yet.
     */
    public function getUnreadCommentCount(): int
    {
        return (int) $this->getReceivedComments()->unread()->count();
    }

    public function markCommentsRead(): void
    {
        Comment::updateAll(
            ['read_at' => time()],
            ['recipient_id' => $this->id, 'read_at' => null],
        );
    }

    /**
     * Every user has exactly one profile row, created together with the user.
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $profile = new Profile(['user_id' => $this->id]);
            $profile->save(false);
        }
    }
}
