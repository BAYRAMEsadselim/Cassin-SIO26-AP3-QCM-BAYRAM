<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Matiere;
use App\Models\Utilisateur;
use DomainException;

/** Association d'une ou plusieurs matières à un formateur (table formateur_matiere). */
final class FormateurController extends Controller
{
    public function index(): void
    {
        $this->render('admin/formateurs/index', ['formateurs' => Utilisateur::formateursAvecMatieres()]);
    }

    public function edit(int $id): void
    {
        $formateur = $this->formateurOuErreur($id);
        $this->render('admin/formateurs/matieres', [
            'formateur' => $formateur,
            'matieres'  => Matiere::all(),
            'cochees'   => Utilisateur::matieresIds($id),
            'erreur'    => null,
        ]);
    }

    public function update(int $id): void
    {
        $formateur = $this->formateurOuErreur($id);

        // On ne garde que des identifiants de matières qui existent vraiment
        $valides = array_map(fn(array $m) => (int) $m['id'], Matiere::all());
        $envoyees = is_array($_POST['matieres'] ?? null) ? array_map('intval', $_POST['matieres']) : [];
        $ids = array_values(array_intersect($envoyees, $valides));

        try {
            Utilisateur::affecterMatieres($id, $ids);
        } catch (DomainException $e) {
            $this->render('admin/formateurs/matieres', [
                'formateur' => $formateur,
                'matieres'  => Matiere::all(),
                'cochees'   => Utilisateur::matieresIds($id),
                'erreur'    => $e->getMessage(),
            ]);
            return;
        }
        $this->flash('ok', 'Matières du formateur enregistrées.');
        $this->redirect('/admin/formateurs');
    }

    private function formateurOuErreur(int $id): array
    {
        $u = Utilisateur::find($id);
        if ($u === null || $u['role'] !== 'formateur') {
            http_response_code(404);
            exit('Formateur introuvable.');
        }
        return $u;
    }
}
