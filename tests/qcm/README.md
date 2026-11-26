# Plateforme de QCM en ligne

PHP 8.1+ natif (sans framework) en MVC, MySQL/MariaDB via PDO, JavaScript objet (modules ES).

## Démarrage rapide (WSL Ubuntu)

Détails dans [docs/technique/installations.md](docs/technique/installations.md).

```bash
sudo service mariadb start
sudo mariadb < database/schema.sql
sudo mariadb < database/seed.sql
cp config/database.php config/database.local.php   # puis renseigner utilisateur et mot de passe
php -S localhost:8000 -t public
```

Compte de départ (développement uniquement) : `admin@example.test` / `admin123`.

## Structure

`app/` (Controllers, Models, Views, Middleware, Security, Core), `public/` (seul dossier exposé), `config/`, `database/`, `storage/`, `uploads/`, `tests/`, `docs/`.

## Fonctionnalités

| Espace | État |
| --- | --- |
| Connexion, rôles, permissions, middlewares | Fait |
| Administrateur : comptes, matières, formateurs, mots-clés, classes | Fait |
| Questions (administrateur et formateur) | À faire |
| QCM et tirage aléatoire (formateur) | À faire |
| Passage des QCM et résultats (étudiant) | À faire |

## Tests

```bash
php tests/run.php
```

## Documentation

`docs/projet` (planification), `docs/technique` (architecture, conventions, API, diagrammes, maintenance, déploiement, rapport d'utilisation de l'IA), `docs/utilisateur` (manuels).
