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
use App\Models\Entity\Category;
use App\Validators\PostValidator;
use App\Controllers\BaseController;
use App\Validators\CategoryValidator;
use App\Models\Repository\PostRepository;
use App\Models\Repository\CategoryRepository;

class CategoryController extends BaseController
{

    public function index(): string
    {
        $title = 'Liste des categories';
        $active = 'categories';
        $pdo = Connection::getPDO();
        $table = new CategoryRepository($pdo);
        [$categories, $paginatedquery] = $table->findPaginatedbyCategory();
        $link = $this->router->url('admin_categories');
        return $this->render('admins/category/index', compact('title', 'categories', 'paginatedquery', 'link', 'active'));
    }

    public function new(): string
    {
        $link = $this->router->url('admin_categories');


        $errors = [];
        $category = new Category();

        if (!empty($_POST)) {
            //dd($post->getId());
            $pdo = Connection::getPDO();

            $table = new CategoryRepository($pdo);

            $v = new CategoryValidator($_POST, $table);

            if ($v->validate()) {

                //dd($created_at);

                Hydrator::hydrate($category, $_POST, ['name', 'slug']);

                $id = $table->insertCategory($category);
                // dd($id);
                Session::setFlash('success', 'la categorie a bien été créé ✅');
                header('Location: ' . $link);
                exit;
            } else {
                $errors = $v->errors();
            }
        }

        $form = new Form($category, $errors);
        $title = "Nouvelle categorie";
        $active = 'categories';
        return $this->render('admins/category/new', compact('title', 'form', 'errors', 'link', 'active'));
    }

    public function edit(int $id): string
    {
        $link = $this->router->url('admin_categories');

        $pdo = Connection::getPDO();

        $table = new CategoryRepository($pdo);
        $category = $table->findCategory($id);
        $errors = [];
        if (!empty($_POST)) {
            $v = new CategoryValidator($_POST, $table, $category->getId());
            if ($v->validate()) {
                Hydrator::hydrate($category, $_POST, ['name', 'slug']);

                $table->updateCategory($category);
                Session::setFlash('success', "L’article #{$id} a bien été mis à jour ✅");
            } else {
                Session::setFlash('danger', "Impossible de mettre à jour l’article #{$id}.");
                $errors = $v->errors();
            }
        }
        $form = new Form($category, $errors);
        $title = "Modification de la categorie #$id";
        $active = 'categories';
        return $this->render('admins/category/edit', compact('title', 'form', 'errors', 'link', 'active'));
    }

    public function delete(int $id): void
    {
        $link = $this->router->url('admin_categories');

        $pdo = Connection::getPDO();

        $table = new CategoryRepository($pdo);
        $table->deleteCategory($id);
        Session::setFlash('success', "La categorie #{$id} a été supprimé 🗑️");
        http_response_code(301);
        header('Location: ' . $link);
        exit();
    }
}
