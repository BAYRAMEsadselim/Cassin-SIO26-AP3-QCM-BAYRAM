<?php
declare(strict_types=1);

use App\Controllers\Admin\ClasseController;
use App\Controllers\Admin\FormateurController;
use App\Controllers\Admin\MatiereController;
use App\Controllers\Admin\MotCleController;
use App\Controllers\Admin\UtilisateurController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AuthorizationMiddleware;
use App\Security\Permission;

/** @var App\Core\Router $router */

// Pile de middlewares : être connecté ET avoir la permission demandée.
$exige = fn(Permission $p): array => [new AuthMiddleware(), new AuthorizationMiddleware($p)];

// --- Public ---
$router->add('GET',  '/login',  AuthController::class, 'form');
$router->add('POST', '/login',  AuthController::class, 'login');

// --- Tout utilisateur connecté ---
$router->add('POST', '/logout', AuthController::class, 'logout', [new AuthMiddleware()]);
$router->add('GET',  '/',       HomeController::class, 'index',  [new AuthMiddleware()]);

// --- Administrateur : comptes ---
$p = $exige(Permission::GERER_COMPTES);
$router->add('GET',  '/admin/utilisateurs',                UtilisateurController::class, 'index',   $p);
$router->add('GET',  '/admin/utilisateurs/creer',          UtilisateurController::class, 'create',  $p);
$router->add('POST', '/admin/utilisateurs',                UtilisateurController::class, 'store',   $p);
$router->add('GET',  '/admin/utilisateurs/{id}/modifier',  UtilisateurController::class, 'edit',    $p);
$router->add('POST', '/admin/utilisateurs/{id}/modifier',  UtilisateurController::class, 'update',  $p);
$router->add('POST', '/admin/utilisateurs/{id}/supprimer', UtilisateurController::class, 'destroy', $p);

// --- Administrateur : affectation des matières aux formateurs ---
$router->add('GET',  '/admin/formateurs',                 FormateurController::class, 'index',  $p);
$router->add('GET',  '/admin/formateurs/{id}/matieres',   FormateurController::class, 'edit',   $p);
$router->add('POST', '/admin/formateurs/{id}/matieres',   FormateurController::class, 'update', $p);

// --- Administrateur : matières ---
$p = $exige(Permission::GERER_MATIERES);
$router->add('GET',  '/admin/matieres',                MatiereController::class, 'index',   $p);
$router->add('GET',  '/admin/matieres/creer',          MatiereController::class, 'create',  $p);
$router->add('POST', '/admin/matieres',                MatiereController::class, 'store',   $p);
$router->add('GET',  '/admin/matieres/{id}/modifier',  MatiereController::class, 'edit',    $p);
$router->add('POST', '/admin/matieres/{id}/modifier',  MatiereController::class, 'update',  $p);
$router->add('POST', '/admin/matieres/{id}/supprimer', MatiereController::class, 'destroy', $p);

// --- Administrateur : mots-clés ---
$p = $exige(Permission::GERER_MOTS_CLES);
$router->add('GET',  '/admin/mots-cles',                MotCleController::class, 'index',   $p);
$router->add('GET',  '/admin/mots-cles/creer',          MotCleController::class, 'create',  $p);
$router->add('POST', '/admin/mots-cles',                MotCleController::class, 'store',   $p);
$router->add('GET',  '/admin/mots-cles/{id}/modifier',  MotCleController::class, 'edit',    $p);
$router->add('POST', '/admin/mots-cles/{id}/modifier',  MotCleController::class, 'update',  $p);
$router->add('POST', '/admin/mots-cles/{id}/supprimer', MotCleController::class, 'destroy', $p);

// --- Administrateur : classes (la route /creer doit précéder /{id}) ---
$p = $exige(Permission::GERER_CLASSES);
$router->add('GET',  '/admin/classes',                                         ClasseController::class, 'index',          $p);
$router->add('GET',  '/admin/classes/creer',                                   ClasseController::class, 'create',         $p);
$router->add('POST', '/admin/classes',                                         ClasseController::class, 'store',          $p);
$router->add('GET',  '/admin/classes/{id}',                                    ClasseController::class, 'show',           $p);
$router->add('GET',  '/admin/classes/{id}/modifier',                           ClasseController::class, 'edit',           $p);
$router->add('POST', '/admin/classes/{id}/modifier',                           ClasseController::class, 'update',         $p);
$router->add('POST', '/admin/classes/{id}/supprimer',                          ClasseController::class, 'destroy',        $p);
$router->add('POST', '/admin/classes/{id}/etudiants',                          ClasseController::class, 'ajouterEtudiant', $p);
$router->add('POST', '/admin/classes/{id}/etudiants/{eid}/retirer',            ClasseController::class, 'retirerEtudiant', $p);
$router->add('POST', '/admin/classes/{id}/matieres',                           ClasseController::class, 'ajouterMatiere', $p);
$router->add('POST', '/admin/classes/{id}/matieres/{cmid}/retirer',            ClasseController::class, 'retirerMatiere', $p);

// À ajouter ensuite : /questions (GERER_QUESTIONS), /formateur/qcm (GERER_QCM), /etudiant/qcm (PASSER_QCM)
