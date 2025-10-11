<?php

namespace App\Helpers;

use App\Helpers\Session;

class Auth
{
    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user']) && isset($_SESSION['user']['id']);
    }

    public static function user(): ?array
    {
        return self::isLoggedIn() ? $_SESSION['user'] : null;
    }

    public static function requireLogin($router): void
    {
        $redirect = $router->url('login');
        $flashMessage = 'Veuillez vous connecter pour accéder à cette page.';
        if (!self::isLoggedIn()) {
            Session::setFlash('danger', $flashMessage);
            header('Location: ' . $redirect);
            exit;
        }
    }

    public static function requireGuest(string $redirect = '/admin'): void
    {
        if (self::isLoggedIn()) {
            header('Location: ' . $redirect);
            exit;
        }
    }
}
