<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Security\Auth;

/** Exige un utilisateur connecté, sinon redirige vers la page de connexion. */
final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        if (Auth::user() === null) {
            header('Location: /login');
            exit;
        }
    }
}
