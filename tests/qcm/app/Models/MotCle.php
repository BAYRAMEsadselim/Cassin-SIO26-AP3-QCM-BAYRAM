<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DomainException;
use PDOException;

/** CRUD des mots-clés avec le verrou RG2 : un mot-clé rattaché à une question n'est plus modifiable ni supprimable. */
final class MotCle
{
    public static function all(): array
    {
        $sql = 'SELECT k.id, k.libelle,
                       (SELECT COUNT(*) FROM question_mot_cle q WHERE q.mot_cle_id = k.id) AS nb_questions
                FROM mot_cle k ORDER BY k.libelle';
        return Database::pdo()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT id, libelle FROM mot_cle WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function estVerrouille(int $id): bool
    {
        $stmt = Database::pdo()->prepare('SELECT EXISTS (SELECT 1 FROM question_mot_cle WHERE mot_cle_id = ?)');
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }

    public static function create(string $libelle): int
    {
        $stmt = Database::pdo()->prepare('INSERT INTO mot_cle (libelle) VALUES (?)');
        $stmt->execute([$libelle]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, string $libelle): void
    {
        if (self::estVerrouille($id)) {
            throw new DomainException('Ce mot-clé est utilisé par une question : il ne peut plus être modifié.');
        }
        $stmt = Database::pdo()->prepare('UPDATE mot_cle SET libelle = ? WHERE id = ?');
        $stmt->execute([$libelle, $id]);
    }

    public static function delete(int $id): void
    {
        if (self::estVerrouille($id)) {
            throw new DomainException('Ce mot-clé est utilisé par une question : il ne peut plus être supprimé.');
        }
        try {
            $stmt = Database::pdo()->prepare('DELETE FROM mot_cle WHERE id = ?');
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { // encore référencé par un QCM (qcm_mot_cle)
                throw new DomainException('Ce mot-clé est utilisé par un QCM : suppression impossible.');
            }
            throw $e;
        }
    }
}
