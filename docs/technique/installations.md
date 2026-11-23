---
title: Configuration de l'environnement de travail
author: LE Raphaël, BAYRAM Esadselim, MEYER Mattéo
creator: Typora inc.
subject: Documentation technique
header:
footer: ${title} - ${author} - Page ${pageNo} / ${totalPages}
---
# Configuration de l'environnement de travail

------

**Date de création** : 19/11/2026
**Date de modification** : 23/11/2026
**Sommaire :**

> [TOC]

<div style="page-break-after:always"></div>

## 1. Logiciels et versions

| Logiciel | Rôle | Version |
|---|---|---|
| Ubuntu (via WSL 2) | Système d'exploitation | 22.04 LTS |
| Apache | Serveur web | 2.4.52 |
| PHP | Langage backend | 8.1.x (extensions : pdo, pdo_mysql, mbstring, xml) |
| MariaDB | Système de gestion de base de données | 10.6.x |
| Composer | Gestionnaire de dépendances PHP | 2.x |
| Doxygen / PHPDoc | Génération de documentation technique | - |
| Visual Studio Code | Éditeur de code (extension WSL Remote) | - |
| VS Code Integrated Terminal / Windows Terminal | Terminal SSH / CLI | - |
| VS Code Remote / FileZilla | Client SFTP / transfert de fichiers | 3.x (FileZilla) |
| Git | Gestionnaire de version | 2.34.x |

## 2. Procédure d'installation

Environnement local Linux, projet servi directement dans `/var/www/html/`.

```bash
# Serveur web + PHP + modules nécessaires
sudo apt update
sudo apt install apache2 php libapache2-mod-php php-mysql php-intl \
    php-mbstring php-xml php-curl php-gd php-cli unzip -y

# Base de données
sudo apt install mariadb-server -y

# Gestionnaire de dépendances PHP
sudo apt install composer -y
```

Création de la base de données :

```bash
sudo mariadb
```
```sql
CREATE DATABASE qcm_db CHARACTER SET utf8mb4;
CREATE USER 'qcm_user'@'localhost' IDENTIFIED BY 'un_mot_de_passe';
GRANT ALL PRIVILEGES ON qcm_db.* TO 'qcm_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Récupération du projet :

```bash
cd /var/www/html
git clone git@github.com:BAYRAMEsadselim/Cassin-SIO26-AP3-QCM-BAYRAM.git
cd Cassin-SIO26-AP3-QCM-BAYRAM
composer install
```

## 3. Procédure de déploiement vers l'environnement de tests

```bash
# Sur l'environnement de tests :
cd /var/www/html
git clone git@github.com:BAYRAMEsadselim/Cassin-SIO26-AP3-QCM-BAYRAM.git
cd Cassin-SIO26-AP3-QCM-BAYRAM
git checkout develop        # branche d'intégration, pas main
composer install
cp env.example .env         # puis renseigner les identifiants propres à cet environnement
```

Mettre à jour `.env` avec les identifiants de la base de données de l'environnement de tests, puis recréer la base avec le script SQL du projet (à constituer en Phase 4).

## 4. Éléments spécifiques de configuration

- **Variables d'environnement** : les identifiants de connexion à la base de données (hôte, nom, utilisateur, mot de passe) sont stockés dans un fichier `.env` à la racine du projet, lu par `config/database.php`. Un fichier `env.example` (sans les vraies valeurs) est versionné pour que chaque développeur sache quelles variables renseigner.
- **Proxy réseau de l'établissement** : le réseau du lycée Cassin impose un proxy (`10.129.254.254:3128`) pour les connexions sortantes (`apt`, SSH). Hors de ce réseau, ce proxy est injoignable et bloque les installations/connexions :
    - pour `apt`, le proxy est déclaré dans `/etc/apt/apt.conf.d/05proxyCassin.conf` et dans `/etc/environment` (variables `http_proxy`/`https_proxy`) ;
    - pour SSH/Git, il était déclaré via un `ProxyCommand` dans `~/.ssh/config` ;
    - en dehors du réseau de l'établissement, ces lignes doivent être commentées (`#`) pour permettre les installations et les `git pull`/`push` ; elles sont à réactiver en étant connecté au réseau du lycée.
    - astuce : le port SSH 22 étant parfois filtré, GitHub permet de passer par le port 443 (`Hostname ssh.github.com`, `Port 443`) pour contourner un pare-feu restrictif.

## 5. Architecture des répertoires du projet (points de sécurité)

```
monProjet/
├── app/            # Code applicatif (MVC) : Controllers, Models, Views, Middleware, Security, Core
├── public/         # SEUL dossier exposé publiquement par Apache (DocumentRoot)
├── uploads/        # Fichiers déposés par les utilisateurs (hors de public/, non exécutable)
├── storage/        # Logs, cache (hors de public/)
├── config/         # Fichiers de configuration (hors de public/)
├── docs/           # Documentation du projet
├── tests/          # Tests unitaires
├── vendor/         # Dépendances Composer (généré, non versionné)
├── .env            # Identifiants réels (non versionné)
└── env.example     # Modèle de .env (versionné, sans valeurs sensibles)
```

**Points de sécurité pris en compte :**
- Seul le dossier `public/` est servi par Apache : le code métier (`app/`), la configuration (`config/`) et les uploads ne sont jamais directement accessibles par URL.
- Le fichier `.env` contenant les identifiants de connexion à la base de données est exclu du suivi de version via `.gitignore`, pour ne jamais exposer de secrets sur GitHub.
- Le dossier `vendor/` (dépendances tierces) est lui aussi exclu du suivi de version : il est régénéré par `composer install`, évitant de committer du code externe.
- Séparation claire entre documentation (`docs/`) et code à mettre en ligne (`app/`, `public/`).

## 6. Conventions de nommage des commits

Le projet suit le formalisme **Conventional Commits** :

```
<type>: <description courte>
```

Types utilisés :
- `feat:` ajout d'une fonctionnalité
- `fix:` correction d'un bug
- `docs:` modification de la documentation
- `chore:` tâche technique sans impact fonctionnel (config, dépendances...)
- `refactor:` réécriture de code sans changement de comportement
- `test:` ajout ou modification de tests

Exemples : `feat: ajout du CRUD matières`, `fix: correction validation formulaire de connexion`, `docs: mise à jour du cahier des charges`.
