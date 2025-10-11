<?php

namespace App\Controllers;

use App\Core\Router;
use App\Helpers\URL;
use App\Helpers\Auth;

abstract class BaseController
{
    protected Router $router;
    protected bool $isAdmin = false;
    protected array $menuAvecUrls = [];
    protected string $home = '';

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->initializeMenu();
    }

    /**
     * Initialise le menu et la logique admin.
     */
    private function initializeMenu(): void
    {
        // Vérifie si on est sur une URL admin
        $this->isAdmin = URL::isAdminUrl();

        if ($this->isAdmin) {
            // Exige connexion
            Auth::requireLogin($this->router);

            $this->home = $this->router->url('admin_posts');
            $this->menuAvecUrls = [
                'articles' => [
                    'label' => 'Articles',
                    'url' => $this->router->url('admin_posts')
                ],
                'categories' => [
                    'label' => 'Catégories',
                    'url' => $this->router->url('admin_categories')
                ],
                'users' => [
                    'label' => 'Déconnexion',
                    'url' => $this->router->url('logout')
                ]
            ];
        } else {
            $this->home = $this->router->url('home');
            $this->menuAvecUrls = [
                'articles' => [
                    'label' => 'Articles',
                    'url' => $this->router->url('home')
                ]
            ];
        }
    }

    /**
     * Rendu d’une vue avec données et layout.
     *
     * @param string $view
     * @param array $data
     * @param string $layout
     * @return string
     */
    protected function render(string $view, array $data = [], string $layout = 'layouts/default.php'): string
    {
        // Injecte les données globales pour les vues
         $data = array_merge($data, [
            'menuAvecUrls' => $this->menuAvecUrls,
            'home' => $this->home,
            'isAdmin' => $this->isAdmin
        ]);

        return $this->router->render($view, $data, $layout);
    }
}
