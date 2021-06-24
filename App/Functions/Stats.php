<?php

    /**
     * Afficher le nombre de tickets émis, pour chaque catégorie
     * @return array
     */
    function CategorieNbTicket($connexion){
        
        $query = "SELECT C.nom_categorie, COUNT(T.id_ticket) AS nombres_tickets
                    FROM CATEGORIES C, TICKETS T
                    WHERE C.id_categorie = T.id_categorie
                    GROUP BY C.id_categorie, C.nom_categorie";

        $result = array();

        if($stmt = mysqli_query($connexion,$query))
        {
            while ($ligne = mysqli_fetch_assoc($stmt))
            {
                $result[] = $ligne;
            }
        } else die(mysqli_error($connexion));

        return $result;
    }

    /**
     * Convertit une date en un format plus adapté pour l'utilisateur
     * @return string
     */
    function ConvertSecond($time){

        $result = '';

        $days = floor($time / (60 * 60 * 24));
        $time -= $days * (60 * 60 * 24);

        $hours = floor($time / (60 * 60));
        $time -= $hours * (60 * 60);

        $minutes = floor($time / 60);
        $time -= $minutes * 60;

        $seconds = floor($time);
        $time -= $seconds;

        $days == 1 ? $result .= "$days jour " : ($days > 1 ? $result .= "$days jours " : null);
        $hours == 1 ? $result .= "$hours heure " : ($hours > 1 ? $result .= "$hours heures " : null);
        $minutes == 1 ? $result .= "$minutes minute " : ($minutes > 1 ? $result .= "$minutes minutes et " : null);
        $seconds == 1 ? $result .= "$seconds seconde " : ($seconds > 1 ? $result .= "$seconds secondes " : null);

        return $result;

    }

    /**
     * Afficher l'évolution du nombre de tickets
     * @return array
     */
    function EvolutionNombreTickets($connexion){

        $query = "SELECT 
                    DATE_FORMAT(date_creation_ticket, '%b') AS mois, 
                    COUNT(*) AS nombre_ticket
                FROM TICKETS
                GROUP BY DATE_FORMAT(date_creation_ticket, '%b')
                ORDER BY date_creation_ticket";

        $result = array();

        if($stmt = mysqli_query($connexion,$query))
        {
            while ($ligne = mysqli_fetch_assoc($stmt))
            {
                $result[] = $ligne;
            }
        } else die(mysqli_error($connexion));

        return $result;
    }

    /**
     * Afficher le temps moyen par ticket
     * (le temps moyen qui s'écoule entre le date de création du ticket et sa cloture)
     * @return array
     */
    function TempsMoyenTickets($connexion){

        $query = "SELECT ROUND(AVG(TIMESTAMPDIFF(SECOND, date_creation_ticket, date_cloture_ticket))) AS temps_moyen 
        FROM TICKETS 
        WHERE date_cloture_ticket IS NOT NULL";

        if($stmt = mysqli_query($connexion,$query))
        {
            $ligne = mysqli_fetch_assoc($stmt);
            $second = $ligne['temps_moyen'];

            return ConvertSecond($second);
        } else die(mysqli_error($connexion));

    }

    /**
     * Afficher le nombre de tickets ayant été envoyés
     * @return array
     */
    function NombreTickets($connexion){

        $query = "SELECT COUNT(*) FROM TICKETS";

        $result = array();

        if($stmt = mysqli_query($connexion,$query))
        {
            $ligne = mysqli_fetch_row($stmt);
            $result = $ligne[0];
            
        } else die(mysqli_error($connexion));

        return $result;
    }

    /**
     * Afficher le nombre de tickets n'ayant pas encoré été cloturé
     * @return array
     */
    function NombreTicketsNonCLoture($connexion){

        $query = "SELECT COUNT(*) FROM TICKETS WHERE date_cloture_ticket IS NULL";

        $result = array();

        if($stmt = mysqli_query($connexion,$query))
        {
            $ligne = mysqli_fetch_row($stmt);
            $result = $ligne[0];
            
        } else die(mysqli_error($connexion));

        return $result;
    }

    /**
     * Activité au cours des 7 derniers jours (Nombre de tickets envoyés)
     * @return array
     */
    function NombreTicketsEnvoyesSemaine($connexion, $id_compte){

        $result = array();

        for ($i = 0; $i < 7; $i++)
        {
            $query = "SELECT COUNT(*) AS nombre_tickets
                FROM TICKETS
                WHERE TO_DAYS(date_creation_ticket) = TO_DAYS(NOW()) - $i
                AND id_compte = ?";
            
            if ($stmt = mysqli_prepare($connexion, $query))
            {
                mysqli_stmt_bind_param($stmt, 'i', $id_compte);
                mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
                $result_tmp = get_result($stmt);
                $result[] = array_shift($result_tmp);
            }
            else die(mysqli_error($connexion));
            
        }

        return $result;
    }

    /**
     * Activité au cours des 7 derniers jours (Nombre de tickets reçus)
     * @return array
     */
    function NombreTicketsRecusSemaine($connexion, $id_compte){

        $result = array();

        for ($i = 1; $i <= 7; $i++)
        {
            $query = "SELECT COUNT(T.id_ticket) AS nombre_tickets
                FROM TICKETS T, RECEVOIR_TICKET RT
                WHERE T.id_ticket = RT.id_ticket
                AND TO_DAYS(T.date_creation_ticket) = TO_DAYS(NOW()) - $i
                AND RT.id_compte = ?";
            
            if ($stmt = mysqli_prepare($connexion, $query))
            {
                mysqli_stmt_bind_param($stmt, 'i', $id_compte);
                mysqli_stmt_execute($stmt) or die(mysqli_error($connexion));
                $result_tmp = get_result($stmt);
                $result[] = array_shift($result_tmp);
            }
            else die(mysqli_error($connexion));
        }
        return $result;
    }

?>