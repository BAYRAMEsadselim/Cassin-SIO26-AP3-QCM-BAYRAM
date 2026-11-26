# Maintenance

## Tâches courantes

| Tâche | Comment faire |
| --- | --- |
| Lire les erreurs | Ouvrir `storage/logs/app.log` |
| Changer le mot de passe d'un compte | Page Comptes, bouton Modifier, saisir un nouveau mot de passe |
| Sauvegarder la base | `mysqldump -u qcm -p qcm_platform > sauvegarde.sql` |
| Restaurer la base | `mysql -u qcm -p qcm_platform < sauvegarde.sql` |
| Lancer les tests | `php tests/run.php` |
| Début d'année scolaire | Créer les nouvelles classes (page Classes) et y affecter les étudiants et les matières |

## Ajouter une fonctionnalité

1. Ajouter ou adapter la table dans `database/schema.sql`.
2. Écrire le modèle dans `app/Models` (requêtes préparées uniquement).
3. Écrire le contrôleur dans `app/Controllers`, en s'inspirant de `Admin/MatiereController.php`.
4. Écrire les vues dans `app/Views/<ressource>`.
5. Déclarer les routes dans `config/routes.php` avec la permission voulue ; ajouter la permission dans `app/Security/Permission.php` si besoin.
6. Ajouter un test dans `tests/` pour les règles de gestion qui ne dépendent pas de la base.

## Points de vigilance

- Les verrous (matière, mot-clé, question) existent dans les modèles et dans la base : modifier l'un sans l'autre crée des incohérences.
- Changer un rôle de permission : uniquement dans `Permission::pourRole()`.
- Ne jamais afficher une donnée sans `e()`.
