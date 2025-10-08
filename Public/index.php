<?php

declare(strict_types=1);

use App\Core\Router;
use App\Config\Config;

require dirname(__DIR__) . '/vendor/autoload.php';

// Mesure du temps
define('DEBUG_TIME', microtime(true));

// ----- Chargement config & environnement
Config::load();

// ----- Paramètres globaux
date_default_timezone_set(Config::get('timezone', 'UTC'));
session_start();

// ----- BASE_URL (pour tes assets)
define('BASE_URL', rtrim(Config::get('base_url', '/'), '/') ?: '');

// ----- Whoops (debug)
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();



// ------------------------------------------------------------------
// Router : envoi vers les contrôleurs
// ------------------------------------------------------------------
$viewsPath = dirname(__DIR__) . '/App/Views';
$router    = new Router($viewsPath);

// IMPORTANT : basePath pour AltoRouter si ton app est dans /blog-mvc/public
// Exemple: /blog-mvc/public  -> on veut /blog-mvc
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$basePath  = $scriptDir === '/' ? '' : $scriptDir;
$router->setBasePath($basePath); // ← nécessite la petite méthode setBasePath() dans Router

// Déclaration des routes (Controller@method)
$router
    ->get('/', 'PostController@index', 'home')

    ->run();
