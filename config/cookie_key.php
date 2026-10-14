<?php

declare(strict_types=1);

/**
 * Cookie validation key.
 *
 * Read from COOKIE_VALIDATION_KEY, or generated once and kept in runtime/ so that
 * no secret has to be committed to the repository.
 */
$key = getenv('COOKIE_VALIDATION_KEY');

if (is_string($key) && $key !== '') {
    return $key;
}

$file = dirname(__DIR__) . '/runtime/cookie_validation.key';

if (!is_file($file)) {
    file_put_contents($file, bin2hex(random_bytes(32)), LOCK_EX);
    chmod($file, 0600);
}

return trim((string) file_get_contents($file));
