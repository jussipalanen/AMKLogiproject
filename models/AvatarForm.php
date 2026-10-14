<?php

declare(strict_types=1);

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Profile picture upload. The picture itself is stored by Profile::replaceAvatar().
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

    public function save(Profile $profile): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$profile->replaceAvatar($this->avatar->tempName, $this->avatar->extension)) {
            $this->addError('avatar', 'Kuvan tallennus epäonnistui.');

            return false;
        }

        return true;
    }
}
