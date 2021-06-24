<?php


    /**
     * Afficher tous les compte sauf celui rentré en paramêtre
     * @return array
     */
    function AfficherComptes($connexion, $id_compte){

        $comptes = array();
        $query = "SELECT * FROM COMPTES WHERE id_compte <> ?";
        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));

            $comptes = get_result($stmt);

        } else die(mysqli_error($connexion));

        return $comptes;
    }

    /**
     * Afficher l'ensemble des promotion de la base de données
     * @return array
     */
    function AfficherPromotions($connexion){

        $promotions = array();
        $query = "SELECT * FROM PROMOTIONS";

        if ($result = mysqli_query($connexion, $query))
        {
            while ($ligne = mysqli_fetch_assoc($result))
            {
                $promotions[] = $ligne;
            }
        } else die(mysqli_error($connexion));

        return $promotions;
    }

    /**
     * Afficher l'email des administrateur de l'application
     * @return array
     */
    function AfficherEmailAdministrateurs($connexion){

        $email = array();
        $query = "SELECT email_compte FROM COMPTES  WHERE privilege_compte = 'administrateur'";

        if ($result = mysqli_query($connexion, $query))
        {
            while ($ligne = mysqli_fetch_assoc($result))
            {
                $email[] = $ligne;
            }
        } else die(mysqli_error($connexion));

        return $email;
    }

    /**
     * Afficher les informations d'un compte
     * @return array
     */
    function AfficherUnCompte($connexion, $id_compte){

        $query = "SELECT * FROM COMPTES WHERE id_compte = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 's', $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);

            return array_shift($result);

        } else die(mysqli_error($connexion));
    }

    /**
     * Afficher les informations d'un administrateur
     * @return array
     */
    function AfficherUnAdministrateur($connexion, $id_compte){

        $query = "SELECT * FROM ADMINISTRATEURS WHERE id_compte = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);

            return array_shift($result);

        } else die(mysqli_error($connexion));
    }

    /**
     * Afficher les informations d'un agent administratif
     * @return array
     */
    function AfficherUnAgentAdmin($connexion, $id_compte){

        $query = "SELECT * FROM AGENTS_ADMINS WHERE id_compte = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);

            return array_shift($result);

        } else die(mysqli_error($connexion));
    }

    /**
     * Afficher les informations d'un Enseignant
     * @return array
     */
    function AfficherUnEnseignant($connexion, $id_compte){

        $query = "SELECT * FROM ENSEIGNANTS WHERE id_compte = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);

            return array_shift($result);

        } else die(mysqli_error($connexion));
    }

    /**
     * Afficher les informations d'un étudiant
     * @return array
     */
    function AfficherUnEtudiant($connexion, $id_compte){

        $query = "SELECT * FROM ETUDIANTS WHERE id_compte = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);

            return array_shift($result);

        } else die(mysqli_error($connexion));
    }

    /**
     * Afficher les informations d'une photo
     * @return array
     */
    function AfficherUnePhoto($connexion, $id_photo){

        $query = "SELECT * FROM PHOTOS WHERE id_photo = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_photo);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);

            return array_shift($result);

        } else die(mysqli_error($connexion));
    }

    /**
     * Afficher un utilisateur en fontion de son privilège (Ex: etudiant, enseignant ou agent_admin)
     * @return array
     */
    function AfficherUtilisateur($connexion, $id_compte, $privilege){

        $utilisateur = null;
        
        switch ($privilege) {
            case 'etudiant':
                $utilisateur = AfficherUnEtudiant($connexion, $id_compte);
                break;
            case 'enseignant':
                $utilisateur = AfficherUnEnseignant($connexion, $id_compte);
                break;
            case 'agent_admin':
                $utilisateur = AfficherUnAgentAdmin($connexion, $id_compte);
                break;
            case 'administrateur':
                $utilisateur = AfficherUnAdministrateur($connexion, $id_compte);
                break;
        }

        return $utilisateur;
    }

    /**
     * Vérifier l'existance d'un email dans la base de donnnée autre que le compte rentré en paremêtre
     * Retourne le nombre de ligne renvoyées par la requête
     * @return integer
     */
    function ExisteAutreEmail($connexion, $email, $id_compte){

        $email = htmlspecialchars(strip_tags($email));
        $query = "SELECT * 
                FROM COMPTES 
                WHERE email_compte = ? 
                AND id_compte <> ? ";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, "si", $email, $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            mysqli_stmt_store_result($stmt);
            return mysqli_stmt_num_rows($stmt);
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Vérifier l'existance d'un email dans la base de donnnée
     * Retourne le nombre de ligne renvoyées par la requête
     * @return integer
     */
    function ExisteEmail($connexion, $email){

        $email = htmlspecialchars(strip_tags($email));
        $query = "SELECT * FROM COMPTES WHERE email_compte = ? ";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            mysqli_stmt_store_result($stmt);
            return mysqli_stmt_num_rows($stmt);
        }
        else die(mysqli_error($connexion));
    }

    /**
     * Vérifier qu'une récuperation existe dans la base de données
     * @return boolean
     */
    function ExisteRecuperation($connexion, $email){

        $email = htmlspecialchars(strip_tags($email));
        $query = "SELECT * FROM RECUPERATION WHERE email_recuperation = ? ";

        $stmt = mysqli_prepare($connexion, $query);
        
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $rowcount = mysqli_stmt_num_rows($stmt);

        return $rowcount;
    }

    /**
     * Confirmer que le code de récupération envoyé par mail à bien été vérifié
     * @return boolean
     */
    function ConfirmerRecuperation($connexion, $email_recuperation){

        $query = "UPDATE RECUPERATION SET confirm_recuperation = 1 WHERE email_recuperation = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 's', $email_recuperation);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    } 

    /**
     * Creer un compte dans la base de données
     * @return boolean
     */
    function CreerCompte($connexion, $mdp_compte, $email_compte, $privilege_compte){
        
        $query = "INSERT INTO COMPTES (mdp_compte, email_compte, privilege_compte)
                  VALUES (?, ?, ?)";
        
        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'sss', $mdp_compte, $email_compte, $privilege_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            return mysqli_insert_id($connexion);
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Creer un enseignant dans la base de données
     * @return boolean
     */
    function CreerEnseignant($connexion, $id_enseignant, $nom_enseignant, $prenom_enseignant, $id_compte){

        $query = "INSERT INTO ENSEIGNANTS (id_enseignant, nom_enseignant, prenom_enseignant, id_compte) VALUES (?, ?, ?, ?)";

        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'issi', $id_enseignant, $nom_enseignant, $prenom_enseignant, $id_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Creer un étudiant dans la base de données
     * @return boolean
     */
    function CreerEtudiant($connexion, $id_etudiant, $nom_etudiant, $prenom_etudiant, $id_compte){

        $query = "INSERT INTO ETUDIANTS (id_etudiant, nom_etudiant, prenom_etudiant, id_compte) VALUES (?, ?, ?, ?)";

        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'issi', $id_etudiant, $nom_etudiant, $prenom_etudiant, $id_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Creer une photo dans la base de données
     * @return boolean
     */
    function CreerPhoto($connexion, $libelle_photo, $taille_photo, $format_photo){

        $query = "INSERT INTO PHOTOS (libelle_photo, taille_photo, format_photo) VALUES (?, ?, ?)";

        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'sis', $libelle_photo, $taille_photo, $format_photo);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            return mysqli_insert_id($connexion);
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Créer une récupération dans la base de donnée
     * @return boolean
     */
    function CreerRecuperation($connexion, $code_recuperation, $email_recuperation){

        $query = "INSERT INTO RECUPERATION (code_recuperation, email_recuperation) VALUES (?, ?)";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ss', $code_recuperation, $email_recuperation);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Enregistrer un ficher sur le serveur
     * @return boolean
     */
    function EnregistrerFichier($id_fichier, $nom_fichier, $format_fichier, $fichier_tmp, $destination){

        // $nom_fichier = $nom_piece_jointe.'.'.$format_piece_jointe;
        $destination .= '/'.$id_fichier;
        
        if(file_exists($destination))
        {
            $files = glob($destination.'/*');
            foreach($files as $file) {
                if(is_file($file)) 
                    // Delete the given file
                    unlink($file); 
            }

            $destination .= '/'.$nom_fichier.'.'.$format_fichier;
        } else {
            mkdir($destination, 0777);
            chmod($destination, 0777);
            $destination .= '/'.$nom_fichier.'.'.$format_fichier;
        }

        if(move_uploaded_file($fichier_tmp, $destination))
        {
            chmod($destination, 0777);
            return true;
        } 
        else{
            return false;
        } 
    }

    /**
     * Récupérer l'identifiant d'un compte à partir de son adresse mail
     * @return integer
     */
    function GetIdCompte($connexion, $email){

        $id_compte = 0;
        $query = "SELECT id_compte FROM COMPTES WHERE email_compte = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = get_result($stmt);
            $id_compte = array_shift($result)['id_compte'];

        } else die(mysqli_error($connexion));

        return $id_compte;
    }

    /**
     * Récupère un jeu de résultat à partir d'une requête préparée
     * @return array
     */
    function get_result(\mysqli_stmt $statement){

        $result = array();

        // Stocke un jeu de résultats depuis une requête préparée
        mysqli_stmt_store_result($statement);

        // On boucle sur le nombre de ligne de la requête
        for ($i = 0; $i < mysqli_stmt_num_rows($statement); $i++)
        {
            // On récupère métadonnées de préparation de la requête
            $metadata = mysqli_stmt_result_metadata($statement);
            $params = array();
            // Pour chaque champs de la ligne
            while ($field = mysqli_fetch_field($metadata))
            {
                $params[] = &$result[$i][$field->name];
            }

            // On bind le resultat en passant la fonction bind_result, le stmt et paremètres
            call_user_func_array(array($statement, 'bind_result'), $params);
            mysqli_stmt_fetch($statement);
        }
        return $result;
    }

    /**
     * Modifier un compte dans la base de données
     * @return boolean
     */
    function ModifierCompte($connexion, $id_compte, $email_compte, $id_photo){

        $query = "UPDATE COMPTES 
                SET email_compte = ?, id_photo = ?
                WHERE id_compte = ?";
        
        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'sii', $email_compte, $id_photo, $id_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));

        } else die(mysqli_error($connexion));

    }

    /**
     * Modifier une photo dans la base de données
     * @return integer
     */
    function ModifierPhoto($connexion, $id_photo, $libelle_photo, $taille_photo, $format_photo){

        $query = "UPDATE PHOTOS 
                SET libelle_photo = ?, taille_photo = ?, format_photo = ?
                WHERE id_photo = ?";

        if($stmt = mysqli_prepare($connexion, $query)){
            mysqli_stmt_bind_param($stmt, 'sisi', $libelle_photo, $taille_photo, $format_photo, $id_photo);
            
            if(mysqli_stmt_execute($stmt)){
                return true;
            }
            else die('ModifierPhoto'.mysqli_error($connexion));

        }
        else die(mysqli_error($connexion));
    }

    /**
     * Modifier un administrateur dans la base de données
     * @return boolean
     */
    function ModifierUnAdministrateur($connexion, $nom_agent_admin, $prenom_agent_admin, $sexe_agent_admin, $id_compte){

        $query = "UPDATE ADMINISTRATEURS
                SET nom_administrateur = ?, prenom_administrateur = ?, sexe_administrateur = ? 
                WHERE id_compte = ?";

        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'sssi', $nom_agent_admin, $prenom_agent_admin, $sexe_agent_admin, $id_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        }
        else die(mysqli_error($connexion));
    }

    /**
     * Modifier un agent administratif dans la base de données
     * @return boolean
     */
    function ModifierUnAgentAdmin($connexion, $nom_agent_admin, $prenom_agent_admin, $sexe_agent_admin, $id_compte){

        $query = "UPDATE AGENTS_ADMINS
                SET nom_agent_admin = ?, prenom_agent_admin = ?, sexe_agent_admin = ? 
                WHERE id_compte = ?";

        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'sssi', $nom_agent_admin, $prenom_agent_admin, $sexe_agent_admin, $id_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        }
        else die(mysqli_error($connexion));
    }

    /**
     * Modifier un enseignant dans la base de données
     * @return boolean
     */
    function ModifierUnEnseignant($connexion, $nom_enseignant, $prenom_enseignant, $sexe_enseignant, $id_compte){

        $query = "UPDATE ENSEIGNANTS
                SET nom_enseignant = ?, prenom_enseignant = ?, sexe_enseignant = ?
                WHERE id_compte = ?";

        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'sssi', $nom_enseignant, $prenom_enseignant, $sexe_enseignant, $id_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        }
        else die(mysqli_error($connexion));
    }

    /**
     * Modifier un étudiant dans la base de données
     * @return boolean
     */
    function ModifierUnEtudiant($connexion, $nom_etudiant, $prenom_etudiant, $sexe_etudiant, $id_promotion, $id_compte){

        $query = "UPDATE ETUDIANTS
                    SET nom_etudiant = ?, prenom_etudiant = ?, sexe_etudiant = ?, id_promotion = ? 
                    WHERE id_compte = ?";

        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'sssii', $nom_etudiant, $prenom_etudiant, $sexe_etudiant, $id_promotion, $id_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        }
        else die(mysqli_error($connexion));
    }

    /**
     * Modifier le mot de passe d'un compte
     * @return boolean
     */
    function ModifierMdp($connexion, $email_compte, $mdp_compte){
        
        $query = "UPDATE COMPTES SET mdp_compte = ? WHERE email_compte = ?";
        
        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ss', $mdp_compte, $email_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }
      
    /**
     * Vérifier qu'un utilisateur à bien confirmer son mot de passe reçu par mail
     * @return integer
     */
    function VerifierConfirmationRecuperation($connexion, $email_recuperation){
        
        $query = "SELECT confirm_recuperation FROM RECUPERATION WHERE email_recuperation = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, "s", $email_recuperation);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);

            return  array_shift($result)['confirm_recuperation'];
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Vérifier le mot de passe d'un utilisateur
     * @return boolean
     */
    function VerifierMdp($connexion, $email, $mdp){

        $query = "SELECT mdp_compte FROM COMPTES WHERE email_compte = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);
            $mdp_compte = array_shift($result)['mdp_compte'];

            return sha1($mdp) == $mdp_compte ? true : false;
    
        } else die(mysqli_error($connexion));

    }

    /**
     * Vérifier que le code de récuperation rentré par l'utilisateur correspond à sont email
     * Retourne le nombre de ligne renvoyées par la requête
     * @return integer
     */
    function VerifierRecuperation($connexion, $code_recuperation, $email_recuperation){

        $query = "SELECT * FROM RECUPERATION WHERE code_recuperation = ? AND email_recuperation = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, "ss", $code_recuperation, $email_recuperation);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            mysqli_stmt_store_result($stmt);
            return mysqli_stmt_num_rows($stmt);
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Modifier une récupération dans la base de données
     * @return boolean
     */
    function ModifierRecuperation($connexion, $code_recuperation, $email_recuperation){

        $query = "UPDATE RECUPERATION SET code_recuperation = ? WHERE email_recuperation = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ss', $code_recuperation, $email_recuperation);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));

        return false;
    }

    /**
     * Retirer l'identifiant d'une photo à une compte
     * @return integer
     */
    function RetirerPhotoCompte($connexion, $id_compte){

        $query = "UPDATE COMPTES SET id_photo = null WHERE id_compte = ?";
        
        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Supprimer un dossier sur le serveur
     * @return boolean
     */
    function SupprimerDossier($id_dossier, $destination){

        $destination .= '/'.$id_dossier;
        
        $files = glob($destination.'/*');
        foreach ($files as $file){
            if (is_file($file)) 
                unlink($file); 
        }
        return rmdir($destination);
    }

    /**
     * Supprimer une récupération dans la base de donnée
     * @return boolean
     */
    function SupprimerRecuperation($connexion, $email_recuperation){
        
        $query = "DELETE FROM RECUPERATION WHERE email_recuperation = ?";
        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 's', $email_recuperation);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Créer une photo dans la base de données
     * Retourne l'identifiant de la photo
     * @return integer
     */
    function SupprimerPhoto($connexion, $id_photo){

        $query = "DELETE FROM PHOTOS WHERE id_photo = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_photo);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * @return array
     */
    function Sexe(){
        return array(
            'H' => 'Homme',
            'F' => 'Femme', 
            'A' => 'Autre'
        );
    }