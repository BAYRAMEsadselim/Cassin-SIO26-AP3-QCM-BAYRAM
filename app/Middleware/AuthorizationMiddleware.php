<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Security\Authorization;
use App\Security\Permission;

/** Exige une permission précise (voir Permission::pourRole). À placer après AuthMiddleware. */
final class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __construct(private Permission $permission)
    {
    }

    public function handle(): void
    {
        if (!Authorization::peut($this->permission)) {
            http_response_code(403);
            exit('Accès refusé.');
        }
    }
}
