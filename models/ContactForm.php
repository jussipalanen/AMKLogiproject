<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\captcha\Captcha;
use yii\mail\MailerInterface;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public string $name = '';
    public string $email = '';
    public string $subject = '';
    public string $body = '';
    public string $verifyCode = '';

    /**
     * Like the original app, the CAPTCHA is shown only when an image library is available.
     * `params['contactCaptcha']` can force it on or off.
     */
    public static function captchaEnabled(): bool
    {
        $setting = Yii::$app->params['contactCaptcha'] ?? null;

        if ($setting !== null) {
            return (bool) $setting;
        }

        try {
            Captcha::checkRequirements();

            return true;
        } catch (InvalidConfigException) {
            return false;
        }
    }

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            [['name', 'email', 'subject', 'body'], 'trim'],
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            [['name', 'subject'], 'string', 'max' => 128],
            ['body', 'string', 'max' => 5000],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha', 'when' => static fn (): bool => self::captchaEnabled()],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Nimi',
            'email' => 'Sähköpostiosoite',
            'subject' => 'Aihe',
            'body' => 'Viesti',
            'verifyCode' => 'Varmistuskoodi',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param MailerInterface $mailer the mailer component.
     * @param string $email the target email address.
     * @param string $senderEmail the sender email address.
     * @param string $senderName the sender name.
     *
     * @return bool whether the model passes validation.
     */
    public function contact(MailerInterface $mailer, string $email, string $senderEmail, string $senderName): bool
    {
        if ($this->validate()) {
            $mailer->compose()
                ->setTo($email)
                ->setFrom([$senderEmail => $senderName])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();

            return true;
        }

        return false;
    }
}
