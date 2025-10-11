<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\Routes;
use App\Config\Config;
use App\Middleware\CsrfMiddleware;

require dirname(__DIR__) . '/vendor/autoload.php';

// Mesure du temps
define('DEBUG_TIME', microtime(true));

// ----- Chargement config & environnement
Config::load();

// ----- Paramètres globaux
define('APP_TIMEZONE', Config::get('timezone', 'UTC'));
date_default_timezone_set(APP_TIMEZONE);
session_set_cookie_params([
    'httponly' => true,
    'secure'   => isset($_SERVER['HTTPS']), // true en prod HTTPS
    'samesite' => 'Lax',                    // 'Strict' si compatible
]);
session_start();

// ----- BASE_URL (pour tes assets)
define('BASE_URL', rtrim(Config::get('base_url', '/'), '/'));
// Dossier racine du projet (par rapport à index.php)
define('ROOT_DIR', dirname(__DIR__));

// Dossier public
define('PUBLIC_DIR', ROOT_DIR . '/public');

// Dossier images
define('IMG_DIR', PUBLIC_DIR . '/img');

// Dossier images de posts
define('POST_IMG_DIR', IMG_DIR . '/post');





// ----- Whoops (debug)
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


CsrfMiddleware::handle();

// ------------------------------------------------------------------
// Router : envoi vers les contrôleurs
// ------------------------------------------------------------------
$viewsPath = dirname(__DIR__) . '/App/Views';
$router    = new Router($viewsPath);

// IMPORTANT : basePath pour AltoRouter si ton app est dans /blog-mvc/public
// Exemple: /blog-mvc/public  -> on veut /blog-mvc
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$basePath  = $scriptDir === '/' ? '' : $scriptDir;
// Enregistrement des routes
Routes::register($router, $basePath);
