<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\tests\Support\Factory;
use app\tests\Support\FunctionalTester;

final class LoginFormCest
{
    public function _before(FunctionalTester $I)
    {
        Factory::user('matti');
        $I->amOnRoute('site/login');
    }

    public function openLoginPage(FunctionalTester $I)
    {
        $I->see('Kirjaudu sisään', 'h1');
    }

    public function loginWithEmptyCredentials(FunctionalTester $I)
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validations errors');
        $I->see('Tunnus ei voi olla tyhjä.');
        $I->see('Salasana ei voi olla tyhjä.');
    }

    public function loginWithWrongCredentials(FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'matti',
            'LoginForm[password]' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Väärä tunnus tai salasana.');
    }

    public function loginSuccessfullyOpensFrontPage(FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'matti',
            'LoginForm[password]' => Factory::PASSWORD,
        ]);
        $I->see('Kirjaudu ulos (matti)');
        $I->see('Tervetuloa AMKLogiin!', 'h1');
        $I->see('Hei, matti!');
        $I->dontSeeElement('form#login-form');
    }

    public function unreadCommentCountIsShownAfterLogin(FunctionalTester $I)
    {
        $matti = \app\models\User::findByUsername('matti');
        Factory::comment(Factory::user('liisa'), $matti, 'Moi!');

        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'matti',
            'LoginForm[password]' => Factory::PASSWORD,
        ]);
        $I->see('(1)', '.navbar .amk-unread');
        $I->see('1 uusi kommentti', '.amk-unread-summary');
    }
}
