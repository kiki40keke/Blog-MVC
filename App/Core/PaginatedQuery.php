<?php

namespace App;

use \PDO;
use \Exception;
use App\Helpers\URL;
use App\Core\Connection;


class PaginatedQuery
{

    private string $query;
    private string $queryCount;
    private ?PDO $pdo = null;
    private int $perPage = 12;
    private ?int $count = null;
    private ?array $items = null;

    public function __construct(string $query, string $queryCount, int $perPage = 12, ?PDO $pdo = null)
    {
        $this->pdo = $pdo ?: Connection::getPDO();
        $this->queryCount = $queryCount;
        $this->perPage = $perPage;
        $this->query = $query;
    }
    private function getCountQuery(): int
    {
        if ($this->count === null) {
            $this->count = (int)$this->pdo->query($this->queryCount)->fetch(PDO::FETCH_NUM)[0];
        }
        return    (int)ceil($this->count / $this->perPage);
    }

    public function getItems(string $classMapping): array
    {
        if ($this->items === null) {
            $currentPage = $this->getCurrentPage();
            $pages = $this->getCountQuery();

            if ($currentPage > $pages) {
                throw new \Exception('Numéro de page invalide');
            }
            $offset = $this->perPage * ($currentPage - 1);
            $query = $this->pdo->query($this->query . "
             LIMIT  {$this->perPage} OFFSET {$offset}");
            $this->items = $query->fetchAll(PDO::FETCH_CLASS, $classMapping);
        }
        return $this->items;
    }

    public function getPreviousLink(string $link): string
    {
        $disabled = ' ';
        $currentPage = $this->getCurrentPage();
        $l = $link;

        if ($currentPage > 2) {
            $l = $link .  '?page=' . ($currentPage - 1);
        }
        if ($currentPage <= 1) {
            $disabled .= 'disabled';
        }
        $li = '<li class="page-item' . $disabled . '">';
        $a = '<a class="page-link" href="' . $l . '">Previous</a> </li>';
        return $li . $a;
    }

    public function getNextLink($link)
    {
        $pages = $this->getCountQuery();
        $disabled = ' ';
        $currentPage = $this->getCurrentPage();
        $l = $link;
        $l = $link .  '?page=' . ($currentPage + 1);
        if ($currentPage >= $pages) {
            $disabled .= 'disabled';
        }
        $li = '<li class="page-item' . $disabled . '">';
        $a = '<a class="page-link" href="' . $l . '">Next</a></li>';
        return $li . $a;
    }

    private function getCurrentPage(): int
    {
        return URL::getPositiveInt('page', 1);
    }
}
