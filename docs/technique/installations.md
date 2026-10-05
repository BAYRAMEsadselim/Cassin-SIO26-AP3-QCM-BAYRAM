---
title: Documentation de configuration de l'environnement de travail
author: Esadselim BAYRAM
creator: Typora inc.
subject: Documentation technique
header:
footer: ${title} -${author} - Page ${pageNo} /${totalPages}
---

# Documentation de configuration de l'environnement de travail

---

**Date de création** : 05/10/2026 - E. BAYRAM  
**Date de modification** : 05/10/2026 - E. BAYRAM  
**Sommaire :**

> [TOC]

<div style="page-break-after:always"></div>

## 1. Logiciels et composants utilisés

Pour assurer le développement et l'exécution de l'application Web QCM, l'environnement de travail repose sur une pile LAMP sous WSL (Windows Subsystem for Linux) :

- **Système d'exploitation** : Ubuntu 22.04 LTS (via WSL 2)
- **Serveur Web** : Apache / 2.4.52
- **Langage Backend** : PHP / 8.1.x (avec extensions `pdo`, `pdo_mysql`, `mbstring`, `xml`)
- **Système de Gestion de Base de Données (SGBD)** : MariaDB / 10.6.x
- **Gestionnaire de dépendances** : Composer / 2.x
- **Génération de documentation technique** : Doxygen / PHPDoc
- **Éditeur de code** : Visual Studio Code (avec extension *WSL Remote*)
- **Terminal SSH / CLI** : VS Code Integrated Terminal / Windows Terminal
- **Client SFTP / Transfert** : VS Code Remote / FileZilla 3.x
- **Gestionnaire de version** : Git / 2.34.x

---

## 2. Procédure d'installation de l'environnement (Local / WSL)

Voici les commandes d'installation et de configuration de la pile sur une machine Ubuntu / WSL vierge :

### 2.1. Mise à jour du système
```bash
sudo apt update && sudo apt upgrade -y

