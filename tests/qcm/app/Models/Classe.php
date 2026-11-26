<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DomainException;
use PDOException;

/**
 * Une classe existe pour une année donnée (RG5). Elle regroupe des étudiants
 * (classe_etudiant) et des couples matière + formateur (classe_matiere).
 */
final class Classe
{
    public static function all(): array
    {
        $sql = 'SELECT c.id, c.nom, c.annee,
                       (SELECT COUNT(*) FROM classe_etudiant ce WHERE ce.classe_id = c.id) AS nb_etudiants,
                       (SELECT COUNT(*) FROM classe_matiere cm WHERE cm.classe_id = c.id) AS nb_matieres
                FROM classe c ORDER BY c.annee DESC, c.nom';
        return Database::pdo()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT id, nom, annee FROM classe WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Peut lever PDOException (code 23000) si le couple nom + année existe déjà. */
    public static function create(string $nom, int $annee): int
    {
        $stmt = Database::pdo()->prepare('INSERT INTO classe (nom, annee) VALUES (?, ?)');
        $stmt->execute([$nom, $annee]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, string $nom, int $annee): void
    {
        $stmt = Database::pdo()->prepare('UPDATE classe SET nom = ?, annee = ? WHERE id = ?');
        $stmt->execute([$nom, $annee, $id]);
    }

    /**
     * Supprime la classe et ses affectations de matières.
     * Refusé si un QCM existe déjà pour cette classe.
     */
    public static function delete(int $id): void
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('DELETE FROM classe_matiere WHERE classe_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM classe WHERE id = ?')->execute([$id]);
            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() === '23000') {
                throw new DomainException('Suppression impossible : des QCM existent pour cette classe.');
            }
            throw $e;
        }
    }

    // ---------- Étudiants ----------

    public static function etudiants(int $classeId): array
    {
        $sql = 'SELECT u.id, u.nom, u.prenom, u.email
                FROM classe_etudiant ce JOIN utilisateur u ON u.id = ce.etudiant_id
                WHERE ce.classe_id = ? ORDER BY u.nom, u.prenom';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$classeId]);
        return $stmt->fetchAll();
    }

    /** Étudiants qui ne sont dans aucune classe de cette année. */
    public static function etudiantsDisponibles(int $annee): array
    {
        $sql = "SELECT u.id, u.nom, u.prenom, u.email
                FROM utilisateur u
                WHERE u.role = 'etudiant'
                  AND u.id NOT IN (SELECT ce.etudiant_id
                                   FROM classe_etudiant ce JOIN classe c ON c.id = ce.classe_id
                                   WHERE c.annee = ?)
                ORDER BY u.nom, u.prenom";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$annee]);
        return $stmt->fetchAll();
    }

    /** @throws DomainException si l'étudiant est déjà dans une classe de la même année */
    public static function ajouterEtudiant(int $classeId, int $etudiantId, int $annee): void
    {
        $ids = array_map(fn(array $e) => (int) $e['id'], self::etudiantsDisponibles($annee));
        if (!in_array($etudiantId, $ids, true)) {
            throw new DomainException("Cet étudiant est déjà dans une classe de {$annee}, ou n'est pas un étudiant.");
        }
        $stmt = Database::pdo()->prepare('INSERT INTO classe_etudiant (classe_id, etudiant_id) VALUES (?, ?)');
        $stmt->execute([$classeId, $etudiantId]);
    }

    public static function retirerEtudiant(int $classeId, int $etudiantId): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM classe_etudiant WHERE classe_id = ? AND etudiant_id = ?');
        $stmt->execute([$classeId, $etudiantId]);
    }

    // ---------- Matières et formateurs ----------

    public static function matieres(int $classeId): array
    {
        $sql = 'SELECT cm.id, m.libelle AS matiere, u.nom, u.prenom
                FROM classe_matiere cm
                JOIN matiere m ON m.id = cm.matiere_id
                JOIN utilisateur u ON u.id = cm.formateur_id
                WHERE cm.classe_id = ? ORDER BY m.libelle';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$classeId]);
        return $stmt->fetchAll();
    }

    /** Couples (matière, formateur) qui peuvent encore être ajoutés à la classe. */
    public static function affectationsPossibles(int $classeId): array
    {
        $sql = 'SELECT fm.formateur_id, fm.matiere_id, m.libelle AS matiere, u.nom, u.prenom
                FROM formateur_matiere fm
                JOIN matiere m ON m.id = fm.matiere_id
                JOIN utilisateur u ON u.id = fm.formateur_id
                WHERE fm.matiere_id NOT IN (SELECT matiere_id FROM classe_matiere WHERE classe_id = ?)
                ORDER BY m.libelle, u.nom, u.prenom';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$classeId]);
        return $stmt->fetchAll();
    }

    /** @throws DomainException */
    public static function ajouterMatiere(int $classeId, int $matiereId, int $formateurId): void
    {
        try {
            $stmt = Database::pdo()->prepare(
                'INSERT INTO classe_matiere (classe_id, matiere_id, formateur_id) VALUES (?, ?, ?)'
            );
            $stmt->execute([$classeId, $matiereId, $formateurId]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new DomainException(
                    "Affectation impossible : matière déjà présente dans la classe, ou formateur qui n'assure pas cette matière."
                );
            }
            throw $e;
        }
    }

    /** @throws DomainException si un QCM utilise déjà cette matière dans la classe */
    public static function retirerMatiere(int $classeId, int $classeMatiereId): void
    {
        try {
            $stmt = Database::pdo()->prepare('DELETE FROM classe_matiere WHERE id = ? AND classe_id = ?');
            $stmt->execute([$classeMatiereId, $classeId]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new DomainException('Impossible de retirer cette matière : des QCM l\'utilisent dans cette classe.');
            }
            throw $e;
        }
    }
}
