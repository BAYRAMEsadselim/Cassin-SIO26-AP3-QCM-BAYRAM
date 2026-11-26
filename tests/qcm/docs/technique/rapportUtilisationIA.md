# Rapport d'utilisation de l'IA

Ce document décrit comment un assistant d'IA (Claude, d'Anthropic) a été utilisé dans ce projet, ce qui a été vérifié, et ce qui reste à la charge de l'étudiant. Les parties « À compléter » sont à renseigner par vous : elles doivent refléter ce que vous avez réellement fait.

## Outil et périmètre

- Outil : Claude (assistant conversationnel d'Anthropic).
- Utilisé pour : l'analyse du cahier des charges, la conception (règles de gestion, MCD/MLD, script SQL, architecture MVC), l'écriture du squelette de code PHP et JavaScript, le CRUD des matières, des comptes, des mots-clés, des classes et de l'affectation des formateurs, et cette documentation.
- Non utilisé pour : les parties encore à faire (questions, QCM, espace étudiant), sauf indication contraire plus bas.

## Méthode

1. Le cahier des charges a été fourni tel quel ; le livrable demandé (conception complète, puis squelette de code) a été précisé par des questions.
2. La conception a été rédigée dans un document de conception, relu avant d'écrire le code.
3. Le code a été produit par étapes (noyau, puis un CRUD servant de modèle, puis les autres CRUD), et chaque étape a été installée et essayée en local sous WSL Ubuntu.
4. L'arborescence du projet a été adaptée à celle demandée (middlewares, sécurité, configuration, documentation).

## Vérifications effectuées

| Vérification | Par qui | Résultat |
| --- | --- | --- |
| Syntaxe PHP de tous les fichiers (`php -l`) | Assistant | Sans erreur |
| Tests automatiques (`php tests/run.php`) : permissions, routeur | Assistant | 9 réussis |
| Contrôle des accès sans session (redirections, refus CSRF) sur un serveur local | Assistant | Conforme |
| Essai complet avec la base de données (connexion, CRUD matières et comptes) | Étudiant | Conforme |
| Essai des mots-clés, des classes et de l'affectation des formateurs avec la base | Étudiant | À compléter |

Limite à connaître : l'assistant n'a pas pu exécuter le code contre une vraie base MySQL/MariaDB. Les requêtes SQL des modèles doivent donc être essayées sur votre installation.

## Ce que l'étudiant doit comprendre et pouvoir expliquer

- Le parcours d'une requête (routeur, middlewares, contrôleur, modèle, vue).
- Pourquoi les requêtes sont préparées et les sorties échappées.
- Le rôle des verrous (clés étrangères, déclencheurs, contrôles dans les modèles).

## À compléter par l'étudiant

- Les consignes (prompts) importantes que vous avez données et ce qu'elles ont produit.
- Les erreurs ou limites constatées dans les réponses de l'IA et la façon dont vous les avez corrigées.
- Les modifications que vous avez apportées vous-même au code.
- Votre analyse : ce que l'IA a apporté, ce qui a demandé votre intervention.
