<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DomainException;

/**
 * Modèle de référence pour un CRUD avec verrou (RG1) :
 * une matière affectée à un formateur ou à une classe n'est plus modifiable ni supprimable.
 */
final class Matiere
{
    public static function all(): array
    {
        $sql = 'SELECT m.id, m.libelle,
                       (EXISTS (SELECT 1 FROM formateur_matiere fm WHERE fm.matiere_id = m.id)
                     OR EXISTS (SELECT 1 FROM classe_matiere cm WHERE cm.matiere_id = m.id)) AS verrouillee
                FROM matiere m ORDER BY m.libelle';
        return Database::pdo()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT id, libelle FROM matiere WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function estVerrouillee(int $id): bool
    {
        $sql = 'SELECT EXISTS (SELECT 1 FROM formateur_matiere WHERE matiere_id = :id)
                    OR EXISTS (SELECT 1 FROM classe_matiere WHERE matiere_id = :id2)';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute(['id' => $id, 'id2' => $id]);
        return (bool) $stmt->fetchColumn();
    }

    public static function create(string $libelle): int
    {
        $stmt = Database::pdo()->prepare('INSERT INTO matiere (libelle) VALUES (?)');
        $stmt->execute([$libelle]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, string $libelle): void
    {
        if (self::estVerrouillee($id)) {
            throw new DomainException('Cette matière est affectée : elle ne peut plus être modifiée.');
        }
        $stmt = Database::pdo()->prepare('UPDATE matiere SET libelle = ? WHERE id = ?');
        $stmt->execute([$libelle, $id]);
    }

    public static function delete(int $id): void
    {
        if (self::estVerrouillee($id)) {
            throw new DomainException('Cette matière est affectée : elle ne peut plus être supprimée.');
        }
        $stmt = Database::pdo()->prepare('DELETE FROM matiere WHERE id = ?');
        $stmt->execute([$id]);
    }
}
