<?php

namespace App\Config;

use Dotenv\Dotenv;

class Config
{
    private static array $settings = [];
    private static bool $loaded = false;

    public static function load(): void
    {
        if (self::$loaded) return;

        // Charge .env depuis la racine du projet
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        // ✅ Vérifie que certaines variables critiques existent
        $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER']);

        // Charge les valeurs dans le tableau de configuration
        self::$settings = [
            'db_host'    => $_ENV['DB_HOST']    ?? 'localhost',
            'db_name'    => $_ENV['DB_NAME']    ?? 'blog_mvc',
            'db_user'    => $_ENV['DB_USER']    ?? 'root',
            'db_pass'    => $_ENV['DB_PASS']    ?? '',
            'db_port'    => $_ENV['DB_PORT']    ?? '3306',
            'db_charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'base_url'   => rtrim($_ENV['BASE_URL'] ?? 'http://localhost/blog-mvc/public', '/'),
            'timezone'   => $_ENV['TIMEZONE']   ?? 'UTC',
        ];

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        return self::$settings[$key] ?? $default;
    }
}
