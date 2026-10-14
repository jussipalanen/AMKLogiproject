<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
 * @property string|null $relationship
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

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['firstname', 'surname', 'email', 'city', 'relationship', 'homepage', 'skype'], 'trim'],
            [
                ['firstname', 'surname', 'gender', 'birthday', 'email', 'city', 'relationship', 'homepage', 'skype'],
                'default',
                'value' => null,
            ],
            [['firstname', 'surname'], 'string', 'max' => 60],
            ['gender', 'in', 'range' => array_keys(self::GENDERS)],
            ['birthday', 'date', 'format' => 'php:Y-m-d', 'max' => date('Y-m-d'), 'tooBig' => 'Syntymäaika ei voi olla tulevaisuudessa.'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['city', 'string', 'max' => 100],
            ['relationship', 'string', 'max' => 30],
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
            'relationship' => 'Parisuhdetilanne',
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

    public function getAvatarUrl(): ?string
    {
        return $this->avatar_path === null ? null : Yii::getAlias('@web/uploads/' . $this->avatar_path);
    }
}
