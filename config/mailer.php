<?php

declare(strict_types=1);

/**
 * Mailer definition shared by the web and console applications.
 *
 * With MAILER_DSN set (Docker uses Mailpit: smtp://mailpit:1025) mail is sent over SMTP,
 * otherwise messages are written to runtime/mail for inspection.
 */
$dsn = getenv('MAILER_DSN') ?: null;

$mailer = [
    'class' => \yii\symfonymailer\Mailer::class,
    'useFileTransport' => $dsn === null,
    'viewPath' => '@app/mail',
];

if ($dsn !== null) {
    $mailer['transport'] = ['dsn' => $dsn];
}

return $mailer;
