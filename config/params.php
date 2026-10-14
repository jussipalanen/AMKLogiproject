<?php

declare(strict_types=1);

return [
    'adminEmail' => getenv('ADMIN_EMAIL') ?: 'admin@amklogi.test',
    'senderEmail' => getenv('SENDER_EMAIL') ?: 'noreply@amklogi.test',
    'senderName' => 'AMKLogi',
    // Profile pictures are scaled to fit inside this many pixels (width and height).
    'avatarMaxSize' => 600,
    // Album images: largest stored size and thumbnail size in pixels.
    'imageMaxSize' => 1600,
    'imageThumbSize' => 400,
    // Most images one album (and the unsorted images) can hold, and per upload request.
    'albumMaxImages' => (int) (getenv('ALBUM_MAX_IMAGES') ?: 100),
    'uploadMaxFiles' => (int) (getenv('UPLOAD_MAX_FILES') ?: 20),
    // Largest accepted image file in megabytes.
    'uploadMaxFileSizeMb' => 5,
    // Show a CAPTCHA on the contact form. null = automatic (enabled when GD or Imagick is available).
    'contactCaptcha' => null,
];
