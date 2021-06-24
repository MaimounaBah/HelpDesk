
-- ASSOCIATIONS
DROP TABLE IF EXISTS COMPOSER;
DROP TABLE IF EXISTS DISPOSER;
DROP TABLE IF EXISTS INTERVENIR;
DROP TABLE IF EXISTS RECEVOIR_TICKET;
DROP TABLE IF EXISTS CONTIENT_TICKET;
DROP TABLE IF EXISTS CONTIENT_REPONSE;
DROP TABLE IF EXISTS SUIVRE_COURS;
DROP TABLE IF EXISTS RECUPERATION;

-- ENTITES
DROP TABLE IF EXISTS COURS;
DROP TABLE IF EXISTS ETUDIANTS;
DROP TABLE IF EXISTS PROMOTIONS;
DROP TABLE IF EXISTS SECRETARIATS;
DROP TABLE IF EXISTS REPONSES;
DROP TABLE IF EXISTS TICKETS;
DROP TABLE IF EXISTS CATEGORIES;
DROP TABLE IF EXISTS AGENTS_ADMINS;
DROP TABLE IF EXISTS ENSEIGNANTS;
DROP TABLE IF EXISTS ADMINISTRATEURS;
DROP TABLE IF EXISTS COMPTES;
DROP TABLE IF EXISTS PHOTOS;
DROP TABLE IF EXISTS PIECES_JOINTES;

