<?php

namespace App\Models\Repository;


use App\Models\Entity\Category;
use App\Models\Repository\Repository;


class CategoryRepository extends Repository
{

    protected const TABLE  = 'category';
    protected const ENTITY = Category::class;
    protected const ALLOWED_COLUMNS = Category::COLUMNS;


    public function findCategory(int $id): Category
    {
        /** @var Category */
        return $this->find($id);
    }

    public function findPaginatedbyCategory()
    {
        $queryCount = "select count(id) from category";

        $query = "select * from category order by id asc";

        [$categories, $paginatedquery] = $this->findPaginated($query, $queryCount, 8);


        return [$categories, $paginatedquery];
    }

    public function findPaginatedCategory(Category $category)
    {

        $queryCount = "SELECT count(category_id) FROM post_category WHERE category_id={$category->getId()}";

        $query = "SELECT p.*
            FROM post p
            JOIN post_category pc ON pc.post_id=p.id
            WHERE pc.category_id ={$category->getId()}
            ORDER by created_at DESC";

        [$posts, $paginatedquery] = $this->findPaginated($query, $queryCount, 8);

        return [$posts, $paginatedquery];
    }


    public function deleteCategory(int $id)
    {
        $this->delete($id);
    }

    public function updateCategory(Category $category): void
    {

        $this->updateEntity($category);
    }
    public function insertCategory(Category $category): int
    {
        return $this->insertEntity($category);
    }

    public function listCategorie(): array
    {
        $categories = $this->findAll();
        $results = [];
        foreach ($categories as $category) {
            $results[(int)$category->getId()] = $category->getName(); // <- pas de [...]
        }
        return $results;
    }
}
