<?php

namespace App\Helpers;

final class URL
{
    public static function getInt(string $name, ?int $default = null): ?int
    {
        if (!isset($_GET[$name])) return $default;
        if ($_GET[$name] === 0) return 0;
        if (!filter_var($_GET[$name], FILTER_VALIDATE_INT)) {
            throw new \Exception("le parametre $name dans l'url n'est pas un entier");
        }
        return (int)$_GET[$name];
    }

    public static function getPositiveInt(string $name, ?int $default = null): ?int
    {
        $param = self::getInt($name, $default);
        if ($param !== null && $param <= 0) {
            throw new \Exception("le parametre $name dans l'url n'est pas un entier positif");
        }
        return $param;
    }
}
