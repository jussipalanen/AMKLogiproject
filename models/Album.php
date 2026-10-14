<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * A photo album. Private albums are visible only to their owner.
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_private
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read User $user
 * @property-read Image[] $images
 * @property-read Image|null $coverImage
 */
class Album extends ActiveRecord
{
    /** Filled by AlbumQuery::withImageCount(). */
    public ?int $imageCount = null;

    public static function tableName(): string
    {
        return '{{%album}}';
    }

    public static function find(): AlbumQuery
    {
        return new AlbumQuery(static::class);
    }

    /**
     * Most images an album (or the unsorted images) may hold, see params['albumMaxImages'].
     */
    public static function maxImages(): int
    {
        return (int) Yii::$app->params['albumMaxImages'];
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'description'], 'trim'],
            ['name', 'required'],
            ['name', 'string', 'max' => 80],
            ['description', 'default', 'value' => null],
            ['description', 'string', 'max' => 500],
            ['is_private', 'boolean'],
            ['is_private', 'filter', 'filter' => 'boolval'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Albumin nimi',
            'description' => 'Kuvaus',
            'is_private' => 'Yksityinen (näkyy vain sinulle)',
        ];
    }

    public function isVisibleTo(?User $viewer): bool
    {
        return !$this->is_private || $this->isOwnedBy($viewer);
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && $user->id === $this->user_id;
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getImages(): ActiveQuery
    {
        return $this->hasMany(Image::class, ['album_id' => 'id'])->orderBy(['id' => SORT_ASC]);
    }

    public function getCoverImage(): ?Image
    {
        return Image::find()->where(['album_id' => $this->id])->orderBy(['id' => SORT_ASC])->limit(1)->one();
    }

    /**
     * When an album is deleted its images are kept and moved to the unsorted images
     * (the database does the same with ON DELETE SET NULL).
     */
    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        Image::updateAll(['album_id' => null, 'updated_at' => time()], ['album_id' => $this->id]);

        return true;
    }
}
