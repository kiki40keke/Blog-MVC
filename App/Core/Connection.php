<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use App\Config\Config;
use PDOException;

final class Connection
{
    private static ?PDO $pdo = null;

    public static function getPDO(): PDO
    {
        if (self::$pdo === null) {
            try {
                $host    = Config::get('db_host', 'localhost');
                $dbname  = Config::get('db_name', 'blog_mvc');
                $user    = Config::get('db_user', 'root');
                $pass    = Config::get('db_pass', '');
                $port    = (int) Config::get('db_port', 3306);
                $charset = Config::get('db_charset', 'utf8mb4');

                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $host,
                    $port,
                    $dbname,
                    $charset
                );

                self::$pdo = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                        PDO::MYSQL_ATTR_FOUND_ROWS   => true,
                    ]
                );
            } catch (PDOException $e) {
                die('❌ Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
