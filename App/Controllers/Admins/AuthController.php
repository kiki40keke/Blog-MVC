<?php

namespace App\Controllers\Admins;

use App\HTML\Form;
use App\Helpers\Auth;
use App\Core\Connection;
use App\Helpers\Session;
use App\Models\Entity\User;
use App\Validators\UserValidator;
use App\Controllers\BaseController;
use App\Models\Repository\UserRepository;

class AuthController extends BaseController
{

    public function login(): string
    {
        Auth::requireGuest();


        $pdo = Connection::getPDO();
        $user = new User();
        $errors = [];

        if (!empty($_POST)) {
            $table = new UserRepository($pdo);
            $data = $_POST;
            $v = new UserValidator($data, $table);
            $user->setUsername($data['username']);

            if ($v->validate()) {
                $find = $table->findUser($data['username']);
                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id'       => $find->getId(),
                    'username' => $find->getUsername()
                ];
                Session::setFlash('success', 'Bienvenue ' . $find->getUsername());
                header('Location: ' . $this->router->url('admin_posts'));
                exit;
            } else {
                $errors = $v->errors();
            }
        }
        $form = new Form($user, $errors);
        $title = 'Connexion';
        return $this->render('admins/auth/login', compact('form', 'title', 'errors'));
    }
    public function logout(): void
    {
        unset($_SESSION['user']);

        Session::setFlash('success', 'Vous êtes bien déconnecté.');
        header('Location: ' . $this->router->url('login'));
        exit;
    }
}
