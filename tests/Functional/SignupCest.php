<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\models\User;
use app\tests\Support\FunctionalTester;

final class SignupCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('site/signup');
    }

    public function openSignupPage(FunctionalTester $I)
    {
        $I->see('Rekisteröidy', 'h1');
    }

    public function signupWithMismatchingPasswords(FunctionalTester $I)
    {
        $I->submitForm('#signup-form', [
            'SignupForm[username]' => 'uusi',
            'SignupForm[email]' => 'uusi@example.com',
            'SignupForm[password]' => 'salasana123',
            'SignupForm[password_repeat]' => 'salasana321',
        ]);
        $I->see('Salasanat eivät täsmää.');
        $I->dontSeeRecord(User::class, ['username' => 'uusi']);
    }

    public function signupLogsInAndOpensProfileEditor(FunctionalTester $I)
    {
        $I->submitForm('#signup-form', [
            'SignupForm[username]' => 'uusi',
            'SignupForm[email]' => 'uusi@example.com',
            'SignupForm[password]' => 'salasana123',
            'SignupForm[password_repeat]' => 'salasana123',
        ]);
        $I->seeRecord(User::class, ['username' => 'uusi', 'role' => User::ROLE_STUDENT]);
        $I->see('Kiitos rekisteröinnistä!');
        $I->see('Muokkaa profiilia', 'h1');
        $I->see('Kirjaudu ulos (uusi)');
    }
}
