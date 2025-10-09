<?php

declare(strict_types=1);

namespace App\Table\Exception;

use Exception;


class NotFoundException extends Exception
{
    public function __construct(string $table, int|string $id)
    {
        if (is_int($id)) {
            $message = "Aucun enregistrement ne correspond à l'id #{$id} dans la table '{$table}'";
        } else {
            $message = "Erreur lors de l’opération '{$id}' sur la table '{$table}'";
        }

        parent::__construct($message);
    }
}
