# Conventions de code

## PHP

- Chaque fichier commence par `declare(strict_types=1);`.
- Un fichier = une classe, nommé comme la classe (`MatiereController.php`). Espace de noms = chemin du dossier sous `app/` (`App\Controllers\Admin`).
- Classes en `PascalCase`, méthodes et variables en `camelCase`, constantes et cas d'énumération en `MAJUSCULES`.
- Noms des contrôleurs : `<Ressource>Controller`. Actions habituelles : `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.
- Les modèles contiennent tout le SQL ; les contrôleurs ne contiennent aucune requête.
- Toute requête utilise `prepare()` et `execute()`. Jamais de variable dans une chaîne SQL.
- Une règle de gestion violée dans un modèle lève une `DomainException` ; le contrôleur la transforme en message.

## Vues

- Toute donnée affichée passe par `e()`.
- Tout formulaire `POST` contient `<input type="hidden" name="_csrf" value="...">`.
- Pas de requête ni de logique métier dans une vue.

## URL

- En français, en minuscules, avec des tirets : `/admin/mots-cles`.
- Une ressource : `/admin/<ressource>`, `/creer`, `/{id}/modifier`, `/{id}/supprimer`.
- Les routes fixes (`/creer`) se déclarent avant les routes à paramètre (`/{id}`).

## Base de données

- Tables et colonnes en `snake_case`, au singulier (`mot_cle`).
- Clés étrangères nommées `<table>_id`.

## JavaScript

- Modules ES, une classe par fichier (`ApiClient.js`, `QuizPlayer.js`).
- `textContent` plutôt que `innerHTML` pour afficher des données.

## Git

- Messages de commit courts, à l'impératif : `Ajoute le CRUD des classes`.
- Ne jamais versionner `config/*.local.php`.
