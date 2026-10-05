---
title: Documentation de conception applicative et modélisation BDD
author: Esadselim BAYRAM
creator: Typora inc.
subject: Documentation technique - Phase 3
header:
footer: ${title} -${author} - Page ${pageNo} /${totalPages}
---

# Documentation de conception applicative et modélisation BDD

---

**Date de création** : 05/10/2026 - E. BAYRAM  
**Date de modification** : 05/10/2026 - E. BAYRAM  
**Sommaire :**

> [TOC]

<div style="page-break-after:always"></div>

## 1. Présentation générale

Ce document regroupe l'ensemble des éléments de conception technique et fonctionnelle du projet de plateforme QCM en ligne. Il sert de référence pour le développement de la base de données MariaDB et de l'architecture applicative sous le modèle MVC.

---

## 2. Diagramme de cas d'utilisation (Use Cases)

L'application distingue trois rôles principaux : **Administrateur**, **Formateur** et **Étudiant**.

```mermaid
graph TD
    subgraph Plateforme QCM
        %% Cas d'utilisation Administrateur
        UC_Admin1(Gérer les matières - CRUD)
        UC_Admin2(Gérer les comptes utilisateurs - CRUD)
        UC_Admin3(Gérer les classes - CRUD)
        UC_Admin4(Affecter étudiants et formateurs aux classes)
        UC_Admin5(Gérer les mots-clés thématiques - CRUD)

        %% Cas d'utilisation Formateur
        UC_Form1(Gérer la banque de questions - CRUD)
        UC_Form2(Configurer un QCM)
        UC_Form3(Associer critères et mots-clés au QCM)

        %% Cas d'utilisation Étudiant
        UC_Etu1(Consulter la liste des QCM)
        UC_Etu2(Répondre à un QCM en cours)
        UC_Etu3(Consulter les résultats et corrections)
    end

    Admin((Administrateur)) --> UC_Admin1
    Admin --> UC_Admin2
    Admin --> UC_Admin3
    Admin --> UC_Admin4
    Admin --> UC_Admin5
    Admin --> UC_Form1

    Formateur((Formateur)) --> UC_Form1
    Formateur --> UC_Form2
    Formateur --> UC_Form3

    Etudiant((Étudiant)) --> UC_Etu1
    Etudiant --> UC_Etu2
    Etudiant --> UC_Etu3
