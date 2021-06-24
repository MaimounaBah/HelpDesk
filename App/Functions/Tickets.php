<?php

    /**
     * Afficher l'ensemble des catégorie que peut avoir un ticket
     * @return array
     */
    function AfficherCategories($connexion){

        $categories = array();
        $query = "SELECT * FROM CATEGORIES";

        if ($result = mysqli_query($connexion, $query))
        {
            while ($ligne = mysqli_fetch_assoc($result))
            {
                $categories[] = $ligne;
            }
        } else die(mysqli_error($connexion));

        return $categories;
    }

    /**
     * Recupérer les destinataires d'un ticket
     * @return array
     */
    function AfficherDestinatairesTicket($connexion, $id_ticket){

        $destinataires = [];
        $query = "SELECT C.*
        FROM COMPTES C, RECEVOIR_TICKET RT
        WHERE C.id_compte = RT.id_compte
        AND RT.id_ticket = ? ";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $destinataires = get_result($stmt);
             
        } else die(mysqli_error($connexion));

        return $destinataires;
    }

    /**
     * Afficher l'expéditeur d'un ticket
     * @return array
     */
    function AfficherExpediteurTicket($connexion, $id_ticket){
        
        $query = "SELECT C.*
                FROM COMPTES C, TICKETS T
                WHERE C.id_compte = T.id_compte
                AND T.id_ticket = ? 
                ";

        if($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);

            return array_shift($result);

        } else die(mysqli_error($connexion));
    }

    /**
     * Afficher une liste des identifiants des pièces jointes liées à une réponse
     * @return array
     */
    function AfficherIdsPiecesJointesReponse($connexion, $id_reponse){

        $ids_pieces_jointes = array();
        $query = "SELECT PJ.id_piece_jointe 
        FROM PIECES_JOINTES PJ, CONTIENT_REPONSE CR
        WHERE PJ.id_piece_jointe = CR.id_piece_jointe
        AND CR.id_reponse = ?;";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_reponse);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id_piece_jointe);

            while (mysqli_stmt_fetch($stmt)) {
                $ids_pieces_jointes[] = $id_piece_jointe;
            }
        } else die(mysqli_error($connexion));

        return $ids_pieces_jointes;
    }

    /**
     * Afficher une liste des identifiants des pièces jointes liées à un ticket
     * @return array
     */
    function AfficherIdsPiecesJointesTicket($connexion, $id_ticket){

        $ids_pieces_jointes = array();
        $query = "SELECT PJ.id_piece_jointe 
        FROM PIECES_JOINTES PJ, CONTIENT_TICKET CT
        WHERE PJ.id_piece_jointe = CT.id_piece_jointe
        AND CT.id_ticket = ?;";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id_piece_jointe);

            while (mysqli_stmt_fetch($stmt)) {
                $ids_pieces_jointes[] = $id_piece_jointe;
            }
        } else die(mysqli_error($connexion));

        return $ids_pieces_jointes;
    }

    /**
     * Afficher une liste des identifiants des pièces jointes liées à un ticket
     * @return array
     */
    function AfficherIdsReponsesTicket($connexion, $id_ticket){

        $ids_reponses = array();
        $query = "SELECT id_reponse FROM REPONSES WHERE id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            mysqli_stmt_bind_result($stmt, $id_reponse);

            while (mysqli_stmt_fetch($stmt)) {
                $ids_reponses[] = $id_reponse;
            }
        } else die(mysqli_error($connexion));

        return $ids_reponses;
    }

    /**
     * Afficher le nombre de tickets envoyés
     * @return integer
     */
    function AfficherNombreTicketsEnvoyes($connexion, $id_compte){

        $query ="SELECT COUNT(*) AS nombre_tickets FROM TICKETS
                WHERE corbeille_ticket = 0 
                AND id_compte = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);
            return array_shift($result)['nombre_tickets'];
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Afficher le nombre de tickets envoyés
     * @return integer
     */
    function AfficherNombreTicketsRecus($connexion, $id_compte){

        $query ="SELECT COUNT(*) AS nombre_tickets
                FROM TICKETS T, RECEVOIR_TICKET RT
                WHERE T.id_ticket = RT.id_ticket
                AND RT.corbeille_ticket = 0
                AND RT.id_compte = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);
            return array_shift($result)['nombre_tickets'];
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Afficher les pièces jointes d'une réponse
     * @return array
     */
    function AfficherPiecesJointesReponse($connexion, $id_reponse){

        $query = "SELECT PJ.*
        FROM PIECES_JOINTES PJ, CONTIENT_REPONSE CR
        WHERE PJ.id_piece_jointe = CR.id_piece_jointe
        AND CR.id_reponse = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_reponse);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $pieces_jointes = get_result($stmt);
            
            return $pieces_jointes;

        } else die(mysqli_error($connexion));

    }

    /**
     * Afficher les réponse liées à un ticket
     * @return array
     */
    function AfficherReponse($connexion, $id_ticket){

        $query = "SELECT * FROM REPONSES WHERE id_ticket = ? ORDER BY date_creation_reponse DESC";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $reponses = get_result($stmt);

            return $reponses;

        } else die(mysqli_error($connexion));

    }

    /**
     * Afficher les pièces jointes d'un ticket
     * @return array
     */
    function AfficherPiecesJointesTickets($connexion, $id_ticket){

        $query = "SELECT PJ.*
        FROM PIECES_JOINTES PJ, CONTIENT_TICKET CT
        WHERE PJ.id_piece_jointe = CT.id_piece_jointe
        AND CT.id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $pieces_jointes = get_result($stmt);
            
            return $pieces_jointes;

        } else die(mysqli_error($connexion));

    }

    /**
     * Afficher pour un ticket son id et le nombre de nouvelles reponses
     * @return array
     */
    function AfficherTicketsEnvoyesAvecReponses($connexion, $id_compte){

        $query = "SELECT T.id_ticket, COUNT(R.id_reponse) as 'reponse_ticket'
                FROM TICKETS T, REPONSES R
                WHERE T.id_ticket = R.id_ticket
                AND T.id_compte = ?
                AND T.id_compte <> R.id_compte
                AND R.vu_reponse = 0 
                GROUP BY T.id_ticket";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, "i", $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));

            return get_result($stmt);
        }
        else die(mysqli_error($connexion)); 
    }

    /**
     * Afficher l'ensemble des tickets envoyés
     * @return array
     */
    function AfficherTicketsEnvoyes($connexion, $id_compte, $premier, $par_page){

        $tickets = array();
        $query = 
                "SELECT * FROM TICKETS
                WHERE corbeille_ticket = 0 
                AND id_compte = ?
                ORDER BY date_creation_ticket DESC
                LIMIT ?, ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'iii', $id_compte, $premier, $par_page);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $tickets = get_result($stmt);
           
        } else die(mysqli_error($connexion));

        return $tickets;
    }

    /**
     * Afficher l'ensemble des tickets reçus
     * @return array
     */
    function AfficherTicketsRecus($connexion, $id_compte, $premier, $par_page){

        $tickets = array();
        $query = "SELECT T.*, RT.vu_ticket
                FROM TICKETS T, RECEVOIR_TICKET RT
                WHERE T.id_ticket = RT.id_ticket
                AND RT.corbeille_ticket = 0
                AND RT.id_compte = ?
                ORDER BY T.date_creation_ticket DESC
                LIMIT ?, ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'iii', $id_compte,  $premier, $par_page);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $tickets = get_result($stmt);

        } else die(mysqli_error($connexion));

        return $tickets;
    }

    /**
     * Afficher l'ensemble des tickets non vu par un destinataire
     * @return array
     */
    function AfficherTicketsNonVue($connexion, $id_compte){

        $tickets = array();
        $query = "SELECT T.id_ticket, T.date_creation_ticket, C.id_compte, C.privilege_compte
        FROM TICKETS T, RECEVOIR_TICKET RT, COMPTES C
        WHERE T.id_ticket = RT.id_ticket
        AND C.id_compte = T.id_compte
        AND RT.vu_ticket = 0
        AND RT.id_compte = ?
        ORDER BY T.date_creation_ticket DESC";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_compte);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $tickets = get_result($stmt);
            
        } else die(mysqli_error($connexion));

        return $tickets;
    }
    
    /**
     * Afficher une catégorie de la base de donnée
     * @return array
     */
    function AfficherUneCategorie($connexion, $id_categorie){

        $query = "SELECT * FROM CATEGORIES WHERE id_categorie = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_categorie);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);

            return array_shift($result);
            
            
        } else die(mysqli_error($connexion));

    }

    /**
     * Afficher les informations d'un ticket
     * @return array
     */
    function AfficherUnTicket($connexion, $id_ticket){

        $query = "SELECT *FROM TICKETS WHERE id_ticket = ? ";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);
            $ticket = array_shift($result);
            
            return $ticket;
        } else die(mysqli_error($connexion));
    }

    /**
     * Affiche la liste des comptes lié à un ticket
     * @return integer
     */
    function ComptesLierTicket($connexion, $id_ticket){

        $comptes = array();
        $query = "SELECT T.id_compte 
                FROM TICKETS T
                WHERE T.id_ticket = ?
                UNION
                SELECT RT.id_compte
                FROM RECEVOIR_TICKET RT
                WHERE RT.id_ticket = ? ";
        
        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ii', $id_ticket, $id_ticket);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id_compte);

            while (mysqli_stmt_fetch($stmt)) {
                $comptes[] = $id_compte;
            }
        } else die(mysqli_error($connexion));

        return $comptes;

    }
    
    /**
     * Permet à un expéditeur de cloturer son ticket
     * @return boolean
     */
    function CloturerTicket($connexion, $id_ticket){

        // On vérifie si le ticket est déja cloturé
        $verification = "SELECT date_cloture_ticket FROM TICKETS WHERE id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $verification))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);
            $date_cloture_ticket = array_shift($result)['date_cloture_ticket'];
            if ($date_cloture_ticket == null)
            {
                $query = "UPDATE TICKETS SET date_cloture_ticket = CURRENT_TIMESTAMP WHERE id_ticket = ?";

                if ($stmt = mysqli_prepare($connexion, $query))
                {
                    mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
                    return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));

                } else die(mysqli_error($connexion));

            } else return true;

        } else die(mysqli_error($connexion));
        
    }

    /**
     * Creer une ligne dans l'association CONTIENT_REPONSE
     * @return boolean
     */
    function ContientReponse($connexion, $id_reponse, $id_piece_jointe){

        $query = "INSERT INTO CONTIENT_REPONSE (id_reponse, id_piece_jointe) VALUES (?, ?)";
        
        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ii', $id_reponse, $id_piece_jointe);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
            
        } else die(mysqli_error($connexion));
    }

    /**
     * Creer une ligne dans l'association CONTIENT_TICKET
     * @return boolean
     */
    function ContientTicket($connexion, $id_ticket, $id_piece_jointe){

        $query = "INSERT INTO CONTIENT_TICKET (id_ticket, id_piece_jointe) VALUES (?, ?)";
        
        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ii', $id_ticket, $id_piece_jointe);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
            
        } else die(mysqli_error($connexion));
    }

    /**
     * Créer une pièce jointe dans la base de données
     * Retourne l'identifiant de la pièce jointe
     * @return integer
     */
    function CreerPieceJointeBD($connexion, $libelle_piece_jointe, $taille_piece_jointe, $format_piece_jointe){

        $query = "INSERT INTO PIECES_JOINTES (libelle_piece_jointe, taille_piece_jointe, format_piece_jointe) VALUES (?, ?, ?)";
        
        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'sss', $libelle_piece_jointe, $taille_piece_jointe, $format_piece_jointe);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            mysqli_stmt_close($stmt);
            return mysqli_insert_id($connexion);

        } else die(mysqli_error($connexion));

    }

    /**
     * Creer une réponse dans la base de données
     * Retourne l'identifiant de la réponse
     * @return integer
     */
    function CreerReponse($connexion, $contenu_reponse, $id_compte, $id_ticket)
    {
        $query = "INSERT INTO REPONSES (contenu_reponse, id_compte, id_ticket) VALUE (?, ?, ?)";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'sii', $contenu_reponse, $id_compte, $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            return mysqli_insert_id($connexion);

        } else die(mysqli_error($connexion));
    }

    /**
     * Créer une ticket dans la base de données
     * Retourne l'identifiant du ticket
     * @return integer
     */
    function CreerTicket($connexion, $sujet_ticket, $contenu_ticket, $id_compte, $id_categorie){

        $query = "INSERT INTO TICKETS (sujet_ticket, contenu_ticket, id_compte, id_categorie) VALUE (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ssii', $sujet_ticket, $contenu_ticket, $id_compte, $id_categorie);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            return mysqli_insert_id($connexion);

        } else die(mysqli_error($connexion));

    }

    /**
     * Vérifier si un compte est le destinataire d'un ticket
     * @return integer
     */
    function EstDestinataire($connexion, $id_compte, $id_ticket){
        
        $query = "SELECT * FROM RECEVOIR_TICKET WHERE corbeille_ticket = 0 AND id_compte = ? AND id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, "ii",  $id_compte, $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            mysqli_stmt_store_result($stmt);

            return mysqli_stmt_num_rows($stmt);

        } else die (mysqli_error($connexion));
    }

    /**
     * Vérifier si un compte est l'expéditeur d'un ticket
     * @return integer
     */
    function EstExpediteur($connexion, $id_compte, $id_ticket){

        $query = "SELECT * FROM TICKETS WHERE corbeille_ticket = 0 AND id_compte = ? AND id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, "ii",  $id_compte, $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            mysqli_stmt_store_result($stmt);

            return mysqli_stmt_num_rows($stmt);

        } else die (mysqli_error($connexion));
    }
    
    /**
     * Vérifie l'existance d'un ticket dans la base de donnée
     * @return integer
     */
    function ExisteTicket($connexion, $id_ticket){

        $id_ticket = htmlspecialchars(strip_tags($id_ticket));
        $query = "SELECT * FROM TICKETS WHERE id_ticket = ? ";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, "s", $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            mysqli_stmt_store_result($stmt);

            return mysqli_stmt_num_rows($stmt);
        } 
        else die (mysqli_error($connexion));
    }

    /**
     * Modifier la vue d'un expéditeur sur les réponse à son ticket
     * @return boolean
     */
    function ModifierVueReponse($connexion, $id_ticket){

        $query = "UPDATE REPONSES
        SET vu_reponse = 1
        WHERE id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }
    
    /**
     * Modifier un ticket dans la base de donnée
     * @return boolean
     */
    function ModifierTicket($connexion, $id_ticket, $sujet_ticket, $contenu_ticket, $id_categorie){

        $query = "UPDATE TICKETS
                SET sujet_ticket = ?,
                contenu_ticket = ?,
                id_categorie = ?
                WHERE id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ssii', $sujet_ticket, $contenu_ticket, $id_categorie, $id_ticket);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Modifier la vue d'un destinataire sur un ticket
     * @return boolean
     */
    function ModifierVueTicket($connexion, $id_compte, $id_ticket){

        $query = "UPDATE RECEVOIR_TICKET
        SET vu_ticket = 1
        WHERE id_compte = ?
        AND id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ii', $id_compte, $id_ticket);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Afficher le nombre de personne ayant vu un ticket
     * @return integer
     */
    function NombreVueTicket($connexion, $id_ticket){

        $query = "SELECT SUM(vu_ticket) AS nombre_vue 
                FROM RECEVOIR_TICKET
                WHERE id_ticket = ?";
        
        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);
            return array_shift($result)['nombre_vue'];
        } 
        else die(mysqli_error($connexion));
    } 

    /**
     * Envoyer un ticket à un compte
     * @return boolean
     */
    function RecevoirTicket($connexion, $id_ticket, $id_compte){

        $query = "INSERT INTO RECEVOIR_TICKET (id_ticket, id_compte) VALUE (?, ?)";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ii', $id_ticket, $id_compte);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Envoyer un ticket aux étudiant d'une promotion
     * @return boolean
     */
    function RecevoirTicketPromotion($connexion, $id_ticket, $id_promotion){

        $ids_comptes = array();
        $query = "SELECT id_compte FROM ETUDIANTS WHERE id_promotion = ?";
        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_promotion);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            mysqli_stmt_bind_result($stmt, $id_compte);
            while (mysqli_stmt_fetch($stmt))
            {
                $ids_comptes[] = $id_compte;
            }
            
            if (!empty($ids_comptes))
            {
                for ($i = 0; $i < sizeof($ids_comptes); $i++)
                {   
                    RecevoirTicket($connexion, $id_ticket, $ids_comptes[$i]);
                }
            }
            
        } else die(mysqli_error($connexion));
    }

    /**
     * Supprimer le ticket chez le destinataire 
     * corbeille_ticket = 1
     * @return boolean
     */
    function SupprimerTicketDestinataire($connexion, $id_compte, $id_ticket){

        $query = "UPDATE RECEVOIR_TICKET SET corbeille_ticket = 1 WHERE id_compte = ? AND id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ii',  $id_compte, $id_ticket);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Supprimer le ticket chez l'expéditeur
     * corbeille_ticket = 1
     * @return boolean
     */
    function SupprimerTicketExpediteur($connexion, $id_compte, $id_ticket){

        $query = "UPDATE TICKETS SET corbeille_ticket = 1 WHERE id_compte = ? AND id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'ii', $id_compte, $id_ticket);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Vérifie qu’un ticket peut être définitivement supprimé dans la base de données
     * (Si le ticket est dans la corbeille de l’expéditeur et du/des destinataire(s) associé(s) au ticket). 
     * @return boolean
     */
    function VerifSuppressionTicket($connexion, $id_ticket){

        $nombre_comptes = null;
        $nombre_suppressions = null;
        $query = "SELECT 
                    COUNT(DISTINCT T.id_compte) + COUNT(RT.id_compte) AS nombre_comptes,
                    SUM(DISTINCT T.corbeille_ticket) + SUM(RT.corbeille_ticket) AS nombre_suppressions
                FROM TICKETS T, RECEVOIR_TICKET RT
                WHERE T.id_ticket = RT.id_ticket
                AND T.id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
            $result = get_result($stmt);
            $nombre_comptes = $result[0]['nombre_comptes'];
            $nombre_suppressions = $result[0]['nombre_suppressions'];
            
            return $nombre_comptes == $nombre_suppressions ? true : false;
        } 
        else die(mysqli_error($connexion));
    }

    /**
     * Supprimer une ligne dans l'association CONTIENT_REPONSE
     * @return boolean
     */
    function SupprimerContientReponse($connexion, $id_reponse){

        $query = "DELETE FROM CONTIENT_REPONSE WHERE id_reponse = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_reponse);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
           
        } else die(mysqli_error($connexion));
    }

    /**
     * Supprimer une ligne dans l'association CONTIENT_TICKET
     * @return boolean
     */
    function SupprimerContientTicket($connexion, $id_ticket){

        $query = "DELETE FROM CONTIENT_TICKET WHERE id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
           
        } else die(mysqli_error($connexion));
    }

    /**
     * Supprimer une pièce jointe dans la base de donnée
     * @return boolean
     */
    function SupprimerPiecesJointes($connexion, $id_piece_jointe){

        $query = "DELETE FROM PIECES_JOINTES WHERE id_piece_jointe = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_piece_jointe);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));

        } else die(mysqli_error($connexion));
    }
    
    /**
     * Supprimer une ligne dans l'assciation RECEVOIR_TICKET
     * @return boolean
     */
    function SupprimerRecevoirTicket($connexion, $id_ticket){

        $query = "DELETE FROM RECEVOIR_TICKET WHERE id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
           
        } else die(mysqli_error($connexion));
    }

    /**
     * Supprimer définitivement un ticket dans la base de données et sur le serveur
     * @return boolean
     */
    function SupprimerTicket($connexion, $id_ticket){

        $ids_pieces_jointes = AfficherIdsPiecesJointesTicket($connexion, $id_ticket);
        $ids_reponses = AfficherIdsReponsesTicket($connexion, $id_ticket);

        SupprimerRecevoirTicket($connexion, $id_ticket);
        SupprimerContientTicket($connexion, $id_ticket);

        if (!empty($ids_reponses))
        {
            for ($i = 0; $i < sizeof($ids_reponses); $i++)
            {
                SupprimerReponse($connexion, $ids_reponses[$i]);
            }
        }
        
        if (!empty($ids_pieces_jointes))
        {
            
            for ($i = 0; $i < sizeof($ids_pieces_jointes); $i++)
            {
                SupprimerPiecesJointes($connexion, $ids_pieces_jointes[$i]);
                SupprimerDossier($ids_pieces_jointes[$i], 'App/Assets/PiecesJointes');
            }
        }

        $query = "DELETE FROM TICKETS WHERE id_ticket = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_ticket);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
           
        } else die(mysqli_error($connexion));

    } 

    /**
     * Supprimer définitivement une réponse dans la base de données et sur le serveur
     * @return boolean
     */
    function SupprimerReponse($connexion, $id_reponse){

        $ids_pieces_jointes = AfficherIdsPiecesJointesReponse($connexion, $id_reponse);
        SupprimerContientReponse($connexion, $id_reponse);

        if (!empty($ids_pieces_jointes))
        {
            
            for ($i = 0; $i < sizeof($ids_pieces_jointes); $i++)
            {
                SupprimerPiecesJointes($connexion, $ids_pieces_jointes[$i]);
                SupprimerDossier($ids_pieces_jointes[$i], 'App/Assets/PiecesJointes');
            }
        }

        $query = "DELETE FROM REPONSES WHERE id_reponse = ?";

        if ($stmt = mysqli_prepare($connexion, $query))
        {
            mysqli_stmt_bind_param($stmt, 'i', $id_reponse);
            return mysqli_stmt_execute($stmt) ? true : die(mysqli_error($connexion));
           
        } else die(mysqli_error($connexion));
    }