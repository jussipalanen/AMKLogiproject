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
    'POST <username:[A-Za-z0-9_-]+>/comments/delete' => 'comment/delete',
    'POST <username:[A-Za-z0-9_-]+>/comments' => 'comment/create',
    'gii' => 'gii',
    'debug' => 'debug',
    '<username:[A-Za-z0-9_-]+>' => 'user/view',
];
