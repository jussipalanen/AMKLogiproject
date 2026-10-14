<?php

declare(strict_types=1);

namespace app\models;

use yii\base\Model;

/**
 * Registration form (the original RegisterForm).
 */
class SignupForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_repeat = '';

    public function rules(): array
    {
        return [
            [['username', 'email'], 'trim'],
            [['username', 'email', 'password', 'password_repeat'], 'required'],
            ['username', 'string', 'min' => 3, 'max' => 30],
            [
                'username',
                'match',
                'pattern' => User::USERNAME_PATTERN,
                'message' => 'Tunnus saa sisältää vain kirjaimia a–z, numeroita, alaviivoja ja väliviivoja.',
            ],
            ['username', 'validateUsernameAvailable'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Sähköpostiosoite on jo käytössä.'],
            ['password', 'string', 'min' => 8, 'max' => 72],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Salasanat eivät täsmää.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Tunnus',
            'email' => 'Sähköpostiosoite',
            'password' => 'Salasana',
            'password_repeat' => 'Salasana uudestaan',
        ];
    }

    public function validateUsernameAvailable(string $attribute): void
    {
        if (in_array(mb_strtolower($this->username), User::RESERVED_USERNAMES, true)) {
            $this->addError($attribute, 'Tämä tunnus on varattu.');
        } elseif (User::usernameTaken($this->username)) {
            $this->addError($attribute, 'Tunnus on jo käytössä.');
        }
    }

    /**
     * Creates the user (and, through User::afterSave(), an empty profile).
     */
    public function signup(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->role = User::ROLE_STUDENT;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save(false) ? $user : null;
    }
}
