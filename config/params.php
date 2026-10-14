<?php

declare(strict_types=1);

return [
    'adminEmail' => getenv('ADMIN_EMAIL') ?: 'admin@amklogi.test',
    'senderEmail' => getenv('SENDER_EMAIL') ?: 'noreply@amklogi.test',
    'senderName' => 'AMKLogi',
    // Profile pictures are scaled to fit inside this many pixels (width and height).
    'avatarMaxSize' => 600,
    // Show a CAPTCHA on the contact form. null = automatic (enabled when GD or Imagick is available).
    'contactCaptcha' => null,
];
