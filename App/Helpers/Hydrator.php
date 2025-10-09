<?php

namespace App\Helpers;

class Hydrator
{
    /**
     * Hydrate un objet à partir d’un tableau (ex: $_POST)
     */
    public static function hydrate(object $entity, array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
                if (method_exists($entity, $method)) {
                    $entity->$method($data[$field]);
                }
            }
        }
    }
}
