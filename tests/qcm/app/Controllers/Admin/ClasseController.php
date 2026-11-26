<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Classe;
use DomainException;
use PDOException;

final class ClasseController extends Controller
{
    public function index(): void
    {
        $this->render('admin/classes/index', ['classes' => Classe::all()]);
    }

    public function create(): void
    {
        $this->render('admin/classes/form', [
            'classe' => null,
            'valeurs' => ['nom' => '', 'annee' => date('Y')],
            'erreur' => null,
        ]);
    }

    public function store(): void
    {
        [$nom, $annee, $erreur] = $this->valider();
        if ($erreur === null) {
            try {
                $id = Classe::create($nom, $annee);
                $this->flash('ok', 'Classe créée. Ajoutez maintenant des étudiants et des matières.');
                $this->redirect('/admin/classes/' . $id);
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                $erreur = 'Cette classe existe déjà pour cette année.';
            }
        }
        $this->render('admin/classes/form', [
            'classe' => null, 'valeurs' => ['nom' => $nom, 'annee' => $annee], 'erreur' => $erreur,
        ]);
    }

    /** Détail d'une classe : étudiants et matières/formateurs. */
    public function show(int $id): void
    {
        $classe = Classe::find($id) ?? $this->introuvable();
        $this->render('admin/classes/show', [
            'classe'       => $classe,
            'etudiants'    => Classe::etudiants($id),
            'disponibles'  => Classe::etudiantsDisponibles((int) $classe['annee']),
            'matieres'     => Classe::matieres($id),
            'affectations' => Classe::affectationsPossibles($id),
        ]);
    }

    public function edit(int $id): void
    {
        $classe = Classe::find($id) ?? $this->introuvable();
        $this->render('admin/classes/form', ['classe' => $classe, 'valeurs' => $classe, 'erreur' => null]);
    }

    public function update(int $id): void
    {
        $classe = Classe::find($id) ?? $this->introuvable();
        [$nom, $annee, $erreur] = $this->valider();
        if ($erreur === null) {
            try {
                Classe::update($id, $nom, $annee);
                $this->flash('ok', 'Classe modifiée.');
                $this->redirect('/admin/classes/' . $id);
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                $erreur = 'Cette classe existe déjà pour cette année.';
            }
        }
        $this->render('admin/classes/form', [
            'classe' => $classe, 'valeurs' => ['nom' => $nom, 'annee' => $annee], 'erreur' => $erreur,
        ]);
    }

    public function destroy(int $id): void
    {
        try {
            Classe::delete($id);
            $this->flash('ok', 'Classe supprimée.');
        } catch (DomainException $e) {
            $this->flash('erreur', $e->getMessage());
        }
        $this->redirect('/admin/classes');
    }

    // ---------- Étudiants ----------

    public function ajouterEtudiant(int $id): void
    {
        $classe = Classe::find($id) ?? $this->introuvable();
        try {
            Classe::ajouterEtudiant($id, (int) ($_POST['etudiant_id'] ?? 0), (int) $classe['annee']);
            $this->flash('ok', 'Étudiant ajouté.');
        } catch (DomainException $e) {
            $this->flash('erreur', $e->getMessage());
        }
        $this->redirect('/admin/classes/' . $id);
    }

    public function retirerEtudiant(int $id, int $etudiantId): void
    {
        Classe::retirerEtudiant($id, $etudiantId);
        $this->flash('ok', 'Étudiant retiré de la classe.');
        $this->redirect('/admin/classes/' . $id);
    }

    // ---------- Matières + formateur ----------

    public function ajouterMatiere(int $id): void
    {
        Classe::find($id) ?? $this->introuvable();
        // La liste déroulante envoie "idFormateur:idMatiere"
        $parts = explode(':', (string) ($_POST['affectation'] ?? ''));
        if (count($parts) !== 2 || !ctype_digit($parts[0]) || !ctype_digit($parts[1])) {
            $this->flash('erreur', 'Choisissez une matière et son formateur.');
            $this->redirect('/admin/classes/' . $id);
        }
        try {
            Classe::ajouterMatiere($id, (int) $parts[1], (int) $parts[0]);
            $this->flash('ok', 'Matière ajoutée à la classe.');
        } catch (DomainException $e) {
            $this->flash('erreur', $e->getMessage());
        }
        $this->redirect('/admin/classes/' . $id);
    }

    public function retirerMatiere(int $id, int $classeMatiereId): void
    {
        try {
            Classe::retirerMatiere($id, $classeMatiereId);
            $this->flash('ok', 'Matière retirée de la classe.');
        } catch (DomainException $e) {
            $this->flash('erreur', $e->getMessage());
        }
        $this->redirect('/admin/classes/' . $id);
    }

    /** @return array{0:string, 1:int, 2:?string} nom, année, message d'erreur */
    private function valider(): array
    {
        $nom = trim((string) ($_POST['nom'] ?? ''));
        $annee = (int) ($_POST['annee'] ?? 0);
        if ($nom === '' || mb_strlen($nom) > 80) {
            return [$nom, $annee, 'Nom obligatoire (80 caractères maximum).'];
        }
        if ($annee < 2000 || $annee > 2100) {
            return [$nom, $annee, 'Année invalide (entre 2000 et 2100).'];
        }
        return [$nom, $annee, null];
    }

    private function introuvable(): never
    {
        http_response_code(404);
        exit('Classe introuvable.');
    }
}
