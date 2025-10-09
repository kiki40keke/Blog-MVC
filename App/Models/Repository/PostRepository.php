<?php

namespace App\Table;

use App\Models\Entity\Post;
use App\Models\Repository\Repository;


class PostRepository extends Repository
{
    protected const TABLE  = 'post';
    protected const ENTITY = Post::class;
    protected const ALLOWED_COLUMNS = Post::COLUMNS;
    public function findPost(int $id): Post
    {
        /** @var Post */
        return $this->find($id);
    }

    public function findPaginatedPost()
    {
        $queryCount = "select count(id) from post";

        $query = "select * from post order by created_at desc";

        [$posts, $paginatedquery] = $this->findPaginated($query, $queryCount, 8);


        return [$posts, $paginatedquery];
    }



    public function hydratePost(Post $post): void
    {
        $map = $this->fetchCategoriesByPostIds([$post->getId()]);
        $post->setCategories([]);
        foreach ($map[$post->getId()] ?? [] as $c) {
            $post->addCategory($c);
        }
    }

    public function deletePost(int $id): bool
    {
        try {
            return $this->delete($id);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function syncPostCategories(Post $post, array $categories): void
    {
        $postId = (int) $post->getId();

        // 1) Purge d'abord, même si $categories est vide
        $del = $this->pdo->prepare('DELETE FROM post_category WHERE post_id = ?');
        $del->execute([$postId]);

        // 2) Normalisation (ints uniques > 0)
        $categories = array_values(array_unique(
            array_filter(array_map('intval', (array)$categories), fn($v) => $v > 0)
        ));

        // 3) Rien à insérer ? on sort proprement
        if (empty($categories)) {
            return;
        }

        // Insertion multi-lignes
        $placeholders = implode(', ', array_fill(0, count($categories), '(?, ?)'));
        $sql = "INSERT INTO post_category (post_id, category_id) VALUES $placeholders";

        $params = [];
        foreach ($categories as $cid) {
            $params[] = $postId;
            $params[] = $cid;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }


    /**
     * Met à jour un Post à partir de l'objet Post
     */
    public function updatePost(Post $post, array $categories): bool
    {
        try {
            $this->pdo->beginTransaction();

            $this->updateEntity($post);
            $this->syncPostCategories($post, $categories);

            $this->pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            // log ou rethrow si besoin
            return false;
        }
    }



    public function insertPost(Post $post, array  $categories): int
    {
        try {
            $this->pdo->beginTransaction();
            $result = $this->insertEntity($post);
            $post->setId($result);
            $this->syncPostCategories($post, $categories);
            $this->pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            // log ou rethrow si besoin
            return 0;
        }
    }
}
