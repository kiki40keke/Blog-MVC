<?php

declare(strict_types=1);

namespace App\Models\Repository;

use PDO;
use stdClass;
use App\PaginatedQuery;
use App\Models\Entity\Post;
use InvalidArgumentException;
use App\Models\Entity\Category;
use App\Table\Exception\NotFoundException;

abstract class Repository
{
    protected PDO $pdo;

    /** Nom de la table (chaque classe fille doit le définir) */
    protected const TABLE  = '';
    /** Classe d’entité à hydrater (chaque classe fille doit le définir) */
    protected const ENTITY = stdClass::class;
    /** Nom de la clé primaire (par défaut "id") */
    protected const ID_COL = 'id';
    protected const ALLOWED_COLUMNS = [];


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = sprintf(
            'SELECT * FROM %s',
            static::TABLE
        );

        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::ENTITY);

        return $stmt->fetchAll();
    }


    /**
     * Récupère une ligne par son ID ou lance une exception si rien n’est trouvé
     */
    public function find(int $id): object
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = :id LIMIT 1',
            static::TABLE,
            static::ID_COL
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::ENTITY);

        $entity = $stmt->fetch();
        if ($entity === false) {
            throw new NotFoundException(static::TABLE, $id);
        }
        return $entity;
    }

    /**
     * Recherche une ligne par une colonne arbitraire (autorisée) et retourne un objet ou lève une exception si rien n'est trouvé.
     *
     * @param string $column
     * @param mixed $value
     * @return object
     * @throws NotFoundException
     */
    public function findBy(string $column, mixed $value): ?object
    {
        // Vérifier que la colonne est autorisée
        if (!in_array($column, static::ALLOWED_COLUMNS, true) && $column !== static::ID_COL) {
            throw new InvalidArgumentException("Colonne '$column' non autorisée.");
        }

        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = :value LIMIT 1',
            static::TABLE,
            $column
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['value' => $value]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::ENTITY);

        $entity = $stmt->fetch();

        // Soit on retourne null si pas trouvé
        return $entity === false ? null : $entity;
    }


    public function exists(array $columns, ?int $excludeId = null): bool
    {
        if (empty($columns)) {
            throw new InvalidArgumentException('Le tableau de colonnes ne peut pas être vide.');
        }

        $whereParts = [];
        $params     = [];

        foreach ($columns as $col => $val) {
            if (!in_array($col, static::ALLOWED_COLUMNS, true) && $col !== static::ID_COL) {
                return false;
            }
            $whereParts[] = "$col = :$col";
            $params[$col] = $val;
        }

        if ($excludeId !== null) {
            $whereParts[]        = static::ID_COL . " != :excludeId";
            $params['excludeId'] = $excludeId;
        }

        $sql = sprintf(
            "SELECT COUNT(%s) FROM %s WHERE %s",
            static::ID_COL,
            static::TABLE,
            implode(' AND ', $whereParts)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }



    /**
     * Variante qui retourne null au lieu d’une exception
     */
    public function findOrNull(int $id): ?object
    {
        try {
            return $this->find($id);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function findOne(array $conditions, array $select = ['*']): ?array
    {
        // Sécurité colonnes:
        foreach (array_keys($conditions) as $col) {
            if (!in_array($col, static::ALLOWED_COLUMNS, true) && $col !== static::ID_COL) {
                return null;
            }
        }
        foreach ($select as $col) {
            if ($col !== '*' && !in_array($col, static::ALLOWED_COLUMNS, true) && $col !== static::ID_COL) {
                return null;
            }
        }

        $whereParts = [];
        $params     = [];
        foreach ($conditions as $col => $val) {
            $whereParts[] = "$col = :$col";
            $params[$col] = $val;
        }

        $sql = sprintf(
            "SELECT %s FROM %s WHERE %s LIMIT 1",
            $select === ['*'] ? '*' : implode(', ', $select),
            static::TABLE,
            implode(' AND ', $whereParts)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ?: null;
    }





    protected function findPaginated($query, $queryCount, $perPage): array
    {
        $paginatedquery = new PaginatedQuery($query, $queryCount, $perPage);

        $posts = $paginatedquery->getItems(Post::class);
        $this->hydratePosts($posts);



        return [$posts, $paginatedquery];
    }
    protected function fetchCategoriesByPostIds(array $postIds): array
    {
        $postIds = array_values(array_unique(array_filter($postIds, 'is_numeric')));
        if (empty($postIds)) {
            return [];
        }

        // Construire les placeholders : ?, ?, ?
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));

        $sql = "
        SELECT c.*, pc.post_id
        FROM post_category pc
        JOIN category c ON c.id = pc.category_id
        WHERE pc.post_id IN ($placeholders)
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($postIds);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Category::class);

        $byPostId = [];
        /** @var Category $cat */
        foreach ($stmt->fetchAll() as $cat) {
            $byPostId[$cat->getPostId()][] = $cat;
        }
        return $byPostId;
    }

    public function hydratePosts(array $posts): void
    {
        if (empty($posts)) {
            return;
        }

        $postsById = [];
        foreach ($posts as $p) {
            $postsById[$p->getId()] = $p;
        }

        $catsByPostId = $this->fetchCategoriesByPostIds(array_keys($postsById));

        foreach ($catsByPostId as $postId => $cats) {
            foreach ($cats as $c) {
                $postsById[$postId]->addCategory($c);
            }
        }
    }

    public function delete(int $id): bool
    {
        $sql = sprintf(
            'DELETE FROM %s WHERE %s = :id',
            static::TABLE,
            static::ID_COL
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() === 0) {
            return false;
            throw new NotFoundException(static::TABLE, $id);
        }
        return true;
    }

    /**
     * Met à jour un enregistrement par son ID.
     * Toutes les erreurs (champs vides, colonnes interdites, id manquant)
     * lèvent NotFoundException pour rester cohérent avec find()/delete().
     *
     * @throws NotFoundException
     */
    public function update(int $id, array $fields): void
    {
        // champs vides -> même exception pour cohérence
        if (empty($fields)) {
            throw new NotFoundException(static::TABLE, $id);
        }

        // Si une whitelist est définie, refuser les colonnes hors liste
        if (!empty(static::ALLOWED_COLUMNS)) {
            $invalid = array_diff(array_keys($fields), static::ALLOWED_COLUMNS);
            if (!empty($invalid)) {
                // une seule exception partout -> NotFoundException
                throw new NotFoundException(static::TABLE, $id);
            }
        }

        // Construire "SET col = :set_col" en placeholders
        $setParts = [];
        $params   = [];
        foreach ($fields as $col => $val) {
            $ph = 'set_' . $col; // évite collision avec :id
            $setParts[] = sprintf('%s = :%s', $col, $ph);
            $params[$ph] = $val;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :id',
            static::TABLE,
            implode(', ', $setParts),
            static::ID_COL
        );

        $stmt = $this->pdo->prepare($sql);
        $params['id'] = $id;
        $stmt->execute($params);

        // Zéro ligne affectée -> id introuvable OU valeurs identiques selon driver
        // On reste cohérent: on lève NotFoundException
        if ($stmt->rowCount() === 0) {
            throw new NotFoundException(static::TABLE, $id);
        }
    }
    public function updateEntity(object $entity): void
    {
        if (empty(static::ALLOWED_COLUMNS)) {
            throw new \LogicException("ALLOWED_COLUMNS must be defined in " . static::class);
        }

        $fields = [];

        foreach (static::ALLOWED_COLUMNS as $col) {
            $getter = 'get' . ucfirst($col);
            if (method_exists($entity, $getter)) {
                $value = $entity->$getter();
                if ($value !== null) {
                    $fields[$col] = $value;
                }
            }
        }

        if (method_exists($entity, 'getId')) {
            $id = $entity->getId();
        } else {
            throw new \LogicException(get_class($entity) . " must have getId()");
        }

        $this->update($id, $fields);
    }

    protected function insert(array $fields): int
    {
        if (empty($fields)) {
            throw new \RuntimeException("Impossible d'insérer une ligne vide dans " . static::TABLE);
        }

        if (!empty(static::ALLOWED_COLUMNS)) {
            $invalid = array_diff(array_keys($fields), static::ALLOWED_COLUMNS);
            if (!empty($invalid)) {
                throw new \RuntimeException("Colonnes non autorisées: " . implode(', ', $invalid));
            }
        }

        $columns      = implode(', ', array_keys($fields));
        $placeholders = implode(', ', array_map(fn($c) => ':' . $c, array_keys($fields)));
        $sql          = "INSERT INTO " . static::TABLE . " ($columns) VALUES ($placeholders)";

        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute($fields)) {
            throw new \RuntimeException("Erreur lors de l'insertion dans " . static::TABLE);
        }

        return (int) $this->pdo->lastInsertId(); // id AUTO_INCREMENT géré par la DB
    }

    /**
     * Insert par objet (symétrique de updateEntity)
     * - Utilise ALLOWED_COLUMNS
     * - Ignore 'id' si présent (auto-incrément)
     */
    public function insertEntity(object $entity): int
    {
        if (empty(static::ALLOWED_COLUMNS)) {
            throw new \LogicException("ALLOWED_COLUMNS must be defined in " . static::class);
        }

        $fields = [];
        foreach (static::ALLOWED_COLUMNS as $col) {
            if ($col === 'id') {
                continue; // jamais insérer la clé auto-incrémentée
            }
            $getter = 'get' . ucfirst($col); // ex: "created_at" -> "getCreated_at"
            if (method_exists($entity, $getter)) {
                $value = $entity->$getter();
                if ($value !== null) {
                    $fields[$col] = $value;
                }
            }
        }

        return $this->insert($fields);
    }
}
