<?php
declare(strict_types=1);

namespace App\Security;

/** Autorisation : l'utilisateur connecté a-t-il telle permission ? */
final class Authorization
{
    public static function peut(Permission $permission): bool
    {
        $user = Auth::user();
        if ($user === null) {
            return false;
        }
        return in_array($permission, Permission::pourRole($user['role']), true);
    }
}
