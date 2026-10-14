<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * An uploaded image. Files live under @uploads (see ImageUploadForm); `album_id` NULL
 * means the image is unsorted ("Lajittelemattomat") and visible only to its owner.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $album_id
 * @property string $file_path
 * @property string $thumb_path
 * @property int|null $width
 * @property int|null $height
 * @property string|null $caption
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read User $user
 * @property-read Album|null $album
 * @property-read string $url
 * @property-read string $thumbUrl
 */
class Image extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%image}}';
    }

    public static function find(): ImageQuery
    {
        return new ImageQuery(static::class);
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Only the caption is edited through forms; files, owner and album are set in code.
     */
    public function rules(): array
    {
        return [
            ['caption', 'trim'],
            ['caption', 'default', 'value' => null],
            ['caption', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'caption' => 'Kuvateksti',
        ];
    }

    /**
     * Number of images in an album, or in the user's unsorted images when $albumId is null.
     */
    public static function countIn(int $userId, ?int $albumId): int
    {
        return (int) static::find()->where(['user_id' => $userId, 'album_id' => $albumId])->count();
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && $user->id === $this->user_id;
    }

    /**
     * Unsorted images and images in private albums are visible only to the owner.
     */
    public function isVisibleTo(?User $viewer): bool
    {
        if ($this->isOwnedBy($viewer)) {
            return true;
        }

        return $this->album !== null && !$this->album->is_private;
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getAlbum(): ActiveQuery
    {
        return $this->hasOne(Album::class, ['id' => 'album_id']);
    }

    public function getUrl(): string
    {
        return Yii::getAlias('@web/uploads/' . $this->file_path);
    }

    public function getThumbUrl(): string
    {
        return Yii::getAlias('@web/uploads/' . $this->thumb_path);
    }

    /**
     * The neighbouring image in the same album (or the unsorted images), for previous/next links.
     */
    public function sibling(bool $next): ?self
    {
        return static::find()
            ->where(['user_id' => $this->user_id, 'album_id' => $this->album_id])
            ->andWhere([$next ? '>' : '<', 'id', $this->id])
            ->orderBy(['id' => $next ? SORT_ASC : SORT_DESC])
            ->limit(1)
            ->one();
    }

    /**
     * Removes the image files together with the row.
     */
    public function afterDelete(): void
    {
        parent::afterDelete();

        foreach (array_unique([$this->file_path, $this->thumb_path]) as $path) {
            $file = Yii::getAlias('@uploads/' . $path);

            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
