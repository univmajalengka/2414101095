<?php
declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_NAME = 'capstone_wisata';
$DB_USER = 'root';
$DB_PASS = '';

/**
 * Create (and memoize) a PDO instance.
 */
function get_pdo(): PDO
{
    static $pdo;
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}
