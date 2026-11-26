<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DomainException;
use PDOException;

final class Utilisateur
{
    public const ROLES = ['admin', 'formateur', 'etudiant'];

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM utilisateur WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        $sql = 'SELECT id, nom, prenom, email, role FROM utilisateur ORDER BY role, nom, prenom';
        return Database::pdo()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT id, nom, prenom, email, role FROM utilisateur WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Vrai si l'e-mail est déjà utilisé par un autre compte que $exceptId. */
    public static function emailPris(string $email, ?int $exceptId = null): bool
    {
        $stmt = Database::pdo()->prepare('SELECT id FROM utilisateur WHERE email = ? AND id <> ?');
        $stmt->execute([$email, $exceptId ?? 0]);
        return $stmt->fetch() !== false;
    }

    /** @param array{nom:string, prenom:string, email:string, role:string, password:string} $d */
    public static function create(array $d): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO utilisateur (nom, prenom, email, mot_de_passe_hash, role) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $d['nom'], $d['prenom'], $d['email'],
            password_hash($d['password'], PASSWORD_DEFAULT),
            $d['role'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    /** Si $d['password'] est vide, le mot de passe actuel est conservé. */
    public static function update(int $id, array $d): void
    {
        if ($d['password'] !== '') {
            $stmt = Database::pdo()->prepare(
                'UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, role = ?, mot_de_passe_hash = ? WHERE id = ?'
            );
            $stmt->execute([
                $d['nom'], $d['prenom'], $d['email'], $d['role'],
                password_hash($d['password'], PASSWORD_DEFAULT), $id,
            ]);
            return;
        }
        $stmt = Database::pdo()->prepare(
            'UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, role = ? WHERE id = ?'
        );
        $stmt->execute([$d['nom'], $d['prenom'], $d['email'], $d['role'], $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM utilisateur WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ---------- Formateurs et leurs matières (table formateur_matiere) ----------

    /** Liste des formateurs avec leurs matières sous forme de texte ("Algo, BDD"). */
    public static function formateursAvecMatieres(): array
    {
        $sql = "SELECT u.id, u.nom, u.prenom, u.email,
                       GROUP_CONCAT(m.libelle ORDER BY m.libelle SEPARATOR ', ') AS matieres
                FROM utilisateur u
                LEFT JOIN formateur_matiere fm ON fm.formateur_id = u.id
                LEFT JOIN matiere m ON m.id = fm.matiere_id
                WHERE u.role = 'formateur'
                GROUP BY u.id, u.nom, u.prenom, u.email
                ORDER BY u.nom, u.prenom";
        return Database::pdo()->query($sql)->fetchAll();
    }

    /** @return int[] identifiants des matières du formateur */
    public static function matieresIds(int $formateurId): array
    {
        $stmt = Database::pdo()->prepare('SELECT matiere_id FROM formateur_matiere WHERE formateur_id = ?');
        $stmt->execute([$formateurId]);
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    /**
     * Remplace les matières du formateur par $ids.
     * Un retrait est refusé si la matière est déjà affectée à une classe avec ce formateur.
     * @param int[] $ids
     * @throws DomainException
     */
    public static function affecterMatieres(int $formateurId, array $ids): void
    {
        $pdo = Database::pdo();
        $actuels = self::matieresIds($formateurId);
        $ajouts = array_diff($ids, $actuels);
        $retraits = array_diff($actuels, $ids);

        $pdo->beginTransaction();
        try {
            $ins = $pdo->prepare('INSERT INTO formateur_matiere (formateur_id, matiere_id) VALUES (?, ?)');
            foreach ($ajouts as $matiereId) {
                $ins->execute([$formateurId, $matiereId]);
            }
            $del = $pdo->prepare('DELETE FROM formateur_matiere WHERE formateur_id = ? AND matiere_id = ?');
            foreach ($retraits as $matiereId) {
                $del->execute([$formateurId, $matiereId]);
            }
            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() === '23000') {
                throw new DomainException(
                    'Retrait impossible : une des matières retirées est déjà affectée à une classe avec ce formateur.'
                );
            }
            throw $e;
        }
    }
}
