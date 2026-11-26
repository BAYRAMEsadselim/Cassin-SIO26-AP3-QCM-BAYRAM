<?php
declare(strict_types=1);

use App\Core\Router;

function routeurDeTest(): Router
{
    $router = new Router();
    $router->add('GET', '/admin/classes/creer', 'C', 'create');
    $router->add('GET', '/admin/classes/{id}', 'C', 'show');
    $router->add('POST', '/admin/classes/{id}/etudiants/{eid}/retirer', 'C', 'retirer');
    return $router;
}

test('une route fixe passe avant une route avec paramètre', function () {
    $m = routeurDeTest()->match('GET', '/admin/classes/creer');
    verifierEgal('create', $m['route']['action']);
});

test("le paramètre numérique est converti en entier", function () {
    $m = routeurDeTest()->match('GET', '/admin/classes/12');
    verifierEgal('show', $m['route']['action']);
    verifierEgal([12], $m['params']);
});

test('plusieurs paramètres arrivent dans l\'ordre', function () {
    $m = routeurDeTest()->match('POST', '/admin/classes/3/etudiants/9/retirer');
    verifierEgal([3, 9], $m['params']);
});

test('une méthode ou une URL inconnue ne correspond à rien', function () {
    verifierEgal(null, routeurDeTest()->match('POST', '/admin/classes/creer'));
    verifierEgal(null, routeurDeTest()->match('GET', '/inconnu'));
});

test('le slash final est ignoré', function () {
    $m = routeurDeTest()->match('GET', '/admin/classes/5/');
    verifierEgal([5], $m['params']);
});
