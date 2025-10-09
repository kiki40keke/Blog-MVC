<?php

namespace App\Models\Entity;

class User
{
    private int $id;
    private string $username = '';
    private string $password = '';
    public const COLUMNS = ['username', 'password'];


    public function getId(): int
    {
        return $this->id;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
