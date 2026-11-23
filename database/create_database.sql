-- ============================================================
-- Script de création de la base de données
-- Plateforme de QCM en ligne - Atelier de professionnalisation 3
-- Cohérent avec le MCD : FORMATEUR / ELEVE / ADMINISTRATEUR séparés
-- ============================================================

CREATE DATABASE IF NOT EXISTS qcm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qcm_db;

-- ============================================================
-- 1. Comptes
-- ============================================================

CREATE TABLE administrateur (
    id_administrateur  INT AUTO_INCREMENT PRIMARY KEY,
    nom                VARCHAR(100) NOT NULL,
    prenom             VARCHAR(100) NOT NULL,
    email              VARCHAR(190) NOT NULL UNIQUE,
    mot_de_passe       VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE formateur (
    id_formateur  INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    prenom        VARCHAR(100) NOT NULL,
    email         VARCHAR(190) NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE classe (
    id_class  INT AUTO_INCREMENT PRIMARY KEY,
    nom       VARCHAR(100) NOT NULL,
    annee     VARCHAR(9) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE eleve (
    id_eleve      INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    prenom        VARCHAR(100) NOT NULL,
    email         VARCHAR(190) NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255) NOT NULL,
    id_class      INT NULL,
    CONSTRAINT fk_eleve_classe FOREIGN KEY (id_class) REFERENCES classe(id_class) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE matiere (
    id_matiere  INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(150) NOT NULL,
    affectee    BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB;

-- ============================================================
-- 2. Affectation (association ternaire "affecter")
-- ============================================================

CREATE TABLE affectation (
    id_affectation  INT AUTO_INCREMENT PRIMARY KEY,
    id_formateur    INT NOT NULL,
    id_matiere      INT NOT NULL,
    id_class        INT NOT NULL,
    CONSTRAINT fk_affectation_formateur FOREIGN KEY (id_formateur) REFERENCES formateur(id_formateur) ON DELETE CASCADE,
    CONSTRAINT fk_affectation_matiere   FOREIGN KEY (id_matiere)   REFERENCES matiere(id_matiere)      ON DELETE CASCADE,
    CONSTRAINT fk_affectation_classe    FOREIGN KEY (id_class)     REFERENCES classe(id_class)          ON DELETE CASCADE,
    UNIQUE KEY uniq_affectation (id_matiere, id_class)
) ENGINE=InnoDB;

-- ============================================================
-- 3. QCM
-- ============================================================

CREATE TABLE qcm (
    id_qcm        INT AUTO_INCREMENT PRIMARY KEY,
    titre         VARCHAR(150) NOT NULL,
    nb_questions  INT NOT NULL,
    date_debut    DATETIME NOT NULL,
    date_fin      DATETIME NOT NULL,
    id_matiere    INT NOT NULL,
    id_class      INT NOT NULL,
    id_formateur  INT NOT NULL,
    CONSTRAINT fk_qcm_matiere   FOREIGN KEY (id_matiere)   REFERENCES matiere(id_matiere)     ON DELETE CASCADE,
    CONSTRAINT fk_qcm_classe    FOREIGN KEY (id_class)     REFERENCES classe(id_class)         ON DELETE CASCADE,
    CONSTRAINT fk_qcm_formateur FOREIGN KEY (id_formateur) REFERENCES formateur(id_formateur)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 4. Questions, réponses possibles, mots-clés
-- ============================================================

CREATE TABLE question (
    id_question  INT AUTO_INCREMENT PRIMARY KEY,
    enonce       TEXT NOT NULL,
    type         ENUM('unique', 'multiple', 'negative', 'classement') NOT NULL,
    commentaire  TEXT NULL,
    affectee     BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB;

CREATE TABLE qcm_question (
    id_qcm       INT NOT NULL,
    id_question  INT NOT NULL,
    PRIMARY KEY (id_qcm, id_question),
    CONSTRAINT fk_qq_qcm      FOREIGN KEY (id_qcm)      REFERENCES qcm(id_qcm)           ON DELETE CASCADE,
    CONSTRAINT fk_qq_question FOREIGN KEY (id_question) REFERENCES question(id_question) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reponse_possible (
    id_reponse_possible  INT AUTO_INCREMENT PRIMARY KEY,
    texte                VARCHAR(255) NOT NULL,
    est_correcte         BOOLEAN NOT NULL DEFAULT FALSE,
    id_question          INT NOT NULL,
    CONSTRAINT fk_reponse_question FOREIGN KEY (id_question) REFERENCES question(id_question) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mot_cle (
    id_mot_cle  INT AUTO_INCREMENT PRIMARY KEY,
    libelle     VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE question_mot_cle (
    id_question  INT NOT NULL,
    id_mot_cle   INT NOT NULL,
    PRIMARY KEY (id_question, id_mot_cle),
    CONSTRAINT fk_qmc_question FOREIGN KEY (id_question) REFERENCES question(id_question) ON DELETE CASCADE,
    CONSTRAINT fk_qmc_motcle   FOREIGN KEY (id_mot_cle)  REFERENCES mot_cle(id_mot_cle)   ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 5. Réponses des élèves (association ternaire "repondre")
-- ============================================================

CREATE TABLE reponse_eleve (
    id_reponse_eleve  INT AUTO_INCREMENT PRIMARY KEY,
    reponse_donnee    TEXT NOT NULL,
    est_correcte      BOOLEAN NOT NULL DEFAULT FALSE,
    id_eleve          INT NOT NULL,
    id_qcm            INT NOT NULL,
    id_question       INT NOT NULL,
    CONSTRAINT fk_re_eleve    FOREIGN KEY (id_eleve)    REFERENCES eleve(id_eleve)       ON DELETE CASCADE,
    CONSTRAINT fk_re_qcm      FOREIGN KEY (id_qcm)      REFERENCES qcm(id_qcm)           ON DELETE CASCADE,
    CONSTRAINT fk_re_question FOREIGN KEY (id_question) REFERENCES question(id_question) ON DELETE CASCADE,
    UNIQUE KEY uniq_reponse (id_eleve, id_qcm, id_question)
) ENGINE=InnoDB;

-- ============================================================
-- 6. Jeu de données de test
-- ============================================================

INSERT INTO classe (nom, annee) VALUES
('BTS SIO2 SLAM', '2026-2027'),
('BTS SIO1 SLAM', '2026-2027');

INSERT INTO administrateur (nom, prenom, email, mot_de_passe) VALUES
('SCHOSSIG', 'Frederic', 'admin@cassin.fr', 'a_faire');

INSERT INTO formateur (nom, prenom, email, mot_de_passe) VALUES
('DUPONT', 'Jean', 'j.dupont@cassin.fr', 'a_faire');

INSERT INTO eleve (nom, prenom, email, mot_de_passe, id_class) VALUES
('LE', 'Raphaël', 'le.raphael67@gmail.com', 'a_faire', 1),
('BAYRAM', 'Esad', 'bayramesadselim@gmail.com', 'a_faire', 1),
('MEYER', 'Mattéo', 'lyonmatteo@gmail.com', 'a_faire', 1);

INSERT INTO matiere (nom, affectee) VALUES
('PHP / Développement web', TRUE),
('Anglais', FALSE);

INSERT INTO affectation (id_formateur, id_matiere, id_class) VALUES
(1, 1, 1);

INSERT INTO mot_cle (libelle) VALUES
('Boucles'), ('Fonctions'), ('POO');

INSERT INTO question (enonce, type, commentaire, affectee) VALUES
('Quelle fonction permet de hacher un mot de passe en PHP ?', 'unique', 'password_hash() est la fonction recommandee depuis PHP 5.5', TRUE),
('Quels mots-cles permettent de declarer une boucle en PHP ?', 'multiple', 'for, foreach et while sont les 3 boucles principales', FALSE);

INSERT INTO reponse_possible (texte, est_correcte, id_question) VALUES
('password_hash()', TRUE, 1),
('md5()', FALSE, 1),
('for', TRUE, 2),
('foreach', TRUE, 2),
('while', TRUE, 2),
('repeat', FALSE, 2);

INSERT INTO question_mot_cle (id_question, id_mot_cle) VALUES
(1, 3), (2, 1);

INSERT INTO qcm (titre, nb_questions, date_debut, date_fin, id_matiere, id_class, id_formateur) VALUES
('QCM n1 - Bases PHP', 1, '2026-11-20 00:00:00', '2026-11-30 23:59:59', 1, 1, 1);

INSERT INTO qcm_question (id_qcm, id_question) VALUES
(1, 1);
