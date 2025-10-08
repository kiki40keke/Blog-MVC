<?php

declare(strict_types=1);

use App\Config\Config;

require dirname(__DIR__) . '/vendor/autoload.php';

// ----- Chargement config & environnement
Config::load();

// ----- Paramètres globaux
date_default_timezone_set(Config::get('timezone', 'UTC'));
session_start();

// ----- (Optionnel) Définir une constante BASE_URL pour tes vues
define('BASE_URL', rtrim(Config::get('base_url', '/'), '/'));

// ----- Connexion PDO
try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        Config::get('db_host'),
        Config::get('db_name')
    );

    $pdo = new PDO($dsn, Config::get('db_user'), Config::get('db_pass'), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>Erreur de connexion à la base</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</pre>";
    exit;
}

// ----- Mini “homepage” provisoire SANS routeur
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Blog MVC — Bootstrap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= BASE_URL ?>/css/app.css" rel="stylesheet">
</head>

<body>
    <main style="max-width:720px;margin:48px auto;font-family:system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;">
        <h1>Blog MVC — point d’entrée</h1>
        <p>Environnement chargé depuis <code>.env</code> ✅</p>
        <ul>
            <li><strong>BASE_URL</strong> : <code><?= BASE_URL ?></code></li>
            <li><strong>DB_NAME</strong> : <code><?= htmlspecialchars(Config::get('db_name')) ?></code></li>
            <li><strong>TIMEZONE</strong> : <code><?= htmlspecialchars(Config::get('timezone')) ?></code></li>
        </ul>

        <hr>

        <?php
        // Test DB simple (optionnel) — affiche le nombre de tables
        try {
            $tables = $pdo->query("SHOW TABLES")->fetchAll();
            echo "<p>Connexion DB OK ✅ — Tables trouvées : <strong>" . count($tables) . "</strong></p>";
        } catch (Throwable $t) {
            echo "<p>Connexion DB OK ✅ — mais impossible de lister les tables (aucune ou permissions).</p>";
        }
        ?>

        <p style="margin-top:24px;">
            Ce fichier est volontairement <strong>sans routeur</strong>. Tu pourras brancher ton Router plus tard.<br>
            Commence par créer tes dossiers <code>app/Views</code>, <code>app/Controllers</code>, etc.
        </p>
    </main>
</body>

</html>