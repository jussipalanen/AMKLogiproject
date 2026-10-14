<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\ImageResizer;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;

/**
 * Profile details shown on a user's page.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $firstname
 * @property string|null $surname
 * @property string|null $gender
 * @property string|null $birthday
 * @property string|null $email
 * @property string|null $city
 * @property string|null $homepage
 * @property string|null $skype
 * @property string|null $avatar_path
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read User $user
 * @property-read string|null $avatarUrl
 * @property-read string $fullName
 * @property-read string|null $genderLabel
 */
class Profile extends ActiveRecord
{
    public const GENDERS = [
        'male' => 'Mies',
        'female' => 'Nainen',
        'other' => 'Muu',
    ];

    public static function tableName(): string
    {
        return '{{%profile}}';
    }

    /**
     * Cities for the "Kaupunki" dropdown (config/cities.php).
     *
     * @return string[]
     */
    public static function cities(): array
    {
        static $cities = null;

        return $cities ??= require Yii::getAlias('@app/config/cities.php');
    }

    /**
     * Dropdown options; a saved city that is not on the list (older data) stays selectable.
     *
     * @return array<string, string>
     */
    public function cityOptions(): array
    {
        $cities = self::cities();
        $saved = $this->getOldAttribute('city');

        if (is_string($saved) && $saved !== '' && !in_array($saved, $cities, true)) {
            $cities[] = $saved;
        }

        return array_combine($cities, $cities);
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
            [['firstname', 'surname', 'email', 'city', 'homepage', 'skype'], 'trim'],
            [
                ['firstname', 'surname', 'gender', 'birthday', 'email', 'city', 'homepage', 'skype'],
                'default',
                'value' => null,
            ],
            [['firstname', 'surname'], 'string', 'max' => 60],
            ['gender', 'in', 'range' => array_keys(self::GENDERS)],
            ['birthday', 'date', 'format' => 'php:Y-m-d', 'max' => date('Y-m-d'), 'tooBig' => 'Syntymäaika ei voi olla tulevaisuudessa.'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'city',
                'in',
                'range' => static fn (): array => self::cities(),
                'message' => 'Valitse kaupunki listalta.',
                // older free-text values can be kept when saving other fields
                'when' => static fn (self $model): bool => $model->city !== $model->getOldAttribute('city'),
            ],
            // Only http(s) links, so the homepage can be rendered as a link safely.
            ['homepage', 'url', 'validSchemes' => ['http', 'https'], 'defaultScheme' => 'https'],
            ['homepage', 'string', 'max' => 255],
            ['skype', 'string', 'max' => 64],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'firstname' => 'Etunimi',
            'surname' => 'Sukunimi',
            'gender' => 'Sukupuoli',
            'birthday' => 'Syntymäaika',
            'email' => 'Julkinen sähköposti',
            'city' => 'Kaupunki',
            'homepage' => 'Kotisivu',
            'skype' => 'Skype',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getFullName(): string
    {
        return trim($this->firstname . ' ' . $this->surname);
    }

    public function getGenderLabel(): ?string
    {
        return self::GENDERS[$this->gender] ?? null;
    }

    /**
     * Makes a copy of $sourcePath the profile picture: scaled to params['avatarMaxSize'] and
     * re-encoded as JPEG, or copied as-is without GD. The previous picture file is removed.
     * Used for uploads and for album images ("Aseta profiilikuvaksi").
     */
    public function replaceAvatar(string $sourcePath, string $extension): bool
    {
        $directory = (string) $this->user_id;
        FileHelper::createDirectory(Yii::getAlias("@uploads/$directory"));

        $name = Yii::$app->security->generateRandomString(16);
        $relativePath = "$directory/$name.jpg";

        if (ImageResizer::toJpeg($sourcePath, Yii::getAlias("@uploads/$relativePath"), Yii::$app->params['avatarMaxSize']) === null) {
            $relativePath = "$directory/$name." . strtolower($extension);

            if (!copy($sourcePath, Yii::getAlias("@uploads/$relativePath"))) {
                return false;
            }
        }

        $previous = $this->avatar_path;
        $this->avatar_path = $relativePath;
        $this->save(false, ['avatar_path', 'updated_at']);

        if ($previous !== null && is_file(Yii::getAlias("@uploads/$previous"))) {
            unlink(Yii::getAlias("@uploads/$previous"));
        }

        return true;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatar_path === null ? null : Yii::getAlias('@web/uploads/' . $this->avatar_path);
    }
}
