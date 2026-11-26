# Déploiement

Hypothèse : serveur Linux avec Apache, PHP 8.1+ et MariaDB. À adapter à l'hébergeur choisi.

## Avant la mise en ligne

- Supprimer ou changer le compte de test `admin@example.test` / `admin123`.
- Créer un utilisateur MariaDB dédié, avec des droits limités à la base `qcm_platform`, et un mot de passe fort.
- Vérifier que `config/database.local.php` n'est pas versionné.

## Étapes

1. Copier le projet sur le serveur, par exemple dans `/var/www/qcm`.
2. Pointer la racine web d'Apache sur **`/var/www/qcm/public`** uniquement, jamais sur la racine du projet (`config`, `app`, `storage` doivent rester inaccessibles depuis le web).
3. Activer la réécriture d'URL : `sudo a2enmod rewrite`, et `AllowOverride All` sur le dossier `public` pour que `.htaccess` fonctionne.
4. Importer `database/schema.sql`, puis créer le premier compte administrateur avec un mot de passe haché (`php -r 'echo password_hash("MotDePasse", PASSWORD_DEFAULT);'`).
5. Créer `config/database.local.php` avec les identifiants de production.
6. Donner à Apache les droits d'écriture sur `storage/` et `uploads/` :
   ```bash
   sudo chown -R www-data:www-data storage uploads
   ```
7. Activer HTTPS (certificat Let's Encrypt, `certbot --apache`). Quand HTTPS est actif, ajouter `'secure' => true` aux paramètres du cookie de session dans `app/Security/Auth.php`.

## Contrôles après déploiement

- La page de connexion s'affiche et la connexion fonctionne.
- Une adresse comme `https://serveur/config/database.php` renvoie une erreur 404, jamais le contenu du fichier.
- Une erreur provoquée volontairement s'écrit dans `storage/logs/app.log` sans rien afficher à l'écran.
