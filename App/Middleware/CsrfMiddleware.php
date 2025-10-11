<?php

namespace App\Middleware;

use App\Helpers\Csrf;

final class CsrfMiddleware
{
    /**
     * À appeler dans index.php avant le dispatch du routeur.
     * Filtre toutes les requêtes mutantes (POST/PUT/PATCH/DELETE).
     */
    public static function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // On laisse passer GET/HEAD/OPTIONS sans vérif CSRF
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        // Vérifie le token pour les méthodes qui modifient de l'état
        Csrf::verifyOrFail();
    }
}
