<?php

namespace App\Validators;

use App\Models\Repository\UserRepository;

class UserValidator extends ValidatorBase
{
    public function __construct(array $data, UserRepository $Repository)
    {
        parent::__construct($data, $Repository);

        $v = $this->validator;

        $v->labels([
            'username'       => 'Nom d\'utilisateur',
            'password'       => 'Mot de passe',
        ]);

        $v->rule('required', ['username', 'password']);
        $v->rule('validLogin', 'login', 'username', 'password')
            ->message('Pseudo ou mot de passe incorrect.');
    }
}