-- -----------------------------------------------------------------------------
--       TABLE : RECUPERATION
-- -----------------------------------------------------------------------------

    CREATE TABLE RECUPERATION
    (
        id_recuperation INT PRIMARY KEY AUTO_INCREMENT,
        email_recuperation VARCHAR(50),
        code_recuperation CHAR(8),
        confirm_recuperation INT(1) DEFAULT 0

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : COURS
-- -----------------------------------------------------------------------------

    CREATE TABLE COURS
    (
        id_cours INT PRIMARY KEY AUTO_INCREMENT,
        nom_cours VARCHAR(50),
        type_cours VARCHAR(50)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : SECRETARIATS
-- -----------------------------------------------------------------------------

    CREATE TABLE SECRETARIATS
    (
        id_secrerariat INT PRIMARY KEY AUTO_INCREMENT,
        nom_secretariat VARCHAR(50),
        tel_secretariat VARCHAR(50)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : PIECES_JOINTES
-- -----------------------------------------------------------------------------

    CREATE TABLE PIECES_JOINTES
    (
        id_piece_jointe INT PRIMARY KEY AUTO_INCREMENT,
        libelle_piece_jointe VARCHAR(255),
        taille_piece_jointe FLOAT,
        format_piece_jointe VARCHAR(50)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------------------------------------------------------
--       TABLE : PHOTOS
-- -----------------------------------------------------------------------------

    CREATE TABLE PHOTOS
    (
        id_photo INT PRIMARY KEY AUTO_INCREMENT,
        libelle_photo VARCHAR(255),
        taille_photo INT,
        format_photo VARCHAR(50)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : PROMOTIONS
-- -----------------------------------------------------------------------------

    CREATE TABLE PROMOTIONS
    (
        id_promotion INT PRIMARY KEY AUTO_INCREMENT,
        nom_promotion VARCHAR(50),
        annee_promotion INT(4),
        id_secrerariat INT NULL,

        FOREIGN KEY(id_secrerariat) REFERENCES SECRETARIATS(id_secrerariat)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : COMPTES
-- -----------------------------------------------------------------------------

    CREATE TABLE COMPTES
    (
        id_compte INT PRIMARY KEY AUTO_INCREMENT,
        email_compte VARCHAR(50) NOT NULL UNIQUE,
        mdp_compte VARCHAR(50) NOT NULL,
        privilege_compte VARCHAR(20) NOT NULL CHECK(privilege_compte IN ('etudiant', 'enseignant', 'agent_admin', 'administrateur')),
        id_photo INT,

        FOREIGN KEY(id_photo) REFERENCES PHOTOS(id_photo)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : ETUDIANTS
-- -----------------------------------------------------------------------------

    CREATE TABLE ETUDIANTS
    (
        id_etudiant INT(8) PRIMARY KEY,
        nom_etudiant VARCHAR(50) NOT NULL,
        prenom_etudiant VARCHAR(50) NOT NULL,
        sexe_etudiant CHAR(1) CHECK (sexe_etudiant IN ('F','H','A')),
        id_compte INT NOT NULL,
        id_promotion INT,

        FOREIGN KEY(id_compte) REFERENCES COMPTES(id_compte),
        FOREIGN KEY(id_promotion) REFERENCES PROMOTIONS(id_promotion)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : AGENTS_ADMINS
-- -----------------------------------------------------------------------------

    CREATE TABLE AGENTS_ADMINS
    (
        id_agent_admin INT PRIMARY KEY AUTO_INCREMENT,
        nom_agent_admin VARCHAR(50) NOT NULL,
        prenom_agent_admin VARCHAR(50) NOT NULL,
        sexe_agent_admin CHAR(1) CHECK (sexe_agent_admin IN ('F','H','A')),
        id_compte INT NOT NULL,

        FOREIGN KEY(id_compte) REFERENCES COMPTES(id_compte)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : ENSEIGNANTS
-- -----------------------------------------------------------------------------

    CREATE TABLE ENSEIGNANTS
    (
        id_enseignant INT PRIMARY KEY AUTO_INCREMENT,
        nom_enseignant VARCHAR(50),
        prenom_enseignant VARCHAR(50),
        sexe_enseignant CHAR(1) CHECK (sexe_enseignant IN ('F','H','A')),
        id_compte INT NOT NULL,

        FOREIGN KEY(id_compte) REFERENCES COMPTES(id_compte)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : ADMINISTRATEURS
-- -----------------------------------------------------------------------------

    CREATE TABLE ADMINISTRATEURS
    (
        id_administrateur INT PRIMARY KEY AUTO_INCREMENT,
        nom_administrateur VARCHAR(50),
        prenom_administrateur VARCHAR(50),
        sexe_administrateur CHAR(1) CHECK (sexe_administrateur IN ('F','H','A')),
        id_compte INT NOT NULL,

        FOREIGN KEY(id_compte) REFERENCES COMPTES(id_compte)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : CATEGORIES
-- -----------------------------------------------------------------------------

    CREATE TABLE CATEGORIES
    (
        id_categorie INT PRIMARY KEY AUTO_INCREMENT,
        nom_categorie VARCHAR(50) NOT NULL

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : TICKETS
-- -----------------------------------------------------------------------------

    CREATE TABLE TICKETS
    (
        id_ticket INT PRIMARY KEY AUTO_INCREMENT,
        sujet_ticket VARCHAR(50) NOT NULL,
        contenu_ticket TEXT NOT NULL,
        date_creation_ticket TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        date_cloture_ticket TIMESTAMP NULL,
        corbeille_ticket INT(1) NOT NULL DEFAULT 0 CHECK (corbeille_ticket BETWEEN 0 AND 1),
        id_categorie INT NOT NULL,
        id_compte INT NOT NULL,
        
        FOREIGN KEY(id_categorie) REFERENCES CATEGORIES(id_categorie),
        FOREIGN KEY(id_compte) REFERENCES COMPTES(id_compte)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : REPONSES
-- -----------------------------------------------------------------------------

    CREATE TABLE REPONSES
    (
        id_reponse INT PRIMARY KEY AUTO_INCREMENT,
        contenu_reponse TEXT NOT NULL,
        date_creation_reponse TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        vu_reponse INT(1) NOT NULL DEFAULT 0 CHECK (vu_reponse BETWEEN 0 AND 1),
        id_compte INT NOT NULL,
        id_ticket INT NOT NULL,

        FOREIGN KEY(id_ticket) REFERENCES TICKETS(id_ticket)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : SUIVRE_COURS
-- -----------------------------------------------------------------------------

    CREATE TABLE SUIVRE_COURS
    (
        id_etudiant INT(8),
        id_cours INT,
        semestre VARCHAR(50),

        PRIMARY KEY(id_etudiant, id_cours),
        FOREIGN KEY(id_etudiant) REFERENCES ETUDIANTS(id_etudiant),
        FOREIGN KEY(id_cours) REFERENCES COURS(id_cours)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : COMPOSER
-- -----------------------------------------------------------------------------

    CREATE TABLE COMPOSER
    (
        id_secrerariat INT,
        id_agent_admin INT,

        PRIMARY KEY(id_secrerariat, id_agent_admin),
        FOREIGN KEY(id_secrerariat) REFERENCES SECRETARIATS(id_secrerariat),
        FOREIGN KEY(id_agent_admin) REFERENCES AGENTS_ADMINS(id_agent_admin)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : DISPOSER
-- -----------------------------------------------------------------------------

    CREATE TABLE DISPOSER
    (
        id_cours INT,
        id_enseignant_referent INT,

        PRIMARY KEY(id_cours, id_enseignant_referent),
        FOREIGN KEY(id_cours) REFERENCES COURS(id_cours),
        FOREIGN KEY(id_enseignant_referent) REFERENCES ENSEIGNANTS(id_enseignant)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : INTERVENIR
-- -----------------------------------------------------------------------------

    CREATE TABLE INTERVENIR
    (
        id_cours INT,
        id_enseignant INT,

        PRIMARY KEY(id_cours, id_enseignant),
        FOREIGN KEY(id_cours) REFERENCES COURS(id_cours),
        FOREIGN KEY(id_enseignant) REFERENCES ENSEIGNANTS(id_enseignant)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : RECEVOIR_TICKET
-- -----------------------------------------------------------------------------

    CREATE TABLE RECEVOIR_TICKET
    (
        id_ticket INT,
        id_compte INT,
        vu_ticket INT(1) NOT NULL DEFAULT 0 CHECK (vu_ticket BETWEEN 0 AND 1),
        corbeille_ticket INT(1) NOT NULL DEFAULT 0 CHECK (corbeille_ticket BETWEEN 0 AND 1),

        PRIMARY KEY(id_ticket, id_compte),
        FOREIGN KEY(id_ticket) REFERENCES TICKETS(id_ticket),
        FOREIGN KEY(id_compte) REFERENCES COMPTES(id_compte)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : CONTIENT_TICKET
-- -----------------------------------------------------------------------------

    CREATE TABLE CONTIENT_TICKET
    (
        id_ticket INT,
        id_piece_jointe INT,

        PRIMARY KEY(id_ticket, id_piece_jointe),
        FOREIGN KEY(id_ticket) REFERENCES TICKETS(id_ticket),
        FOREIGN KEY(id_piece_jointe) REFERENCES PIECES_JOINTES(id_piece_jointe)

    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------------------------
--       TABLE : CONTIENT_REPONSE
-- -----------------------------------------------------------------------------

    CREATE TABLE CONTIENT_REPONSE
    (
        id_piece_jointe INT,
        id_reponse INT,

        PRIMARY KEY(id_piece_jointe, id_reponse),
        FOREIGN KEY(id_piece_jointe) REFERENCES PIECES_JOINTES(id_piece_jointe),
        FOREIGN KEY(id_reponse) REFERENCES REPONSES(id_reponse)
        
    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
