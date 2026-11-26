# Plateforme de QCM en ligne

Projet réalisé dans le cadre de l'Atelier de professionnalisation 3 (BTS SIO SLAM, 2e année).

## Présentation
Application web PHP (MVC) permettant aux formateurs de configurer des QCM et d'évaluer leurs étudiants.

## Équipe
- LE Raphaël
- BAYRAM Esadselim
- MEYER Mattéo

## Organisation Git
- `main` : version stable, référence pour l'évaluation
- `develop` : intégration des fonctionnalités
- `feature/...` : une branche par fonctionnalité

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

## Tests

```bash
php tests/run.php
```

## Documentation
Voir le dossier `docs/` (gestion de projet, technique, utilisateur).

## Installation
Voir `docs/technique/installations.md`.
