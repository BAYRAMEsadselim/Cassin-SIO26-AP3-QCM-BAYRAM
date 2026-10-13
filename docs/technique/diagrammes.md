# Conception de l'Application - Plateforme QCM

## 1. Charte Graphique et Maquettes

### 1.1. Identité visuelle
- **Palette de couleurs** :
  - **Primary (Principale)** : `#0d6efd` (Bleu institutionnel)
  - **Secondary (Secondaire)** : `#6c757d` (Gris neutre)
  - **Accent / Success** : `#198754` (Vert de validation / bonne réponse)
  - **Danger / Error** : `#dc3545` (Rouge d'erreur / mauvaise réponse)
  - **Background** : `#f8f9fa` (Fond clair reposant)
- **Typographie** : `System-ui`, `-apple-system`, `Roboto`, `Segoe UI`, `sans-serif` (Lisibilité optimale).
- **Framework CSS** : Bootstrap 5 pour garantir une interface moderne et entièrement responsive.

### 1.2. Logique de navigation et maquettes des vues

#### A. Écran de connexion (`/login`)
- **Structure** : Carte centralisée au milieu de la page.
- **Éléments** : Logo, champ Email, champ Mot de passe, bouton "Se connecter", message d'erreur d'authentification en rouge si besoin.

#### B. Tableau de bord Étudiant (`/student/dashboard`)
- **Header** : Nom de l'étudiant, classe actuelle, bouton de déconnexion.
- **Section QCMs** :
  - **QCMs en cours** : Carte avec titre, matière, date limite et bouton vert "Démarrer".
  - **QCMs passés** : Liste des questionnaires terminés avec la note obtenue et un bouton "Voir le détail/correction".
  - **QCMs futurs** : Liste grise non cliquable affichant la date d'ouverture à venir.

#### C. Interface de passation d'un QCM (`/qcm/play`)
- **Header fixe** : Titre du QCM, chronomètre / temps restant, barre de progression.
- **Corps de page** : Énoncé de la question, choix de réponses (boutons radio ou cases à cocher selon le type de question).
- **Footer** : Boutons "Question précédente", "Question suivante" et "Valider le QCM".

---

## 2. Diagrammes UML (À compléter par l'équipe)

*(Section réservée pour les diagrammes de cas d'utilisation et de séquence)*

---

## 3. Modèle Conceptuel et Relationnel de Données (À compléter par l'équipe)

*(Section réservée pour le MCD et le MLD)*

