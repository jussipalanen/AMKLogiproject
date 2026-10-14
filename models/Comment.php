<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * A comment written on a user's wall. Private comments are visible only to the
 * author, the wall owner and admins (see CommentQuery::visibleTo()).
 *
 * @property int $id
 * @property int $author_id
 * @property int $recipient_id
 * @property string $body
 * @property bool $is_private
 * @property int|null $read_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read User $author
 * @property-read User $recipient
 */
class Comment extends ActiveRecord
{
    public const MAX_LENGTH = 1000;

    public static function tableName(): string
    {
        return '{{%comment}}';
    }

    public static function find(): CommentQuery
    {
        return new CommentQuery(static::class);
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Only the text and the privacy flag come from user input; author and recipient
     * are set by the controller from the logged-in user and the URL.
     */
    public function rules(): array
    {
        return [
            ['body', 'trim'],
            ['body', 'required'],
            ['body', 'string', 'max' => self::MAX_LENGTH],
            ['is_private', 'boolean'],
            ['is_private', 'filter', 'filter' => 'boolval'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'body' => 'Kommentti',
            'is_private' => 'Yksityinen',
        ];
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    public function getRecipient(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'recipient_id']);
    }
}
