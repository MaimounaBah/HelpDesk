<?php

// Connexion à la base de donnée

    function getConnection(){

        // local
        // $host = "localhost";
        // $db_name = "help_desk";
        // $username = "root";
        // $password = "";
        // $connexion = null;

        // etu-web2
        $host = "localhost";
        $db_name = "db_21909854_2";
        $username = "21909854";
        $password = "04079D";
        $connexion = null;

        // Create connection
        $connexion = mysqli_connect($host, $username, $password);

        mysqli_query($connexion, "SET NAMES UTF8");

        // Check connection
        if ($connexion == null) 
        {
            echo "Echec de connection";
        } 
        elseif (mysqli_select_db($connexion, $db_name) == true)
        {
            // echo "Connection réussie";
        } 
        else 
        {
            echo "Cette base n'existe pas";
        }

        return $connexion;
    }