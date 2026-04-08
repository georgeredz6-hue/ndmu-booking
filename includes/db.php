<?php
/**
 * Database connection include.
 * Provides a shared $pdo instance via PDO.
 *
 * Supports two connection modes:
 *   1. MYSQL_URL or DATABASE_URL  (Railway-style connection string)
 *   2. Individual DB_HOST, DB_NAME, DB_USER, DB_PASS env vars
 */
declare(strict_types=1);

if (!isset($pdo)) {
    $mysqlUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: '';

    if ($mysqlUrl !== '') {
        // Parse Railway-style mysql://user:pass@host:port/dbname
        $parts = parse_url($mysqlUrl);
        $dbHost = $parts['host'] ?? 'localhost';
        $dbPort = $parts['port'] ?? 3306;
        $dbName = ltrim($parts['path'] ?? '/railway', '/');
        $dbUser = $parts['user'] ?? 'root';
        $dbPass = $parts['pass'] ?? '';
    } else {
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbPort = (int)(getenv('DB_PORT') ?: 3306);
        $dbName = getenv('DB_NAME') ?: 'univ_book';
        $dbUser = getenv('DB_USER') ?: 'root';
        $dbPass = getenv('DB_PASS') ?: '';
    }

    try {
        $pdo = new PDO(
            "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4",
            $dbUser,
            $dbPass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    } catch (PDOException $ex) {
        http_response_code(500);
        echo '<h1>Database connection failed</h1>';
        exit;
    }
}
