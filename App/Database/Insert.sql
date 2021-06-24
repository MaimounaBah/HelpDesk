-- COMPTES

INSERT INTO COMPTES (id_compte, email_compte, mdp_compte, privilege_compte) VALUES 

(111, 'caroline.dubois@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'administrateur'),
(112, 'jean.gautier@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'administrateur'),

(1, 'emmeline.charbonneau@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'agent_admin'),
(2, 'fantina.sorel@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'agent_admin'),

(3, 'franck.ravat@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'enseignant'),
(4, 'sylvie.doutre@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'enseignant'),
(5, 'nathalie.valles@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'enseignant'),

(6, 'r.monlouis@gmail.com', '37fa265330ad83eaa879efb1e2db6380896cf639', 'etudiant'),
(7, 'maimouna.bah@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'etudiant'),
(8, 'vlada.stegarescu@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'etudiant'),
(9, 'amir.said@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'etudiant'),
(10, 'mamoudou.kaba@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'etudiant'),
(11, 'david.chartelain@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'etudiant'),
(12, 'miguel.mariesainte@ut-capitole.fr', '37fa265330ad83eaa879efb1e2db6380896cf639', 'etudiant');

-- PROMOTIONS
INSERT INTO PROMOTIONS (id_promotion, nom_promotion, annee_promotion) VALUES
(1, "L3 MIASHS TI", 2020),
(2, "M1 MIAGE IM", 2020),
(3, "M1 MIAGE 2IS", 2020),
(4, "M2 MIAGE 2IS", 2020),
(5, "M2 MIAGE IPM", 2020),
(6, "M2 MIAGE ISIAD", 2020);

-- ADMINISTRATEURS
INSERT INTO ADMINISTRATEURS (id_administrateur, prenom_administrateur, nom_administrateur, sexe_administrateur, id_compte) VALUES
(39439496, 'Caroline', 'Dubois', 'F', 111),
(39394396, 'Jean', 'Gautier', 'H', 112);

-- AGENTS ADMINISTRATIFS
INSERT INTO AGENTS_ADMINS (id_agent_admin, prenom_agent_admin, nom_agent_admin, sexe_agent_admin, id_compte) VALUES
(23234566, 'Emmeline', 'Charbonneau', 'F', 1),
(45345556, 'Fantina', 'Sorel', 'F', 2);

-- ENSEIGNANTS
INSERT INTO ENSEIGNANTS (id_enseignant, prenom_enseignant, nom_enseignant, sexe_enseignant, id_compte) VALUES
(24537456, 'Frank', 'Ravat', 'H', 3),
(43848586, 'Sylvie', 'Doutre', 'F', 4),
(43848546, 'Nathalie', 'Valles', 'F', 5);

-- ETUDIANTS
INSERT INTO ETUDIANTS (id_etudiant, prenom_etudiant, nom_etudiant, sexe_etudiant, id_compte, id_promotion) VALUES
(23448844, 'ruddy', 'monlouis', 'H', 6, 2),
(22344457, 'maimouna', 'bah', 'F', 7, 2),
(21456778, 'vlada', 'stegarescu', 'F', 8, 2),
(38383837, 'amir', 'said', 'H', 9, 3),
(23456754, 'mamoudou', 'kaba', 'H', 10, 1),
(23838386, 'david', 'chartelain', 'H', 11, 1),
(26378486, 'miguel', 'marie-sainte', 'H', 11, 3);

-- CATEGORIES
INSERT INTO CATEGORIES (id_categorie, nom_categorie) VALUES
(1, "Demande d'aide"),
(2, "Demande d'informations"),
(3, "Dépladement d'un cours"),
(4, "Vie universitaire"),
(5, "Demande de document"),
(6, 'Autres');

