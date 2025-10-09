<?php

namespace App\Models\Repository;

use App\Models\Entity\User;
use App\Models\Repository\Repository;

class UserRepository extends Repository
{
    protected const TABLE  = 'user';
    protected const ENTITY = User::class;
    protected const ALLOWED_COLUMNS = User::COLUMNS;

    public function findUser(string $username): ?User
    {
        /** @var User */
        return $this->findBy('username', $username);
    }
}
