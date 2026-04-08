<?php
/**
 * Database connection include.
 * Provides a shared $pdo instance via PDO.
 *
 * Supports multiple connection modes:
 *   1. MYSQL_URL or DATABASE_URL  (Railway-style connection string)
 *   2. MYSQL_HOST + MYSQL_DATABASE + MYSQL_USER + MYSQL_PASSWORD (Railway auto-injected)
 *   3. Individual DB_HOST, DB_NAME, DB_USER, DB_PASS env vars
 */
declare(strict_types=1);

if (!isset($pdo)) {
    $mysqlUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: '';

    if ($mysqlUrl !== '') {
        // Parse Railway-style mysql://user:pass@host:port/dbname
        $parts = parse_url($mysqlUrl);
        $dbHost = $parts['host'] ?? 'localhost';
        $dbPort = (int)($parts['port'] ?? 3306);
        $dbName = ltrim($parts['path'] ?? '/railway', '/');
        $dbUser = urldecode($parts['user'] ?? 'root');
        $dbPass = urldecode($parts['pass'] ?? '');
    } elseif (getenv('MYSQL_HOST')) {
        // Railway auto-injected individual variables
        $dbHost = getenv('MYSQL_HOST') ?: 'localhost';
        $dbPort = (int)(getenv('MYSQL_PORT') ?: 3306);
        $dbName = getenv('MYSQL_DATABASE') ?: 'railway';
        $dbUser = getenv('MYSQL_USER') ?: 'root';
        $dbPass = getenv('MYSQL_PASSWORD') ?: '';
    } else {
        // Manual / local env vars
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
        // Show connection target for debugging (no password exposed)
        echo '<h1>Database connection failed</h1>';
        echo '<p style="color:#666;">Could not connect to <code>' . htmlspecialchars("{$dbHost}:{$dbPort}/{$dbName}") . '</code> as <code>' . htmlspecialchars($dbUser) . '</code></p>';
        echo '<p style="color:#999;font-size:0.875rem;">Error: ' . htmlspecialchars($ex->getMessage()) . '</p>';
        echo '<p style="color:#999;font-size:0.875rem;">Check that MYSQL_URL or MYSQL_HOST variables are set in your Railway service.</p>';
        exit;
    }
}
