# Routes et API

Les routes sont déclarées dans `config/routes.php`. Tout `POST` exige le jeton CSRF (champ `_csrf` dans un formulaire, en-tête `X-CSRF-Token` pour le JavaScript).

## Pages (HTML)

| Méthode | URL | Permission | Description |
| --- | --- | --- | --- |
| GET, POST | `/login` | Publique | Connexion |
| POST | `/logout` | Connecté | Déconnexion |
| GET | `/` | Connecté | Redirige selon le rôle |
| GET | `/admin/utilisateurs` | `GERER_COMPTES` | Liste des comptes |
| GET, POST | `/admin/utilisateurs/creer`, `/admin/utilisateurs`, `/{id}/modifier`, `/{id}/supprimer` | `GERER_COMPTES` | CRUD des comptes |
| GET | `/admin/formateurs` | `GERER_COMPTES` | Formateurs et leurs matières |
| GET, POST | `/admin/formateurs/{id}/matieres` | `GERER_COMPTES` | Affecter des matières à un formateur |
| GET, POST | `/admin/matieres...` | `GERER_MATIERES` | CRUD des matières |
| GET, POST | `/admin/mots-cles...` | `GERER_MOTS_CLES` | CRUD des mots-clés |
| GET, POST | `/admin/classes...` | `GERER_CLASSES` | CRUD des classes |
| GET | `/admin/classes/{id}` | `GERER_CLASSES` | Détail d'une classe |
| POST | `/admin/classes/{id}/etudiants` | `GERER_CLASSES` | Ajouter un étudiant |
| POST | `/admin/classes/{id}/etudiants/{eid}/retirer` | `GERER_CLASSES` | Retirer un étudiant |
| POST | `/admin/classes/{id}/matieres` | `GERER_CLASSES` | Ajouter une matière avec son formateur |
| POST | `/admin/classes/{id}/matieres/{cmid}/retirer` | `GERER_CLASSES` | Retirer une matière |

## À créer (JSON, utilisées par `ApiClient.js`)

| Méthode | URL | Permission | Description |
| --- | --- | --- | --- |
| POST | `/api/tentatives/{id}/reponse` | `PASSER_QCM` | Sauvegarde la réponse à une question |
| POST | `/api/tentatives/{id}/valider` | `PASSER_QCM` | Valide définitivement le QCM |
| GET | `/api/qcm?matiere=&date=` | `PASSER_QCM` | Liste filtrée des QCM |

## Codes de réponse

| Code | Signification |
| --- | --- |
| 302 | Redirection (non connecté, ou après une action réussie) |
| 403 | Permission manquante |
| 404 | Route ou ressource introuvable |
| 419 | Jeton CSRF absent ou invalide |
| 422 | Données du formulaire invalides |
| 500 | Erreur interne (voir `storage/logs/app.log`) |
