<?php

namespace App\Controllers\Admins;

use App\Helpers\Text;
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

    public function show(int $id): string
    {
        $link = $this->router->url('admin_posts');

        $pdo = Connection::getPDO();

        $table = new PostRepository($pdo);
        $post = $table->findPost($id);
        $title = "Article " . Text::e($post->getName());
        $linkedit = $this->router->url('admin_post_edit', ['id' => $post->getId()]);
        $linkdelete = $this->router->url('admin_post_delete', ['id' => $post->getId()]);
        return $this->render('admins/post/show', compact('title', 'post', 'link', 'linkedit', 'linkdelete'));
    }
}
