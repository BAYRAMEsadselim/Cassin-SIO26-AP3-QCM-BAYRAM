# Architecture

## Principe

Application PHP 8.1+ sans framework, en modèle MVC. Un point d'entrée unique (`public/index.php`) reçoit toutes les requêtes.

## Parcours d'une requête

1. `public/index.php` charge l'autoloader, la configuration, démarre la session et charge `config/routes.php`.
2. Le `Router` cherche la route qui correspond à la méthode et à l'URL, et en extrait les paramètres (`{id}`).
3. Les **middlewares** de la route s'exécutent dans l'ordre : `AuthMiddleware` (connecté ?), `AuthorizationMiddleware` (permission ?), puis `CsrfMiddleware` (ajouté automatiquement pour les requêtes autres que GET).
4. Le **contrôleur** valide les données, appelle les **modèles** (requêtes PDO préparées) et choisit une **vue**.
5. La vue est insérée dans le gabarit `app/Views/layouts/main.php`.

## Dossiers

| Dossier | Rôle |
| --- | --- |
| `app/Core` | `Router`, `Database` (PDO), `Controller` (classe mère), `helpers.php` (fonction `e()`) |
| `app/Middleware` | `MiddlewareInterface` et les trois middlewares |
| `app/Security` | `Auth` (connexion), `Authorization` (droit ?), `Permission` (liste des droits par rôle), `Csrf` |
| `app/Models` | Accès aux données : `Utilisateur`, `Matiere`, `MotCle`, `Classe` |
| `app/Controllers` | Un contrôleur par ressource, dossier `Admin/` pour l'administration |
| `app/Views` | Pages PHP, un dossier par ressource |
| `config` | `config.php` (général), `database.php` (base), `routes.php` (routes) |
| `database` | `schema.sql` (tables et déclencheurs), `seed.sql` (données de départ) |
| `storage` | Journaux (`logs/app.log`) et cache |
| `uploads` | Fichiers déposés, hors du dossier web |
| `tests` | Tests sans dépendance : `php tests/run.php` |

## Rôles et permissions

Les droits sont définis à un seul endroit : `Permission::pourRole()`. Une route demande une permission, pas un rôle.

| Permission | Administrateur | Formateur | Étudiant |
| --- | --- | --- | --- |
| `GERER_COMPTES` (comptes, affectation formateur-matières) | oui | | |
| `GERER_MATIERES`, `GERER_CLASSES`, `GERER_MOTS_CLES` | oui | | |
| `GERER_QUESTIONS` | oui | oui | |
| `GERER_QCM` | | oui | |
| `PASSER_QCM` | | | oui |

## Règles de gestion appliquées deux fois

Les verrous (une matière affectée, un mot-clé utilisé, une question dans un QCM) sont contrôlés dans les modèles PHP, pour afficher un message clair, et dans la base (clés étrangères `RESTRICT` et déclencheurs), comme dernier filet de sécurité.

## Sécurité

Mots de passe hachés (`password_hash`), requêtes préparées, échappement des sorties (`e()`), jeton CSRF sur tous les `POST`, régénération de l'identifiant de session à la connexion, erreurs détaillées écrites dans `storage/logs/app.log` et jamais affichées.
