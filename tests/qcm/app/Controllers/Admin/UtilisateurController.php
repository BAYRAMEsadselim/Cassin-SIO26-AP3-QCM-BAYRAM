<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Security\Auth;
use App\Core\Controller;
use App\Models\Utilisateur;
use PDOException;

final class UtilisateurController extends Controller
{
    public function index(): void
    {
        $this->render('admin/utilisateurs/index', [
            'utilisateurs' => Utilisateur::all(),
            'moiId'        => Auth::user()['id'],
        ]);
    }

    public function create(): void
    {
        $this->afficherFormulaire(null, ['nom' => '', 'prenom' => '', 'email' => '', 'role' => 'etudiant'], []);
    }

    public function store(): void
    {
        [$data, $erreurs] = $this->valider(null, false);
        if ($erreurs !== []) {
            http_response_code(422);
            $this->afficherFormulaire(null, $data, $erreurs);
            return;
        }
        Utilisateur::create($data);
        $this->flash('ok', 'Compte créé.');
        $this->redirect('/admin/utilisateurs');
    }

    public function edit(int $id): void
    {
        $utilisateur = Utilisateur::find($id) ?? $this->introuvable();
        $this->afficherFormulaire($utilisateur, $utilisateur, []);
    }

    public function update(int $id): void
    {
        $utilisateur = Utilisateur::find($id) ?? $this->introuvable();
        $estMoi = $id === Auth::user()['id'];

        [$data, $erreurs] = $this->valider($id, $estMoi, $utilisateur['role']);
        if ($erreurs !== []) {
            http_response_code(422);
            $this->afficherFormulaire($utilisateur, $data, $erreurs);
            return;
        }
        Utilisateur::update($id, $data);

        if ($estMoi) { // garder le nom affiché à jour dans la session
            $_SESSION['user']['nom'] = $data['nom'];
            $_SESSION['user']['prenom'] = $data['prenom'];
        }
        $this->flash('ok', 'Compte modifié.');
        $this->redirect('/admin/utilisateurs');
    }

    public function destroy(int $id): void
    {
        if ($id === Auth::user()['id']) {
            $this->flash('erreur', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->redirect('/admin/utilisateurs');
        }
        try {
            Utilisateur::delete($id);
            $this->flash('ok', 'Compte supprimé.');
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') { // clé étrangère : le compte est encore utilisé
                throw $e;
            }
            $this->flash('erreur', 'Ce compte est encore utilisé (questions, classes...). Suppression impossible.');
        }
        $this->redirect('/admin/utilisateurs');
    }

    /**
     * Valide le formulaire.
     * @param int|null $id        null à la création
     * @param bool     $estMoi    true si l'admin modifie son propre compte (le rôle ne change pas)
     * @return array{0: array, 1: string[]} [données, erreurs]
     */
    private function valider(?int $id, bool $estMoi, ?string $roleActuel = null): array
    {
        $data = [
            'nom'      => trim((string) ($_POST['nom'] ?? '')),
            'prenom'   => trim((string) ($_POST['prenom'] ?? '')),
            'email'    => strtolower(trim((string) ($_POST['email'] ?? ''))),
            'role'     => (string) ($_POST['role'] ?? ''),
            'password' => (string) ($_POST['password'] ?? ''),
        ];
        if ($estMoi && $roleActuel !== null) {
            $data['role'] = $roleActuel; // on ne se retire pas ses propres droits d'admin
        }

        $erreurs = [];
        if ($data['nom'] === '' || mb_strlen($data['nom']) > 80) {
            $erreurs[] = 'Nom obligatoire (80 caractères maximum).';
        }
        if ($data['prenom'] === '' || mb_strlen($data['prenom']) > 80) {
            $erreurs[] = 'Prénom obligatoire (80 caractères maximum).';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 190) {
            $erreurs[] = 'Adresse e-mail invalide.';
        } elseif (Utilisateur::emailPris($data['email'], $id)) {
            $erreurs[] = 'Cette adresse e-mail est déjà utilisée.';
        }
        if (!in_array($data['role'], Utilisateur::ROLES, true)) {
            $erreurs[] = 'Rôle invalide.';
        }
        $creation = $id === null;
        if (($creation || $data['password'] !== '') && strlen($data['password']) < 8) {
            $erreurs[] = 'Mot de passe : 8 caractères minimum.';
        }
        return [$data, $erreurs];
    }

    private function afficherFormulaire(?array $utilisateur, array $valeurs, array $erreurs): void
    {
        $this->render('admin/utilisateurs/form', [
            'utilisateur' => $utilisateur,
            'valeurs'     => $valeurs,
            'erreurs'     => $erreurs,
            'estMoi'      => $utilisateur !== null && (int) $utilisateur['id'] === Auth::user()['id'],
        ]);
    }

    private function introuvable(): never
    {
        http_response_code(404);
        exit('Compte introuvable.');
    }
}
