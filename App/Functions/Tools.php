<?php


    /**
     * Exécute le script de création de la base de données
     * Utilisé en développement
     * @return void
     */

    function DropCreateTable($connexion)
    {

        //load file
        $commands = file_get_contents(ROOT.'/App/Database/Create.sql');

        //delete comments
        $lines = explode("\n", $commands);
        $commands = '';
        foreach($lines as $line){
            $line = trim($line);
            if( $line && !startsWith($line, '--') ){
                $commands .= $line . "\n";
            }
        }

        //convert to array
        $commands = explode(";", $commands);

        // pre_r($commands);

        //run commands
        $total = $success = 0;
        foreach($commands as $command){
            if(trim($command)){
    
                if ($stmt = mysqli_prepare($connexion, $command))
                {
                    mysqli_stmt_execute($stmt);
                } else die(mysqli_error($connexion));
            
            }
        }

    }

    /**
     * Démarrer la session, si il n'y a aucune session en cours
     * @return void
     */
    function init_session()
    {
        if(!session_id())
        {
            session_start();
            session_regenerate_id();
            return true;
        }
        
        return false;
    }

    /**
     * Vider les variable de sessions et la détruit
     * @return void
     */
    function clean_session()
    {
        session_unset();
        session_destroy();
    }

    /**
     * Vérifier si l'utlisateur est connecté
     * @return boolean
     */
    function is_logged()
    {
        if (isset($_SESSION['auth']))
        {
            return true;
        }

        return false;
    }

    function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    function pre_r($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }

    /**
     * Redéfinir la variable $_FILES (
     * Mettre tout les information d'une fichier dans une seul clé de tableau
     * Fonction utilisé pour les pièces jointes
     * @return array
     */
    function ReArrayFiles($files){

        $file_ary = array();
        $file_count = count($files['name']);
        $file_keys = array_keys($files);

        for ($i = 0; $i < $file_count; $i++){
            foreach ($file_keys as $key){
                $file_ary[$i][$key] = $files[$key][$i];
            }
        }

        return $file_ary;
    }

    /**
     * Vérifier si l'utilisateur à renseigné une pièce jointe 
     * Prend en paramètre une clé de la variable $_FILES 
     * @return boolean
     */
    function EmptyPiecesJointes($pieces_jointes){

        $empty = true;
        $i = 0;

        for ($i = 0; $i < sizeof($pieces_jointes); $i++){

            if ($pieces_jointes[$i]['size'] != 0){
                $empty = false;
            } 
        }

        return $empty;
    }