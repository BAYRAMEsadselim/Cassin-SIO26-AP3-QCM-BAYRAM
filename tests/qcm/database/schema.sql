CREATE DATABASE IF NOT EXISTS qcm_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qcm_platform;

CREATE TABLE utilisateur (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(80) NOT NULL,
  prenom VARCHAR(80) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  mot_de_passe_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','formateur','etudiant') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE matiere (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE formateur_matiere (
  formateur_id INT UNSIGNED NOT NULL,
  matiere_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (formateur_id, matiere_id),
  FOREIGN KEY (formateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
  FOREIGN KEY (matiere_id) REFERENCES matiere(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE classe (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(80) NOT NULL,
  annee SMALLINT UNSIGNED NOT NULL,
  UNIQUE (nom, annee)
) ENGINE=InnoDB;

CREATE TABLE classe_etudiant (
  classe_id INT UNSIGNED NOT NULL,
  etudiant_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (classe_id, etudiant_id),
  FOREIGN KEY (classe_id) REFERENCES classe(id) ON DELETE CASCADE,
  FOREIGN KEY (etudiant_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE classe_matiere (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  classe_id INT UNSIGNED NOT NULL,
  matiere_id INT UNSIGNED NOT NULL,
  formateur_id INT UNSIGNED NOT NULL,
  UNIQUE (classe_id, matiere_id),
  FOREIGN KEY (classe_id) REFERENCES classe(id) ON DELETE RESTRICT,
  -- le formateur doit assurer la matière (RG6)
  FOREIGN KEY (formateur_id, matiere_id)
    REFERENCES formateur_matiere(formateur_id, matiere_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE mot_cle (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE question (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  enonce TEXT NOT NULL,
  type ENUM('unique','multiple','negative','classement') NOT NULL,
  commentaire TEXT NULL,
  auteur_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (auteur_id) REFERENCES utilisateur(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE proposition (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  question_id INT UNSIGNED NOT NULL,
  libelle VARCHAR(500) NOT NULL,
  est_correcte TINYINT(1) NOT NULL DEFAULT 0,
  rang_correct TINYINT UNSIGNED NULL,   -- seulement pour le type classement
  FOREIGN KEY (question_id) REFERENCES question(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE question_mot_cle (
  question_id INT UNSIGNED NOT NULL,
  mot_cle_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (question_id, mot_cle_id),
  FOREIGN KEY (question_id) REFERENCES question(id) ON DELETE CASCADE,
  FOREIGN KEY (mot_cle_id) REFERENCES mot_cle(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE qcm (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(160) NOT NULL,
  classe_matiere_id INT UNSIGNED NOT NULL,
  date_debut DATETIME NOT NULL,
  date_fin DATETIME NOT NULL,           -- jour unique : 00:00:00 à 23:59:59
  nb_questions SMALLINT UNSIGNED NOT NULL,
  CHECK (date_fin >= date_debut),
  FOREIGN KEY (classe_matiere_id) REFERENCES classe_matiere(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE qcm_mot_cle (
  qcm_id INT UNSIGNED NOT NULL,
  mot_cle_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (qcm_id, mot_cle_id),
  FOREIGN KEY (qcm_id) REFERENCES qcm(id) ON DELETE CASCADE,
  FOREIGN KEY (mot_cle_id) REFERENCES mot_cle(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE qcm_question (
  qcm_id INT UNSIGNED NOT NULL,
  question_id INT UNSIGNED NOT NULL,
  position SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (qcm_id, question_id),
  FOREIGN KEY (qcm_id) REFERENCES qcm(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES question(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE tentative (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  qcm_id INT UNSIGNED NOT NULL,
  etudiant_id INT UNSIGNED NOT NULL,
  date_debut DATETIME NOT NULL,
  date_validation DATETIME NULL,        -- NULL = en cours
  score DECIMAL(5,2) NULL,
  UNIQUE (qcm_id, etudiant_id),
  FOREIGN KEY (qcm_id) REFERENCES qcm(id) ON DELETE CASCADE,
  FOREIGN KEY (etudiant_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reponse_etudiant (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tentative_id INT UNSIGNED NOT NULL,
  question_id INT UNSIGNED NOT NULL,
  proposition_id INT UNSIGNED NOT NULL,
  rang TINYINT UNSIGNED NULL,           -- seulement pour le type classement
  UNIQUE (tentative_id, question_id, proposition_id),
  FOREIGN KEY (tentative_id) REFERENCES tentative(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES question(id) ON DELETE RESTRICT,
  FOREIGN KEY (proposition_id) REFERENCES proposition(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Verrous de modification (RG1, RG2, RG3, RG9)
DELIMITER $$

CREATE TRIGGER trg_matiere_bu BEFORE UPDATE ON matiere FOR EACH ROW
BEGIN
  IF EXISTS (SELECT 1 FROM formateur_matiere WHERE matiere_id = OLD.id)
  OR EXISTS (SELECT 1 FROM classe_matiere WHERE matiere_id = OLD.id) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Matière affectée : modification interdite';
  END IF;
END$$

CREATE TRIGGER trg_mot_cle_bu BEFORE UPDATE ON mot_cle FOR EACH ROW
BEGIN
  IF EXISTS (SELECT 1 FROM question_mot_cle WHERE mot_cle_id = OLD.id) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Mot-clé utilisé : modification interdite';
  END IF;
END$$

CREATE TRIGGER trg_question_bu BEFORE UPDATE ON question FOR EACH ROW
BEGIN
  IF EXISTS (SELECT 1 FROM qcm_question WHERE question_id = OLD.id) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Question utilisée dans un QCM : modification interdite';
  END IF;
END$$

CREATE TRIGGER trg_reponse_bi BEFORE INSERT ON reponse_etudiant FOR EACH ROW
BEGIN
  IF (SELECT date_validation FROM tentative WHERE id = NEW.tentative_id) IS NOT NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tentative validée : réponses verrouillées';
  END IF;
END$$

DELIMITER ;
