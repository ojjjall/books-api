<?php

namespace App;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    // Reads an env var from $_ENV, $_SERVER, or getenv() — works locally AND on Railway
    private static function env(string $key, string $default = ''): string
    {
        $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return ($val === false || $val === null || $val === '') ? $default : (string) $val;
    }

    public static function get(): PDO
    {
        if (self::$pdo) {
            return self::$pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            self::env('DB_HOST', '127.0.0.1'),
            self::env('DB_PORT', '3306'),
            self::env('DB_NAME', 'books_api'),
            self::env('DB_CHARSET', 'utf8mb4')
        );

        try {
            self::$pdo = new PDO(
                $dsn,
                self::env('DB_USER', 'root'),
                self::env('DB_PASS', ''),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            error_log('[DB] ' . $e->getMessage());
            throw new \RuntimeException('Database connection failed', 500, $e);
        }

        return self::$pdo;
    }
}