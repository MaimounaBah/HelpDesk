<?php


    include '../Database/Connect.php';
    include '../Functions/Tools.php';
    include '../Functions/Comptes.php';
    include '../Functions/Tickets.php';

    init_session();

    $erreurs = null;
    $success = false;
    $code_recuperation = '';
    $redirect = '';

    $connexion = getConnection();

    $email_recuperation = $_POST['email'];

    if (empty($email_recuperation))
    {
        $erreurs = "Veuillez renseigner ce champs";
    } elseif (ExisteEmail($connexion, $email_recuperation) != 1){
        $erreurs = "L'email n'existe pas";
    } else {

        for ($i = 0; $i < 8; $i++)
        {
            $code_recuperation .= mt_rand(0, 9);
        }
        if(ExisteRecuperation($connexion, $email_recuperation) == 1)
        {
            ModifierRecuperation($connexion, $code_recuperation, $email_recuperation) ? $success = true : null;
        } else {
            CreerRecuperation($connexion, $code_recuperation, $email_recuperation) ? $success = true : null;
        }

        if($success){
            
            $_SESSION['email_recuperation'] = $email_recuperation;
        }
    }


    $res = [
        "success" => $success, 
        "erreurs" => $erreurs,
        "email_recuperation" => $email_recuperation, 
        "code_recuperation" => $code_recuperation
    ];

    echo json_encode($res);


?>