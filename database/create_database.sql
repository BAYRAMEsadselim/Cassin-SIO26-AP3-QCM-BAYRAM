CREATE TABLE Matieres(
   idMatiere INT AUTO_INCREMENT,
   nomMatiere VARCHAR(50) ,
   PRIMARY KEY(idMatiere)
);

CREATE TABLE Utilisateurs(
   idUtilisateur INT AUTO_INCREMENT,
   nom VARCHAR(50) ,
   prenom VARCHAR(50) ,
   mail VARCHAR(20) ,
   motDePasse VARCHAR(100) ,
   PRIMARY KEY(idUtilisateur),
   UNIQUE(mail)
);

CREATE TABLE TypesRoles(
   idRole INT AUTO_INCREMENT,
   intitule VARCHAR(50) ,
   PRIMARY KEY(idRole)
);

CREATE TABLE Classes(
   IdClasse INT AUTO_INCREMENT,
   libelle VARCHAR(50) ,
   annee SMALLINT,
   PRIMARY KEY(IdClasse)
);

CREATE TABLE MotsClefs(
   IdMotClef INT AUTO_INCREMENT,
   libelle VARCHAR(50) ,
   PRIMARY KEY(IdMotClef)
);

CREATE TABLE TypesQuestion(
   IdTypeQuestion INT AUTO_INCREMENT,
   typeQuestion VARCHAR(50) ,
   PRIMARY KEY(IdTypeQuestion)
);

CREATE TABLE Qcms(
   IdQcm INT AUTO_INCREMENT,
   titre VARCHAR(50) ,
   dateDebut DATETIME,
   dateFin DATETIME,
   nbQuestions INT,
   idMatiere INT NOT NULL,
   PRIMARY KEY(IdQcm),
   FOREIGN KEY(idMatiere) REFERENCES Matieres(idMatiere)
);

CREATE TABLE Enseignements(
   IdEnseignement INT AUTO_INCREMENT,
   idFormateur INT,
   idMatiere INT,
   IdClasse INT,
   PRIMARY KEY(IdEnseignement),
   FOREIGN KEY(idFormateur) REFERENCES Utilisateurs(idUtilisateur),
   FOREIGN KEY(idMatiere) REFERENCES Matieres(idMatiere),
   FOREIGN KEY(IdClasse) REFERENCES Classes(IdClasse)
);

CREATE TABLE Copies(
   IdCopie INT AUTO_INCREMENT,
   dateValidation DATE,
   note INT,
   IdQcm INT NOT NULL,
   idEleve INT NOT NULL,
   PRIMARY KEY(IdCopie),
   FOREIGN KEY(IdQcm) REFERENCES Qcms(IdQcm),
   FOREIGN KEY(idEleve) REFERENCES Utilisateurs(idUtilisateur)
);

CREATE TABLE Questions(
   idQuestion INT AUTO_INCREMENT,
   titreQuestion TEXT,
   commentaire TEXT,
   idFormateur INT NOT NULL,
   IdTypeQuestion INT NOT NULL,
   PRIMARY KEY(idQuestion),
   FOREIGN KEY(idFormateur) REFERENCES Utilisateurs(idUtilisateur),
   FOREIGN KEY(IdTypeQuestion) REFERENCES TypesQuestion(IdTypeQuestion)
);

CREATE TABLE ReponsesPossibles(
   IdReponsePossible INT AUTO_INCREMENT,
   libelle VARCHAR(100) ,
   estCorrect BOOLEAN,
   rangReponse INT,
   idQuestion INT NOT NULL,
   PRIMARY KEY(IdReponsePossible),
   FOREIGN KEY(idQuestion) REFERENCES Questions(idQuestion)
);

CREATE TABLE Roles(
   idUtilisateur INT,
   idRole INT,
   PRIMARY KEY(idUtilisateur, idRole),
   FOREIGN KEY(idUtilisateur) REFERENCES Utilisateurs(idUtilisateur),
   FOREIGN KEY(idRole) REFERENCES TypesRoles(idRole)
);

CREATE TABLE Inscriptions(
   idEleve INT,
   IdClasse INT,
   PRIMARY KEY(idEleve, IdClasse),
   FOREIGN KEY(idEleve) REFERENCES Utilisateurs(idUtilisateur),
   FOREIGN KEY(IdClasse) REFERENCES Classes(IdClasse)
);

CREATE TABLE Cibles(
   IdMotClef INT,
   IdQcm INT,
   PRIMARY KEY(IdMotClef, IdQcm),
   FOREIGN KEY(IdMotClef) REFERENCES MotsClefs(IdMotClef),
   FOREIGN KEY(IdQcm) REFERENCES Qcms(IdQcm)
);

CREATE TABLE Compositions(
   idQuestion INT,
   IdQcm INT,
   PRIMARY KEY(idQuestion, IdQcm),
   FOREIGN KEY(idQuestion) REFERENCES Questions(idQuestion),
   FOREIGN KEY(IdQcm) REFERENCES Qcms(IdQcm)
);

CREATE TABLE Evaluations(
   IdQcm INT,
   IdEnseignement INT,
   PRIMARY KEY(IdQcm, IdEnseignement),
   FOREIGN KEY(IdQcm) REFERENCES Qcms(IdQcm),
   FOREIGN KEY(IdEnseignement) REFERENCES Enseignements(IdEnseignement)
);

CREATE TABLE Qualifications(
   idQuestion INT,
   IdMotClef INT,
   PRIMARY KEY(idQuestion, IdMotClef),
   FOREIGN KEY(idQuestion) REFERENCES Questions(idQuestion),
   FOREIGN KEY(IdMotClef) REFERENCES MotsClefs(IdMotClef)
);

CREATE TABLE ChoixReponses(
   IdReponsePossible INT,
   IdCopie INT,
   rangChoisi INT,
   PRIMARY KEY(IdReponsePossible, IdCopie),
   FOREIGN KEY(IdReponsePossible) REFERENCES ReponsesPossibles(IdReponsePossible),
   FOREIGN KEY(IdCopie) REFERENCES Copies(IdCopie)
);
