<?php

namespace App\Core;

class Router
{
    private string $viewsPath;
    private \AltoRouter $router;

    public function __construct(string $viewsPath)
    {
        $this->viewsPath = rtrim($viewsPath, DIRECTORY_SEPARATOR);
        $this->router    = new \AltoRouter();
    }

    // Déclare les routes (comme avant)
    public function get(string $url, string $target, ?string $name = null): self
    {
        $this->router->map('GET', $url, $target, $name);
        return $this;
    }

    public function post(string $url, string $target, ?string $name = null): self
    {
        $this->router->map('POST', $url, $target, $name);
        return $this;
    }

    public function match(string $url, string $target, ?string $name = null): self
    {
        $this->router->map('GET|POST', $url, $target, $name);
        return $this;
    }

    /**
     * Lance le dispatch.
     * Cible attendue : "PostController@index" (ou FQCN "App\Controllers\PostController@index")
     */
    public function run(): void
    {
        $match = $this->router->match();

        if ($match === false || empty($match['target'])) {
            // 404 -> contrôleur d’erreur si dispo, sinon vue 404 en secours
            $this->callController('App\\Controllers\\ErrorController', 'notFound', []);
            return;
        }

        $target = $match['target'];
        $params = array_values($match['params'] ?? []);

        // Autorise aussi un callable direct si jamais
        if (is_callable($target)) {
            echo call_user_func_array($target, $params);
            return;
        }

        if (!is_string($target) || strpos($target, '@') === false) {
            throw new \RuntimeException('Cible de route invalide. Attendu "Controller@method".');
        }

        [$controller, $method] = explode('@', $target, 2);

        // Ajoute le namespace si nécessaire
        if (strpos($controller, '\\') === false) {
            $controller = 'App\\Controllers\\' . $controller;
        }

        $this->callController($controller, $method, $params);
    }

    private function callController(string $controller, string $method, array $params): void
    {
        if (!class_exists($controller)) {
            $this->renderRaw404("Contrôleur introuvable : {$controller}");
            return;
        }

        $instance = new $controller($this); // on injecte le router

        if (!method_exists($instance, $method)) {
            $this->renderRaw404("Méthode introuvable : {$controller}::{$method}()");
            return;
        }

        $output = $instance->$method(...$params);

        // Un contrôleur peut retourner une chaîne (HTML complet) ou rien (si echo déjà fait)
        if (is_string($output)) {
            echo $output;
        }
    }

    // Pour générer des URLs nommées
    public function url(string $name, array $params = []): string
    {
        return $this->router->generate($name, $params);
    }

    // Helper utilisé par les contrôleurs pour rendre une vue DANS le layout
    public function render(string $view, array $data = [], string $layout = 'layouts/default.php'): string
    {
        $viewFile = $this->viewsPath . DIRECTORY_SEPARATOR . trim($view, '/\\') . '.php';
        if (!is_file($viewFile)) {
            return $this->render('layouts/404'); // fallback sur votre vue 404 si vous préférez
        }

        extract($data, EXTR_SKIP);

        ob_start();
        $router  = $this;
        require $viewFile;
        $content = ob_get_clean();

        ob_start();
        $router  = $this; // dispo dans le layout (pour $router->url())
        require $this->viewsPath . DIRECTORY_SEPARATOR . $layout;
        return ob_get_clean();
    }

    private function renderRaw404(string $message = 'Page introuvable'): void
    {
        http_response_code(404);
        // Si vous avez une vue 404 : echo $this->render('layouts/404');
        echo $this->render('layouts/404', ['message' => $message]);
    }

    public function setBasePath(string $basePath): void
    {
        $this->router->setBasePath($basePath);
    }
}
