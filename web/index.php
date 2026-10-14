<?php

declare(strict_types=1);

// Docker sets YII_ENV=prod and YII_DEBUG=0; a plain checkout runs in dev mode.
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV') ?: 'dev');
defined('YII_DEBUG') or define(
    'YII_DEBUG',
    getenv('YII_DEBUG') === false ? YII_ENV === 'dev' : filter_var(getenv('YII_DEBUG'), FILTER_VALIDATE_BOOL),
);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
