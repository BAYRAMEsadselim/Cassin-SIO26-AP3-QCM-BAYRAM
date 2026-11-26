<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $db = self::reglages();
            $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4";
            self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }

    /** @return array{host:string, name:string, user:string, pass:string} */
    private static function reglages(): array
    {
        $dir = __DIR__ . '/../../config/';
        if (is_file($dir . 'database.local.php')) {
            return require $dir . 'database.local.php';
        }
        // Ancien emplacement (première version du projet) : config/config.local.php avec une clé 'db'
        if (is_file($dir . 'config.local.php')) {
            $ancien = require $dir . 'config.local.php';
            if (isset($ancien['db'])) {
                return $ancien['db'];
            }
        }
        return require $dir . 'database.php';
    }
}
