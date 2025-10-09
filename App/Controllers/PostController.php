<?php

namespace App\Controllers;

use App\Core\Connection;
use App\Models\Repository\PostRepository;

class PostController extends BaseController
{
    public function index(): string
    {
        $title = 'Accueil';
        $active = 'articles';

        $title = "Mon blog";
        $pdo = Connection::getPDO();

        //Pagination

        $table = new PostRepository($pdo);
        [$posts, $paginatedquery] = $table->findPaginatedPost();
        $link = $this->router->url('home');

        //fin


        return $this->render('clients/post/index', compact('title', 'posts', 'paginatedquery', 'link', 'active'));
    }
}
