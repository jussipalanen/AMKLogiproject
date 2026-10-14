<?php

declare(strict_types=1);

$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
$db['dsn'] = getenv('TEST_DB_DSN') ?: 'sqlite:@app/runtime/test.sqlite';
$db['enableSchemaCache'] = false;

return $db;
