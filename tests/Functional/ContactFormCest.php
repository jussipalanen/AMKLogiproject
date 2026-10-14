<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\tests\Support\FunctionalTester;

final class ContactFormCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('site/contact');
    }

    public function openContactPage(FunctionalTester $I)
    {
        $I->see('Ota yhteyttä', 'h1');
    }

    public function submitEmptyForm(FunctionalTester $I)
    {
        $I->submitForm('#contact-form', []);
        $I->expectTo('see validations errors');
        $I->see('Ota yhteyttä', 'h1');
        $I->see('Nimi ei voi olla tyhjä.');
        $I->see('Sähköpostiosoite ei voi olla tyhjä.');
        $I->see('Aihe ei voi olla tyhjä.');
        $I->see('Viesti ei voi olla tyhjä.');
    }

    public function submitFormWithIncorrectEmail(FunctionalTester $I)
    {
        $I->submitForm('#contact-form', [
            'ContactForm[name]' => 'tester',
            'ContactForm[email]' => 'tester.email',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
        ]);
        $I->expectTo('see that email address is wrong');
        $I->see('Sähköpostiosoite on virheellinen sähköpostiosoite.');
        $I->dontSee('Nimi ei voi olla tyhjä.');
    }

    public function submitFormSuccessfully(FunctionalTester $I)
    {
        $I->submitForm('#contact-form', [
            'ContactForm[name]' => 'tester',
            'ContactForm[email]' => 'tester@example.com',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
        ]);
        $I->seeEmailIsSent();
        $I->see('Kiitos yhteydenotostasi! Vastaamme sinulle mahdollisimman pian.');
    }
}
