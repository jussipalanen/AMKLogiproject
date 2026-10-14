<?php

declare(strict_types=1);

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'amklogi',
    'name' => 'AMKLogi',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'fi-FI',
    'sourceLanguage' => 'fi-FI',
    'timeZone' => getenv('APP_TIMEZONE') ?: 'Europe/Helsinki',
    'container' => [
        'singletons' => [
            \yii\mail\MailerInterface::class => require __DIR__ . '/mailer.php',
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@uploads' => dirname(__DIR__) . '/web/uploads',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => require __DIR__ . '/cookie_key.php',
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => \app\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'assetManager' => [
            // ?v=<mtime> on CSS/JS URLs so browsers pick up edited files immediately.
            'appendTimestamp' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'formatter' => [
            'dateFormat' => 'php:j.n.Y',
            'datetimeFormat' => 'php:j.n.Y H:i',
        ],
        'mailer' => \yii\mail\MailerInterface::class,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/url_rules.php',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // Debug toolbar and Gii are only reachable from these addresses. Docker live mode adds
    // the Docker network (requests from the host arrive through its gateway).
    $allowedIPs = array_map('trim', explode(',', getenv('DEV_ALLOWED_IPS') ?: '127.0.0.1,::1'));

    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        'allowedIPs' => $allowedIPs,
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        'allowedIPs' => $allowedIPs,
    ];
}

return $config;
