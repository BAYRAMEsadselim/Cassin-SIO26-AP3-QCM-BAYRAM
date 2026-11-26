<?php
declare(strict_types=1);

namespace App\Security;

use App\Models\Utilisateur;

/** Authentification : connexion, déconnexion, utilisateur courant (stocké en session). */
final class Auth
{
    public static function start(): void
    {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }

    public static function login(string $email, string $password): bool
    {
        $user = Utilisateur::findByEmail($email);
        if ($user === null || !password_verify($password, $user['mot_de_passe_hash'])) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'     => (int) $user['id'],
            'nom'    => $user['nom'],
            'prenom' => $user['prenom'],
            'role'   => $user['role'],
        ];
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}
