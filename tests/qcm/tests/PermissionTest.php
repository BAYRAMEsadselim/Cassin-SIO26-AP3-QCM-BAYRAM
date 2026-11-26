<?php
declare(strict_types=1);

use App\Security\Permission;

test("l'administrateur gère les comptes mais ne crée pas de QCM", function () {
    $p = Permission::pourRole('admin');
    verifier(in_array(Permission::GERER_COMPTES, $p, true));
    verifier(!in_array(Permission::GERER_QCM, $p, true));
});

test('le formateur gère les questions et les QCM, pas les comptes', function () {
    $p = Permission::pourRole('formateur');
    verifier(in_array(Permission::GERER_QUESTIONS, $p, true));
    verifier(in_array(Permission::GERER_QCM, $p, true));
    verifier(!in_array(Permission::GERER_COMPTES, $p, true));
});

test("l'étudiant ne peut que passer des QCM", function () {
    verifierEgal([Permission::PASSER_QCM], Permission::pourRole('etudiant'));
});

test('un rôle inconnu n\'a aucune permission', function () {
    verifierEgal([], Permission::pourRole('pirate'));
});
