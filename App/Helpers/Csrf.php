<?php

namespace App\Helpers;

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // À inclure dans chaque <form> côté vues
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Vérifie le token envoyé :
     * - via champ "csrf_token" (formulaires HTML)
     * - ou via header "X-CSRF-Token" (AJAX/JSON)
     */
    public static function verifyOrFail(): void
    {
        $session = $_SESSION['csrf_token'] ?? null;

        // 1) Formulaires classiques
        $fromBody = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? null;

        // 2) Requêtes AJAX/JSON
        $fromHeader = null;
        if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $fromHeader = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }

        $incoming = $fromBody ?? $fromHeader;

        if (!$session || !$incoming || !hash_equals($session, (string)$incoming)) {
            http_response_code(403);
            exit('CSRF token invalide ou manquant.');
        }

        // (Optionnel) token à usage unique
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
