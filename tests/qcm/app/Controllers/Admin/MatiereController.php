<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Matiere;
use DomainException;
use PDOException;

final class MatiereController extends Controller
{
    public function index(): void
    {
        $this->render('admin/matieres/index', ['matieres' => Matiere::all()]);
    }

    public function create(): void
    {
        $this->render('admin/matieres/form', ['matiere' => null, 'erreur' => null]);
    }

    public function store(): void
    {
        $libelle = trim((string) ($_POST['libelle'] ?? ''));
        if ($libelle === '' || mb_strlen($libelle) > 120) {
            $this->render('admin/matieres/form', ['matiere' => null, 'erreur' => 'Libellé obligatoire (120 caractères maximum).']);
            return;
        }
        try {
            Matiere::create($libelle);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { // doublon (UNIQUE)
                $this->render('admin/matieres/form', ['matiere' => null, 'erreur' => 'Cette matière existe déjà.']);
                return;
            }
            throw $e;
        }
        $this->flash('ok', 'Matière créée.');
        $this->redirect('/admin/matieres');
    }

    public function edit(int $id): void
    {
        $matiere = Matiere::find($id) ?? $this->introuvable();
        $this->render('admin/matieres/form', ['matiere' => $matiere, 'erreur' => null]);
    }

    public function update(int $id): void
    {
        $matiere = Matiere::find($id) ?? $this->introuvable();
        $libelle = trim((string) ($_POST['libelle'] ?? ''));
        if ($libelle === '' || mb_strlen($libelle) > 120) {
            $this->render('admin/matieres/form', ['matiere' => $matiere, 'erreur' => 'Libellé obligatoire (120 caractères maximum).']);
            return;
        }
        try {
            Matiere::update($id, $libelle);
        } catch (DomainException $e) { // verrou RG1
            $this->render('admin/matieres/form', ['matiere' => $matiere, 'erreur' => $e->getMessage()]);
            return;
        }
        $this->flash('ok', 'Matière modifiée.');
        $this->redirect('/admin/matieres');
    }

    public function destroy(int $id): void
    {
        try {
            Matiere::delete($id);
            $this->flash('ok', 'Matière supprimée.');
        } catch (DomainException $e) {
            $this->flash('erreur', $e->getMessage());
        }
        $this->redirect('/admin/matieres');
    }

    private function introuvable(): never
    {
        http_response_code(404);
        exit('Matière introuvable.');
    }
}
