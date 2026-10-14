<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\models\SignupForm;
use app\models\User;
use app\tests\Support\Factory;

final class SignupFormTest extends \Codeception\Test\Unit
{
    private function form(array $attributes = []): SignupForm
    {
        return new SignupForm(array_merge([
            'username' => 'uusi_opiskelija',
            'email' => 'uusi@example.com',
            'password' => 'salasana123',
            'password_repeat' => 'salasana123',
        ], $attributes));
    }

    public function testSignupCreatesStudentWithProfile(): void
    {
        $user = $this->form()->signup();

        verify($user)->instanceOf(User::class);
        verify($user->role)->equals(User::ROLE_STUDENT);
        verify($user->validatePassword('salasana123'))->true();
        verify($user->profile)->notNull();
    }

    public function testPasswordsMustMatch(): void
    {
        $form = $this->form(['password_repeat' => 'something-else']);

        verify($form->signup())->null();
        verify($form->errors)->arrayHasKey('password_repeat');
    }

    public function testUsernameMustBeUniqueIgnoringCase(): void
    {
        Factory::user('Matti');
        $form = $this->form(['username' => 'mATTi']);

        verify($form->signup())->null();
        verify($form->getFirstError('username'))->equals('Tunnus on jo käytössä.');
    }

    public function testEmailMustBeUnique(): void
    {
        Factory::user('matti');
        $form = $this->form(['email' => 'matti@example.com']);

        verify($form->signup())->null();
        verify($form->errors)->arrayHasKey('email');
    }

    public function testReservedUsernamesAreRejected(): void
    {
        $form = $this->form(['username' => 'Login']);

        verify($form->signup())->null();
        verify($form->getFirstError('username'))->equals('Tämä tunnus on varattu.');
    }

    public function testUsernameMustBeUrlSafe(): void
    {
        foreach (["matti'--", 'ma tti', 'mätti', '<b>x</b>'] as $username) {
            $form = $this->form(['username' => $username]);

            verify($form->validate(['username']))->false();
        }
    }
}
