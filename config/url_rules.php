<?php

declare(strict_types=1);

/**
 * Pretty URLs. Profile pages live at /<username> like in the original AMKLogi,
 * so that rule must stay last (see also User::RESERVED_USERNAMES).
 */
return [
    '' => 'site/index',
    'login' => 'site/login',
    'logout' => 'site/logout',
    'register' => 'site/signup',
    'contact' => 'site/contact',
    'about' => 'site/about',
    'users' => 'user/index',
    'profile' => 'profile/update',
    'profile/avatar' => 'profile/avatar',
    'albums/new' => 'album/create',
    'albums/<id:\d+>/edit' => 'album/update',
    'POST albums/<id:\d+>/delete' => 'album/delete',
    'images/upload' => 'image/upload',
    'POST images/move' => 'image/move',
    'POST images/delete' => 'image/delete',
    'POST images/<id:\d+>/avatar' => 'image/avatar',
    'POST images/<id:\d+>/caption' => 'image/caption',
    '<username:[A-Za-z0-9_-]+>/albums' => 'album/index',
    '<username:[A-Za-z0-9_-]+>/albums/unsorted' => 'album/unsorted',
    '<username:[A-Za-z0-9_-]+>/albums/<id:\d+>' => 'album/view',
    '<username:[A-Za-z0-9_-]+>/images/<id:\d+>' => 'image/view',
    'POST <username:[A-Za-z0-9_-]+>/comments/delete' => 'comment/delete',
    'POST <username:[A-Za-z0-9_-]+>/comments' => 'comment/create',
    'gii' => 'gii',
    'debug' => 'debug',
    '<username:[A-Za-z0-9_-]+>' => 'user/view',
];
