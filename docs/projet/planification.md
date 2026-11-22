---
title: Planification du projet
author: LE Raphaël - BAYRAM Esadselim - MEYER Mattéo
creator: Typora inc.
subject: Gestion de projet
header:
footer: ${title} - ${author} - Page ${pageNo} / ${totalPages}
---
# Planification du projet

------

**Date de création** : 19/11/2026
**Date de modification** : 19/11/2026
**Sommaire :**

> [TOC]

<div style="page-break-after:always"></div>

## 1. Diagramme de GANTT

```mermaid
gantt
    title Atelier de professionnalisation 3 - Plateforme de QCM
    dateFormat  YYYY-MM-DD
    axisFormat  %d/%m

    section Phase 1 - Planifier
    Diagramme de GANTT + Milestones/Issues GitHub   :p1, 2026-09-28, 2026-10-07

    section Phase 2 - Environnement
    Config environnement dev + tests                :p2, 2026-09-28, 2026-10-07

    section Phase 3 - Conception
    Charte graphique + maquettes                     :p3a, 2026-10-12, 2026-11-16
    Diagrammes UML (cas d'utilisation, MEA/MR)        :p3b, 2026-10-12, 2026-11-16

    section Phase 4 - Développement
    Script de création de la BDD + jeu de données     :p4a, 2026-11-17, 2026-12-01
    Authentification + gestion des comptes (CRUD)     :p4b, 2026-11-23, 2026-12-07
    Gestion matières / classes / mots-clés (CRUD)     :p4c, 2026-11-30, 2026-12-14
    Gestion des questions (CRUD)                      :p4d, 2026-12-01, 2026-12-14
    Génération automatique des QCM                    :p4e, 2026-12-07, 2026-12-21
    Interface étudiant (répondre, résultats, filtres) :p4f, 2026-12-14, 2027-01-04
    Tests + mise en ligne environnement de tests      :p4g, 2026-12-21, 2027-01-04
```

## 2. Étapes et tâches dans GitHub

Les étapes ci-dessus sont transcrites en **Milestones** GitHub, et chaque ligne du diagramme correspond à une ou plusieurs **Issues** (tickets), assignées à un membre de l'équipe.

### Milestones

| Milestone | Échéance |
|---|---|
| Rendu 1 - Planifier le projet | 07/10/2026 |
| Rendu 2 - Organiser l'environnement de travail | 07/10/2026 |
| Rendu 3 - Concevoir l'application | 16/11/2026 |
| Rendu 4 - Développer l'application | 04/01/2027 |

### Issues (tickets)

**Rendu 1 - Planifier**
- [x] Définir le diagramme de GANTT du projet
- [x] Créer les Milestones dans GitHub
- [x] Créer les Issues correspondant aux tâches
- [x] Rédiger docs/projet/planification.md

**Rendu 2 - Environnement**
- [x] Choisir et configurer l'environnement de dev (VM / WSL / Multipass)
- [x] Installer Apache, PHP, MariaDB
- [x] Configurer l'environnement de tests
- [x] Rédiger docs/technique/installations.md

**Rendu 3 - Conception**
- [ ] Charte graphique + maquettes des écrans
- [ ] Diagramme de contexte statique
- [ ] Diagramme de cas d'utilisation
- [ ] Modèle entité-association / modèle relationnel
- [ ] Rédiger docs/technique/architecture.md

**Rendu 4 - Développement**
- [ ] Script SQL de création de la BDD + jeu de données de test
- [ ] feature/authentification (AuthMiddleware, connexion/déconnexion)
- [ ] feature/gestion-comptes (CRUD utilisateurs, rôles)
- [ ] feature/gestion-matieres (CRUD matières)
- [ ] feature/gestion-classes (CRUD classes)
- [ ] feature/gestion-motscles (CRUD mots-clés)
- [ ] feature/gestion-questions (CRUD questions)
- [ ] feature/generation-qcm (génération automatique par mots-clés)
- [ ] feature/espace-etudiant (liste QCM, réponse, résultats, filtres)
- [ ] Tests + mise en ligne sur l'environnement de tests

## 3. Capture d'écran des tâches et étapes dans GitHub

![Milestones et Issues GitHub](./Medias/issue.png)
![Milestones et Issues GitHub2](./Medias/issue2.png)
![Milestones et Issues GitHub3](./Medias/issue3.png)
