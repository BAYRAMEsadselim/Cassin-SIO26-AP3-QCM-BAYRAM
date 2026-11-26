<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\MotCle;
use DomainException;
use PDOException;

final class MotCleController extends Controller
{
    public function index(): void
    {
        $this->render('admin/mots-cles/index', ['motsCles' => MotCle::all()]);
    }

    public function create(): void
    {
        $this->render('admin/mots-cles/form', ['motCle' => null, 'erreur' => null]);
    }

    public function store(): void
    {
        $libelle = $this->libelle();
        if ($libelle === null) {
            $this->render('admin/mots-cles/form', ['motCle' => null, 'erreur' => 'Libellé obligatoire (80 caractères maximum).']);
            return;
        }
        try {
            MotCle::create($libelle);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->render('admin/mots-cles/form', ['motCle' => null, 'erreur' => 'Ce mot-clé existe déjà.']);
                return;
            }
            throw $e;
        }
        $this->flash('ok', 'Mot-clé créé.');
        $this->redirect('/admin/mots-cles');
    }

    public function edit(int $id): void
    {
        $motCle = MotCle::find($id) ?? $this->introuvable();
        $this->render('admin/mots-cles/form', ['motCle' => $motCle, 'erreur' => null]);
    }

    public function update(int $id): void
    {
        $motCle = MotCle::find($id) ?? $this->introuvable();
        $libelle = $this->libelle();
        if ($libelle === null) {
            $this->render('admin/mots-cles/form', ['motCle' => $motCle, 'erreur' => 'Libellé obligatoire (80 caractères maximum).']);
            return;
        }
        try {
            MotCle::update($id, $libelle);
        } catch (DomainException $e) { // verrou RG2
            $this->render('admin/mots-cles/form', ['motCle' => $motCle, 'erreur' => $e->getMessage()]);
            return;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->render('admin/mots-cles/form', ['motCle' => $motCle, 'erreur' => 'Ce mot-clé existe déjà.']);
                return;
            }
            throw $e;
        }
        $this->flash('ok', 'Mot-clé modifié.');
        $this->redirect('/admin/mots-cles');
    }

    public function destroy(int $id): void
    {
        try {
            MotCle::delete($id);
            $this->flash('ok', 'Mot-clé supprimé.');
        } catch (DomainException $e) {
            $this->flash('erreur', $e->getMessage());
        }
        $this->redirect('/admin/mots-cles');
    }

    private function libelle(): ?string
    {
        $libelle = trim((string) ($_POST['libelle'] ?? ''));
        return ($libelle === '' || mb_strlen($libelle) > 80) ? null : $libelle;
    }

    private function introuvable(): never
    {
        http_response_code(404);
        exit('Mot-clé introuvable.');
    }
}
