<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\tests\Support\Factory;
use app\tests\Support\FunctionalTester;

final class ProfileCest
{
    public function guestIsSentToLogin(FunctionalTester $I)
    {
        $I->amOnRoute('profile/update');
        $I->see('Kirjaudu sisään', 'h1');
    }

    public function unknownUserPageIsNotFound(FunctionalTester $I)
    {
        $I->amOnPage('/ei-ole-olemassa');
        $I->seeResponseCodeIs(404);
    }

    public function editProfileAndSeeItOnProfilePage(FunctionalTester $I)
    {
        $I->amLoggedInAs(Factory::user('liisa'));
        $I->amOnRoute('profile/update');
        $I->submitForm('#profile-form', [
            'Profile[firstname]' => 'Liisa',
            'Profile[surname]' => 'Laaksonen',
            'Profile[gender]' => 'female',
            'Profile[city]' => '<b>Kotka</b>',
            'Profile[homepage]' => 'https://example.com',
        ]);
        $I->see('Profiili tallennettu.');

        $I->amOnPage('/liisa');
        $I->see('Liisa', '.profile-details');
        $I->see('Laaksonen', '.profile-details');
        $I->see('Nainen');
        $I->see('<b>Kotka</b>');
        $I->seeLink('https://example.com', 'https://example.com');
    }

    public function javascriptHomepageIsRejected(FunctionalTester $I)
    {
        $I->amLoggedInAs(Factory::user('liisa'));
        $I->amOnRoute('profile/update');
        $I->submitForm('#profile-form', ['Profile[homepage]' => 'javascript:alert(1)']);
        $I->see('Kotisivu on virheellinen URL.');
    }

    public function nonImageAvatarIsRejected(FunctionalTester $I)
    {
        $I->amLoggedInAs(Factory::user('liisa'));
        $I->amOnRoute('profile/update');
        $I->attachFile('#avatarform-avatar', 'not-an-image.jpg');
        $I->submitForm('#avatar-form', []);
        $I->see('Sallittuja ovat vain tiedostot');
    }
}
