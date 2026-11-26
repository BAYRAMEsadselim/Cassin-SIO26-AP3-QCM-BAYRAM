<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Security\Csrf;

/** Vérifie le jeton CSRF. Le routeur l'ajoute automatiquement à toutes les requêtes autres que GET. */
final class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        Csrf::verify();
    }
}
