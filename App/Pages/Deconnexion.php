<?php

    if (!is_logged())
    {
        die(header('Location: '.ROOT.'/Dashboard'));
    } else {
        clean_session();
        die(header('Location: '.ROOT.'/Connexion'));
    }
    
?>