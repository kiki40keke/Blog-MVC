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

        self::$settings = [
            'db_host'  => $_ENV['DB_HOST']   ?? 'localhost',
            'db_name'  => $_ENV['DB_NAME']   ?? 'blog_mvc',
            'db_user'  => $_ENV['DB_USER']   ?? 'root',
            'db_pass'  => $_ENV['DB_PASS']   ?? '',
            'base_url' => $_ENV['BASE_URL']  ?? 'http://localhost/blog-mvc/public',
            'timezone' => $_ENV['TIMEZONE']  ?? 'UTC',
        ];

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        return self::$settings[$key] ?? $default;
    }
}
