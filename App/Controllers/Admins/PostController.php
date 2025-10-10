<?php

namespace App\Controllers\Admins;

use App\HTML\Form;
use App\Helpers\Date;
use App\Helpers\Text;
use App\Helpers\Upload;
use App\Core\Connection;
use App\Helpers\Session;
use App\Helpers\Hydrator;
use App\Models\Entity\Post;
use App\Validators\PostValidator;
use App\Controllers\BaseController;
use App\Models\Repository\PostRepository;
use App\Models\Repository\CategoryRepository;


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

    public function edit(int $id): string
    {
        $title = "Modification de l'article #$id";
        $active = 'articles';
        $link = $this->router->url('admin_posts');

        $pdo = Connection::getPDO();

        $table = new PostRepository($pdo);
        $post = $table->findPost($id);

        $table->hydratePost($post);
        $categoryTable = new CategoryRepository($pdo);
        $categories = $categoryTable->listCategorie();

        $errors = [];

        if (!empty($_POST)) {
            //dd($post->getId());
            //dd($categories);

            $data = $_POST + ['categories_id' => []] + $_FILES;

            $v = new PostValidator($data, $table, $categories, $post->getId());

            if ($v->validate()) {
                Hydrator::hydrate($post, $data, ['name', 'slug', 'content', 'created_at', 'image']);
                $idCategories = array_values(array_unique(
                    array_filter(array_map('intval', $data['categories_id']), fn($v) => $v > 0)
                ));

                $request = $table->updatePost($post, $idCategories);
                //dd($request);
                if ($request) {
                    Session::setFlash('success', "L’article #{$id} a bien été mis à jour ✅");

                    if (is_array($data['image']) && $data['image']['size'] > 0) {
                        $path = Upload::save($data['image'], $post->getImage());
                    }
                } else {
                    Session::setFlash('danger', "Une erreur est survenue lors de la mise à jour de l’article #{$id}.");
                }

                $table->hydratePost($post);
            } else {
                Session::setFlash('danger', "Impossible de mettre à jour l’article #{$id}.");
                $errors = $v->errors();
            }
        }

        $form = new Form($post, $errors);
        return $this->render('admins/post/edit', compact('form', 'categories', 'post', 'link', 'errors', 'title', 'active'));
    }

    public function new(): string
    {
        $title = "Nouvel article";
        $active = 'articles';
        $link = $this->router->url('admin_posts');

        $pdo = Connection::getPDO();

        $errors = [];
        $post = new Post();
        $post->setCreated_at(date('Y-m-d H:i:s'));
        $categoryTable = new CategoryRepository($pdo);
        $categories = $categoryTable->listCategorie();
        //dd($categories);
        if (!empty($_POST)) {

            $table = new PostRepository($pdo);
            $data = $_POST + ['categories_id' => []] + $_FILES;

            $v = new PostValidator($data, $table, $categories, $post->getId());

            if ($v->validate()) {

                $data['created_at'] = Date::normalizeCreatedAt($data['created_at']);
                Hydrator::hydrate($post, $data, ['name', 'slug', 'content', 'created_at', 'image']);
                $idCategories = array_values(array_unique(
                    array_filter(array_map('intval', $data['categories_id']), fn($v) => $v > 0)
                ));

                $id = $table->insertPost($post, $idCategories);
                if ($id > 0) {
                    Session::setFlash('success', 'L’article a bien été créé ✅');
                    if (is_array($data['image']) && $data['image']['size'] > 0) {
                        $path = Upload::save($data['image'], $post->getImage());
                    }
                } else {
                    Session::setFlash('danger', "Une erreur est survenue lors de la création de l’article.");
                }

                header('Location: ' . $link);
                exit;
            } else {
                $errors = $v->errors();
            }
        }

        $form = new Form($post, $errors);
        return $this->render('admins/post/new', compact('form', 'categories', 'post', 'link', 'errors', 'title', 'active'));
    }

    public function delete(int $id): void
    {
        $link = $this->router->url('admin_posts');

        $filemage = $_POST['image'] ?? null;
        $pdo = Connection::getPDO();

        $table = new PostRepository($pdo);
        //$v = Upload::deleteFile($filemage);


        if ($table->deletePost($id)) {
            Upload::deleteFile($filemage);
            Session::setFlash('success', "L’article #{$id} a été supprimé 🗑️");
        } else {
            Session::setFlash('error', "Une erreur est survenue lors de la suppression de l’article #{$id}.");
        }
        http_response_code(301);
        header('Location: ' . $link);
        exit();
    }
}
