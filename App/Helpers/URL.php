<?php

namespace App\Helpers;

final class URL
{
    private static array $query = [];

    public static function setQuery(array $query): void
    {
        self::$query = $query;
    }

    public static function getInt(string $name, ?int $default = null): ?int
    {
        $query = self::$query ?: $_GET;

        if (!isset($query[$name])) return $default;
        if ($query[$name] === 0 || $query[$name] === '0') return 0;
        if (!filter_var($query[$name], FILTER_VALIDATE_INT)) {
            throw new \Exception("le parametre $name dans l'url n'est pas un entier");
        }
        return (int)$query[$name];
    }

    public static function getPositiveInt(string $name, ?int $default = null): ?int
    {
        $param = self::getInt($name, $default);
        if ($param !== null && $param <= 0) {
            throw new \Exception("le parametre $name dans l'url n'est pas un entier positif");
        }
        return $param;
    }

    public static function isAdminUrl(?string $uri = null): bool
    {
        $uri  = $uri ?? ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);
        // Correction ici : match seulement /admin ou /admin/...
        return preg_match('#^/admin(/|$)#', $path) === 1;
    }
}