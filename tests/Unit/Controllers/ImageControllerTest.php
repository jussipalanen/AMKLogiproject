<?php

declare(strict_types=1);

namespace app\tests\Unit\Controllers;

use app\controllers\ImageController;

final class ImageControllerTest extends \Codeception\Test\Unit
{
    public function testReturnUrlMustBeALocalPath(): void
    {
        foreach (['/', '/matti/albums/3', '/matti/images/5?x=1'] as $url) {
            verify(ImageController::isLocalPath($url))->true();
        }

        foreach (['//evil.example', "/\t/evil.example", "/\n/evil.example", '/\\evil.example', 'https://evil.example', '', 'evil'] as $url) {
            verify(ImageController::isLocalPath($url))->false();
        }
    }
}
