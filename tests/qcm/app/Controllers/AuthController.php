<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Security\Auth;
use App\Core\Controller;

final class AuthController extends Controller
{
    public function form(): void
    {
        if (Auth::user() !== null) {
            $this->redirect('/');
        }
        $this->render('auth/login', ['erreur' => null]);
    }

    public function login(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (Auth::login($email, $password)) {
            $this->redirect('/');
        }
        http_response_code(401);
        // Message volontairement vague : ne pas révéler si l'e-mail existe
        $this->render('auth/login', ['erreur' => 'Identifiants incorrects.']);
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }
}
