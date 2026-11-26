# Installation pour le développement (WSL Ubuntu)

## Prérequis

PHP 8.1 ou plus, MariaDB (ou MySQL 8), extensions `pdo_mysql` et `mbstring`.

## Étapes

1. Installer les paquets :
   ```bash
   sudo apt update
   sudo apt install -y php-cli php-mysql php-mbstring mariadb-server unzip
   ```
2. Démarrer MariaDB (à refaire après chaque redémarrage de WSL sans `systemd`) :
   ```bash
   sudo service mariadb start
   ```
3. Créer la base, les tables et le compte administrateur de départ, depuis la racine du projet :
   ```bash
   sudo mariadb < database/schema.sql
   sudo mariadb < database/seed.sql
   ```
4. Créer l'utilisateur de l'application (le compte `root` de MariaDB ne s'utilise pas depuis PHP sur Ubuntu) :
   ```bash
   sudo mariadb -e "CREATE USER 'qcm'@'localhost' IDENTIFIED BY 'qcm_dev'; GRANT ALL ON qcm_platform.* TO 'qcm'@'localhost';"
   ```
5. Renseigner la connexion :
   ```bash
   cp config/database.php config/database.local.php
   nano config/database.local.php
   ```
   Mettre `'user' => 'qcm'` et `'pass' => 'qcm_dev'`.
6. Lancer le serveur : `php -S localhost:8000 -t public`, puis ouvrir http://localhost:8000.
7. Compte de départ (développement uniquement) : `admin@example.test` / `admin123`.

## Lancer les tests

```bash
php tests/run.php
```

## Problèmes fréquents

| Message | Cause | Solution |
| --- | --- | --- |
| `could not find driver` | Extension manquante | `sudo apt install php-mysql` |
| `Access denied for user` | Mauvais identifiants | Vérifier `config/database.local.php` et l'étape 4 |
| `Connection refused` | MariaDB arrêté | `sudo service mariadb start` |
| Page blanche « Erreur interne » | Exception PHP | Lire `storage/logs/app.log` |
