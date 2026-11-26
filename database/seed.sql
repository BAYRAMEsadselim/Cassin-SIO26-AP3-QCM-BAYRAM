USE qcm_platform;
-- Compte de DÉVELOPPEMENT uniquement : admin@example.test / admin123 (à changer ou supprimer en production)
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe_hash, role)
VALUES ('Admin', 'Premier', 'admin@example.test', '$2y$10$ujI1SCqCNx9Nmdt5O4vNT..jsMHJiKJymYlf.BBrvBjYTg9RNkPdG', 'admin');
INSERT INTO matiere (libelle) VALUES ('Algorithmique'), ('Bases de données');
