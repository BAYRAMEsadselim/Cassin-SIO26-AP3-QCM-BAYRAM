# Diagrammes

Les blocs ci-dessous sont en Mermaid : ils s'affichent sur GitHub, GitLab et dans VS Code avec l'extension « Markdown Preview Mermaid Support ».

## Modèle de données

```mermaid
erDiagram
    UTILISATEUR ||--o{ FORMATEUR_MATIERE : enseigne
    MATIERE ||--o{ FORMATEUR_MATIERE : "est assurée"
    CLASSE ||--o{ CLASSE_ETUDIANT : regroupe
    UTILISATEUR ||--o{ CLASSE_ETUDIANT : "appartient"
    CLASSE ||--o{ CLASSE_MATIERE : programme
    FORMATEUR_MATIERE ||--o{ CLASSE_MATIERE : "assure dans"
    CLASSE_MATIERE ||--o{ QCM : concerne
    QCM ||--o{ QCM_QUESTION : contient
    QUESTION ||--o{ QCM_QUESTION : "tirée dans"
    QUESTION ||--o{ PROPOSITION : compose
    QUESTION ||--o{ QUESTION_MOT_CLE : caractérise
    MOT_CLE ||--o{ QUESTION_MOT_CLE : "qualifie"
    QCM ||--o{ QCM_MOT_CLE : cible
    MOT_CLE ||--o{ QCM_MOT_CLE : "demandé dans"
    QCM ||--o{ TENTATIVE : "est passé par"
    UTILISATEUR ||--o{ TENTATIVE : passe
    TENTATIVE ||--o{ REPONSE_ETUDIANT : contient
    PROPOSITION ||--o{ REPONSE_ETUDIANT : "choisie dans"
```

## Parcours d'une requête

```mermaid
sequenceDiagram
    participant N as Navigateur
    participant I as index.php
    participant R as Router
    participant M as Middlewares
    participant C as Contrôleur
    participant D as Modèle (PDO)
    participant V as Vue
    N->>I: requête HTTP
    I->>R: dispatch(méthode, URL)
    R->>M: Auth, Authorization, Csrf
    alt refus
        M-->>N: 302 /login, 403 ou 419
    else accepté
        R->>C: action(paramètres)
        C->>D: requêtes préparées
        D-->>C: données
        C->>V: render(vue, données)
        V-->>N: page HTML
    end
```

## Règles de verrouillage

```mermaid
flowchart LR
    A[Matière affectée à un formateur ou une classe] --> X[Modification et suppression refusées]
    B[Mot-clé rattaché à une question] --> X
    C[Question utilisée dans un QCM] --> Y[Modification refusée, duplication permise]
    D[Tentative validée] --> Z[Réponses figées]
```
