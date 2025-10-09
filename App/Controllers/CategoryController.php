<?php

namespace App\Controllers;

use App\Core\Connection;
use App\Models\Repository\PostRepository;
use App\Models\Repository\CategoryRepository;

class CategoryController extends BaseController
{
    public function show(string $slug, int $id): string
    {
        $pdo = Connection::getPDO();
        $table = new CategoryRepository($pdo);
        $category = $table->findCategory($id);

        if ($category->getSlug() !== $slug) {
            $url = $this->router->url('category', ['slug' => $category->getSlug(), 'id' => $id]);
            http_response_code(301);
            header('Location: ' . $url);
            exit();
        }
        $title = "Category {$category->getName()}";
        //Pagination
        [$posts, $paginatedquery] = $table->findPaginatedCategory($category);
        //fin pagination
        $link = $this->router->url('category', ['id' => $category->getId(), 'slug' => $category->getSlug()]);
        return $this->render('clients/category/show', compact('title', 'posts', 'paginatedquery', 'link', 'category'));
    }
}
