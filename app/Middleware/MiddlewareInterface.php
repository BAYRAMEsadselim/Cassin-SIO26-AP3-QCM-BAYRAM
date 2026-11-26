<?php
declare(strict_types=1);

namespace App\Middleware;

/** Contrôle exécuté avant le contrôleur. S'il refuse la requête, il l'arrête (redirection ou erreur HTTP). */
interface MiddlewareInterface
{
    public function handle(): void;
}
