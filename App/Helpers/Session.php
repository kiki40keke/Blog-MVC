<?php

namespace App\Helpers;

class Session
{
    public static function setFlash(string $key, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]); // on supprime après lecture (flash = one shot)
        return $message;
    }
}
