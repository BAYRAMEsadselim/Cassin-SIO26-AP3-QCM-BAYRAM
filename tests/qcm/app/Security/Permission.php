<?php
declare(strict_types=1);

namespace App\Security;

/**
 * Liste des permissions de la plateforme et rôles qui les possèdent.
 * Pour donner un droit à un rôle, il suffit de modifier pourRole().
 */
enum Permission: string
{
    case GERER_COMPTES   = 'gerer_comptes';    // comptes + affectation des matières aux formateurs
    case GERER_MATIERES  = 'gerer_matieres';
    case GERER_CLASSES   = 'gerer_classes';
    case GERER_MOTS_CLES = 'gerer_mots_cles';
    case GERER_QUESTIONS = 'gerer_questions';
    case GERER_QCM       = 'gerer_qcm';
    case PASSER_QCM      = 'passer_qcm';

    /** @return Permission[] */
    public static function pourRole(string $role): array
    {
        return match ($role) {
            'admin' => [
                self::GERER_COMPTES, self::GERER_MATIERES, self::GERER_CLASSES,
                self::GERER_MOTS_CLES, self::GERER_QUESTIONS,
            ],
            'formateur' => [self::GERER_QUESTIONS, self::GERER_QCM],
            'etudiant'  => [self::PASSER_QCM],
            default     => [],
        };
    }
}
