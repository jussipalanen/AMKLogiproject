<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\ImageResizer;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Uploads several images at once into one of the user's albums, or into the unsorted
 * images when no album is chosen.
 */
class ImageUploadForm extends Model
{
    /** @var UploadedFile[] */
    public array $files = [];
    public int|string|null $album_id = null;

    /** @var Image[] images created by the last successful upload() */
    public array $uploaded = [];

    public function __construct(private readonly User $owner, $config = [])
    {
        parent::__construct($config);
    }

    public function rules(): array
    {
        $params = Yii::$app->params;

        return [
            [
                'files',
                'image',
                'skipOnEmpty' => false,
                'extensions' => 'png, jpg, jpeg, gif, webp',
                'maxSize' => $params['uploadMaxFileSizeMb'] * 1024 * 1024,
                'maxWidth' => 6000,
                'maxHeight' => 6000,
                'maxFiles' => $params['uploadMaxFiles'],
            ],
            ['album_id', 'filter', 'filter' => static fn ($value): ?int => $value === '' || $value === null ? null : (int) $value],
            ['album_id', 'validateOwnAlbum', 'skipOnEmpty' => true],
            ['files', 'validateAlbumCapacity'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'files' => 'Kuvat',
            'album_id' => 'Albumi',
        ];
    }

    public function validateOwnAlbum(string $attribute): void
    {
        if (!Album::find()->where(['id' => $this->album_id, 'user_id' => $this->owner->id])->exists()) {
            $this->addError($attribute, 'Valitse jokin omista albumeistasi.');
        }
    }

    public function validateAlbumCapacity(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $free = Album::maxImages() - Image::countIn($this->owner->id, $this->album_id);

        if (count($this->files) > $free) {
            $this->addError($attribute, Yii::$app->i18n->format(
                'Albumiin mahtuu enintään {max} kuvaa. Tilaa on vielä {free, plural, =0{0 kuvalle} =1{1 kuvalle} other{# kuvalle}}.',
                ['max' => Album::maxImages(), 'free' => max(0, $free)],
                'fi-FI',
            ));
        }
    }

    /**
     * Album choices for the upload form: the user's albums plus the unsorted images.
     *
     * @return array<string|int, string>
     */
    public function albumOptions(): array
    {
        $albums = Album::find()->where(['user_id' => $this->owner->id])->orderBy(['name' => SORT_ASC])->all();
        $options = ['' => 'Lajittelemattomat'];

        foreach ($albums as $album) {
            $options[$album->id] = $album->name . ($album->is_private ? ' (yksityinen)' : '');
        }

        return $options;
    }

    public function upload(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->uploaded = [];

        foreach ($this->files as $file) {
            $image = $this->store($file);

            if ($image === null) {
                $this->addError('files', 'Kuvan ' . $file->name . ' tallennus epäonnistui.');

                return false;
            }

            $this->uploaded[] = $image;
        }

        return true;
    }

    /**
     * Saves a scaled JPEG and a thumbnail (or the original file as both without GD).
     */
    private function store(UploadedFile $file): ?Image
    {
        $params = Yii::$app->params;
        $directory = $this->owner->id . '/images';
        FileHelper::createDirectory(Yii::getAlias("@uploads/$directory"));

        $name = Yii::$app->security->generateRandomString(16);
        $filePath = "$directory/$name.jpg";
        $thumbPath = "$directory/{$name}_thumb.jpg";

        $size = ImageResizer::toJpeg($file->tempName, Yii::getAlias("@uploads/$filePath"), $params['imageMaxSize']);

        if ($size !== null) {
            ImageResizer::toJpeg($file->tempName, Yii::getAlias("@uploads/$thumbPath"), $params['imageThumbSize']);
        } else {
            $filePath = $thumbPath = "$directory/$name." . strtolower($file->extension);

            if (!copy($file->tempName, Yii::getAlias("@uploads/$filePath"))) {
                return null;
            }

            $dimensions = @getimagesize($file->tempName);
            $size = $dimensions === false ? [null, null] : [$dimensions[0], $dimensions[1]];
        }

        $image = new Image([
            'user_id' => $this->owner->id,
            'album_id' => $this->album_id,
            'file_path' => $filePath,
            'thumb_path' => $thumbPath,
            'width' => $size[0],
            'height' => $size[1],
        ]);

        return $image->save(false) ? $image : null;
    }
}
