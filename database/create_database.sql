-- ============================================================
-- Script de création de la base de données
-- Plateforme de QCM en ligne - Atelier de professionnalisation 3
-- ============================================================

CREATE DATABASE IF NOT EXISTS qcm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qcm_db;

-- ============================================================
-- 1. Tables de base
-- ============================================================

CREATE TABLE utilisateur (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    prenom        VARCHAR(100) NOT NULL,
    email         VARCHAR(190) NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255) NOT NULL,
    role          ENUM('administrateur', 'formateur', 'etudiant') NOT NULL,
    classe_id     INT NULL,           -- renseigné uniquement pour les étudiants
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE classe (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    nom     VARCHAR(100) NOT NULL,
    annee   VARCHAR(9) NOT NULL        -- ex: "2026-2027"
) ENGINE=InnoDB;

ALTER TABLE utilisateur
    ADD CONSTRAINT fk_utilisateur_classe
    FOREIGN KEY (classe_id) REFERENCES classe(id) ON DELETE SET NULL;

CREATE TABLE matiere (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nom       VARCHAR(150) NOT NULL,
    affectee  BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB;

CREATE TABLE affectation (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    classe_id     INT NOT NULL,
    matiere_id    INT NOT NULL,
    formateur_id  INT NOT NULL,
    CONSTRAINT fk_affectation_classe    FOREIGN KEY (classe_id)    REFERENCES classe(id)      ON DELETE CASCADE,
    CONSTRAINT fk_affectation_matiere   FOREIGN KEY (matiere_id)   REFERENCES matiere(id)      ON DELETE CASCADE,
    CONSTRAINT fk_affectation_formateur FOREIGN KEY (formateur_id) REFERENCES utilisateur(id)  ON DELETE CASCADE,
    UNIQUE KEY uniq_affectation (classe_id, matiere_id)
) ENGINE=InnoDB;

CREATE TABLE mot_cle (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    libelle  VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ============================================================
-- 2. Questions et réponses
-- ============================================================

CREATE TABLE question (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    enonce       TEXT NOT NULL,
    type         ENUM('unique', 'multiple', 'negative', 'classement') NOT NULL,
    commentaire  TEXT NULL,
    affectee     BOOLEAN NOT NULL DEFAULT FALSE,
    auteur_id    INT NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_question_auteur FOREIGN KEY (auteur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reponse_possible (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    question_id    INT NOT NULL,
    texte          VARCHAR(255) NOT NULL,
    est_correcte   BOOLEAN NOT NULL DEFAULT FALSE,
    ordre          INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_reponse_question FOREIGN KEY (question_id) REFERENCES question(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE question_mot_cle (
    question_id  INT NOT NULL,
    mot_cle_id   INT NOT NULL,
    PRIMARY KEY (question_id, mot_cle_id),
    CONSTRAINT fk_qmc_question FOREIGN KEY (question_id) REFERENCES question(id) ON DELETE CASCADE,
    CONSTRAINT fk_qmc_motcle   FOREIGN KEY (mot_cle_id)  REFERENCES mot_cle(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 3. QCM
-- ============================================================

CREATE TABLE qcm (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    titre         VARCHAR(150) NOT NULL,
    matiere_id    INT NOT NULL,
    classe_id     INT NOT NULL,
    formateur_id  INT NOT NULL,
    date_debut    DATETIME NOT NULL,
    date_fin      DATETIME NOT NULL,
    nb_questions  INT NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_qcm_matiere   FOREIGN KEY (matiere_id)   REFERENCES matiere(id)     ON DELETE CASCADE,
    CONSTRAINT fk_qcm_classe    FOREIGN KEY (classe_id)    REFERENCES classe(id)      ON DELETE CASCADE,
    CONSTRAINT fk_qcm_formateur FOREIGN KEY (formateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE qcm_question (
    qcm_id       INT NOT NULL,
    question_id  INT NOT NULL,
    PRIMARY KEY (qcm_id, question_id),
    CONSTRAINT fk_qq_qcm      FOREIGN KEY (qcm_id)      REFERENCES qcm(id)      ON DELETE CASCADE,
    CONSTRAINT fk_qq_question FOREIGN KEY (question_id) REFERENCES question(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reponse_etudiant (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id     INT NOT NULL,
    qcm_id          INT NOT NULL,
    question_id     INT NOT NULL,
    reponse_donnee  TEXT NOT NULL,
    est_correcte    BOOLEAN NOT NULL DEFAULT FALSE,
    repondu_le      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_re_etudiant FOREIGN KEY (etudiant_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    CONSTRAINT fk_re_qcm      FOREIGN KEY (qcm_id)      REFERENCES qcm(id)         ON DELETE CASCADE,
    CONSTRAINT fk_re_question FOREIGN KEY (question_id) REFERENCES question(id)    ON DELETE CASCADE,
    UNIQUE KEY uniq_reponse (etudiant_id, qcm_id, question_id)
) ENGINE=InnoDB;

-- ============================================================
-- 4. Jeu de données de test
-- ============================================================

-- Comptes (mots de passe = "password" hashé en bcrypt, à régénérer en PHP avec password_hash())
INSERT INTO classe (nom, annee) VALUES
('BTS SIO2 SLAM', '2026-2027'),
('BTS SIO1 SLAM', '2026-2027');

INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, classe_id) VALUES
('SCHOSSIG', 'Frederic', 'admin@cassin.fr', 'a faire', 'administrateur', NULL),
('LE', 'Raphaël', 'le.raphael67@gmail.com', 'a faire', 'administrateur', 1),
('BAYRAM', 'Esad', 'bayramesadselim@gmail.com', 'a faire', 'administrateur', 2),
('MEYER', 'Mattéo', 'lyonmatteo@gmail.com', 'a faire', 'administrateur', 3),

INSERT INTO matiere (nom, affectee) VALUES
('PHP / Développement web', TRUE),
('Anglais', TRUE),
('Mathematiques', FALSE);

INSERT INTO affectation (classe_id, matiere_id, formateur_id) VALUES
(1, 1, 2),  -- Dupont assure PHP pour BTS SIO2
(1, 2, 3);  -- Martin assure Anglais pour BTS SIO2

INSERT INTO mot_cle (libelle) VALUES
('Boucles'), ('Fonctions'), ('POO'), ('Grammaire'), ('Vocabulaire');

-- Questions PHP
INSERT INTO question (enonce, type, commentaire, affectee, auteur_id) VALUES
('Quelle fonction permet de hacher un mot de passe en PHP ?', 'unique', 'password_hash() est la fonction recommandee depuis PHP 5.5', TRUE, 2),
('Quels mots-cles permettent de declarer une boucle en PHP ?', 'multiple', 'for, foreach et while sont les 3 boucles principales', FALSE, 2),
('PHP est un langage interprete.', 'unique', 'Vrai, PHP est interprete cote serveur', FALSE, 2);

INSERT INTO reponse_possible (question_id, texte, est_correcte, ordre) VALUES
(1, 'password_hash()', TRUE, 1),
(1, 'md5()', FALSE, 2),
(1, 'base64_encode()', FALSE, 3),
(2, 'for', TRUE, 1),
(2, 'foreach', TRUE, 2),
(2, 'while', TRUE, 3),
(2, 'repeat', FALSE, 4),
(3, 'Vrai', TRUE, 1),
(3, 'Faux', FALSE, 2);

INSERT INTO question_mot_cle (question_id, mot_cle_id) VALUES
(1, 3), (2, 1), (3, 3);

-- QCM de démonstration
INSERT INTO qcm (titre, matiere_id, classe_id, formateur_id, date_debut, date_fin, nb_questions) VALUES
('QCM n1 - Bases PHP', 1, 1, 2, '2026-11-20 00:00:00', '2026-11-30 23:59:59', 1);

INSERT INTO qcm_question (qcm_id, question_id) VALUES
(1, 1);
