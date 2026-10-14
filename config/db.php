<?php

declare(strict_types=1);

/**
 * Database connection.
 *
 * Defaults to a local SQLite file so the app runs without a database server.
 * Docker (see docker-compose.yml) points DB_DSN at MySQL instead.
 */
return [
    'class' => \yii\db\Connection::class,
    'dsn' => getenv('DB_DSN') ?: 'sqlite:@app/runtime/amklogi.sqlite',
    'username' => getenv('DB_USERNAME') ?: null,
    'password' => getenv('DB_PASSWORD') ?: null,
    'charset' => 'utf8mb4',
    'on afterOpen' => static function (\yii\base\Event $event): void {
        /** @var \yii\db\Connection $db */
        $db = $event->sender;

        if ($db->driverName === 'sqlite') {
            // SQLite ignores FOREIGN KEY constraints unless they are switched on per connection.
            $db->createCommand('PRAGMA foreign_keys = ON')->execute();
        }
    },
    'enableSchemaCache' => !YII_DEBUG,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
