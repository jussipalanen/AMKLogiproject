<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\models\Profile;

final class ProfileTest extends \Codeception\Test\Unit
{
    public function testHomepageMustBeHttpLink(): void
    {
        $profile = new Profile(['homepage' => 'javascript:alert(1)']);
        verify($profile->validate(['homepage']))->false();

        $profile = new Profile(['homepage' => 'amk.fi']);
        verify($profile->validate(['homepage']))->true();
        verify($profile->homepage)->equals('https://amk.fi');
    }

    public function testBirthdayCannotBeInTheFuture(): void
    {
        $profile = new Profile(['birthday' => date('Y-m-d', strtotime('+1 year'))]);
        verify($profile->validate(['birthday']))->false();

        $profile = new Profile(['birthday' => '2001-05-04']);
        verify($profile->validate(['birthday']))->true();
    }

    public function testEmptyFieldsBecomeNull(): void
    {
        $profile = new Profile(['firstname' => '  ', 'gender' => '']);

        verify($profile->validate(['firstname', 'gender']))->true();
        verify($profile->firstname)->null();
        verify($profile->gender)->null();
    }

    public function testLabels(): void
    {
        $profile = new Profile(['firstname' => 'Liisa', 'surname' => 'Laaksonen', 'gender' => 'female']);

        verify($profile->fullName)->equals('Liisa Laaksonen');
        verify($profile->genderLabel)->equals('Nainen');
        verify($profile->avatarUrl)->null();
    }
}
