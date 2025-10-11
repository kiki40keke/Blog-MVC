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
}
