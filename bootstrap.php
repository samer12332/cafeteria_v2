<?php

if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = __DIR__ . '/storage/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0777, true);
    }

    session_save_path($sessionPath);
    session_start();
}

$lines = file(__DIR__ . '/.env');

foreach ($lines as $line) {
    $line = trim($line);

    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }

    [$key, $value] = explode('=', $line, 2);

    $_ENV[trim($key)] = trim($value);
}

function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}
