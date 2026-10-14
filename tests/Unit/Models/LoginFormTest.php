<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\models\LoginForm;
use app\tests\Support\Factory;
use Yii;

final class LoginFormTest extends \Codeception\Test\Unit
{
    protected function _after(): void
    {
        Yii::$app->user->logout();
    }

    public function testLoginNoUser(): void
    {
        $model = new LoginForm(['username' => 'not_existing_username', 'password' => 'not_existing_password']);

        verify($model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
    }

    public function testLoginWrongPassword(): void
    {
        Factory::user('matti');
        $model = new LoginForm(['username' => 'matti', 'password' => 'wrong_password']);

        verify($model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
        verify($model->errors)->arrayHasKey('password');
    }

    public function testLoginCorrectUpdatesLastLogin(): void
    {
        $user = Factory::user('matti');
        $model = new LoginForm(['username' => 'matti', 'password' => Factory::PASSWORD]);

        verify($model->login())->true();
        verify(Yii::$app->user->isGuest)->false();
        verify(Yii::$app->user->id)->equals($user->id);
        verify($model->errors)->arrayHasNotKey('password');

        $user->refresh();
        verify($user->last_login_at)->notNull();
    }
}
