<?php

namespace App\Controllers\Clients;

use App\Core\Connection;
use App\Controllers\BaseController;
use App\Models\Repository\PostRepository;

class PostController extends BaseController
{
    public function index(): string
    {
        $title = 'Accueil';
        $active = 'articles';

        $pdo = Connection::getPDO();

        //Pagination

        $table = new PostRepository($pdo);
        [$posts, $paginatedquery] = $table->findPaginatedPost();
        $link = $this->router->url('home');

        //fin


        return $this->render('clients/post/index', compact('title', 'posts', 'paginatedquery', 'link', 'active'));
    }

    public function show(string $slug, int $id): string
    {
        $pdo = Connection::getPDO();

        $table = new PostRepository($pdo);
        $post = $table->findPost($id);

        if ($post->getSlug() !== $slug) {
            $url = $this->router->url('post', ['slug' => $post->getSlug(), 'id' => $id]);
            http_response_code(301);
            header('Location: ' . $url);
            exit();
        }

        $table->hydratePost($post);

        $title = "Article {$post->getName()}";

        return $this->render('clients/post/show', compact('title', 'post'));
    }
}
