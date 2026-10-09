<?php
declare(strict_types=1);

function database(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $databaseName = getenv('LOVEFILM_DB_NAME') ?: 'lovefilm';
    $databaseUser = getenv('LOVEFILM_DB_USER') ?: 'root';
    $databasePassword = getenv('LOVEFILM_DB_PASSWORD');
    $databaseHost = getenv('LOVEFILM_DB_HOST') ?: '127.0.0.1';

    if (!preg_match('/\A[A-Za-z0-9_]+\z/', $databaseName)) {
        throw new RuntimeException('The configured LoveFilm database name is invalid.');
    }

    $password = $databasePassword === false ? '' : $databasePassword;
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $databaseHost, $databaseName);

    try {
        $connection = new PDO($dsn, $databaseUser, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        error_log('LoveFilm database connection failed: ' . $exception->getMessage());
        throw new RuntimeException(
            'Unable to connect to the LoveFilm database. Import database.sql in phpMyAdmin and check your database settings.'
        );
    }

    return $connection;
}
