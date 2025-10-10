<?php

namespace App\Controllers\Admins;

use App\Core\Connection;
use App\Controllers\BaseController;
use App\Models\Repository\PostRepository;

class PostController extends BaseController
{
    public function index(): string
    {
        $title = 'Liste des articles';
        $active = 'articles';
        $pdo = Connection::getPDO();

        $table = new PostRepository($pdo);
        [$posts, $paginatedquery] = $table->findPaginatedPost();
        //fin

        $link = $this->router->url('admin_posts');
        return $this->render('admins/post/index', compact('title', 'posts', 'paginatedquery', 'link', 'active'));
    }
}
