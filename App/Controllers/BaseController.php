<?php

namespace App\Controllers;

use App\Core\Router;

abstract class BaseController
{
    protected Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    protected function render(string $view, array $data = [], string $layout = 'layouts/default.php'): string
    {
        return $this->router->render($view, $data, $layout);
    }
}
