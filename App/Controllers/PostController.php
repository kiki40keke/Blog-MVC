<?php

namespace App\Controllers;

class PostController extends BaseController
{
    public function index(): string
    {
        $title = 'Accueil';
        $home  = 'Blogast';
        return $this->render('clients/post/index', compact('title', 'home'));
    }
}
