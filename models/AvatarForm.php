<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Profile picture upload.
 *
 * Images are re-encoded as JPEG and scaled to fit params['avatarMaxSize'], which also
 * strips metadata and anything that is not image data. Without GD the validated original
 * is stored as-is.
 */
class AvatarForm extends Model
{
    public UploadedFile|string|null $avatar = null;

    public function rules(): array
    {
        return [
            [
                'avatar',
                'image',
                'skipOnEmpty' => false,
                'extensions' => 'png, jpg, jpeg, gif, webp',
                'maxSize' => 5 * 1024 * 1024,
                'maxWidth' => 6000,
                'maxHeight' => 6000,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'avatar' => 'Profiilikuva',
        ];
    }

    /**
     * Stores the uploaded picture under @uploads/<user id>/ and replaces the previous one.
     */
    public function save(Profile $profile): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $directory = (string) $profile->user_id;
        FileHelper::createDirectory(Yii::getAlias("@uploads/$directory"));

        $name = Yii::$app->security->generateRandomString(16);
        $relativePath = "$directory/$name.jpg";

        if (!$this->writeResizedJpeg(Yii::getAlias("@uploads/$relativePath"))) {
            $relativePath = "$directory/$name." . strtolower($this->avatar->extension);

            if (!$this->avatar->saveAs(Yii::getAlias("@uploads/$relativePath"), false)) {
                $this->addError('avatar', 'Kuvan tallennus epäonnistui.');

                return false;
            }
        }

        $previous = $profile->avatar_path;
        $profile->avatar_path = $relativePath;
        $profile->save(false, ['avatar_path', 'updated_at']);

        if ($previous !== null && is_file(Yii::getAlias("@uploads/$previous"))) {
            unlink(Yii::getAlias("@uploads/$previous"));
        }

        return true;
    }

    private function writeResizedJpeg(string $target): bool
    {
        if (!function_exists('imagecreatefromstring')) {
            return false;
        }

        $source = @imagecreatefromstring((string) file_get_contents($this->avatar->tempName));

        if ($source === false) {
            return false;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, Yii::$app->params['avatarMaxSize'] / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        // White background for transparent PNG/GIF/WebP images.
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return imagejpeg($canvas, $target, 85);
    }
}
