<?php

    is_logged() ? die(header('Location: '.ROOT.'/Dashboard')) : null;

    // On récupère la connexion
    $connexion = getConnection();

    // On récupère l'email dans la session
    $email_recuperation = isset($_SESSION['email_recuperation']) ? $_SESSION['email_recuperation'] : null;

    // On récupère la section
    $section = isset($_GET['section']) ? $_GET['section'] : '';

    // On initialise un tableau d'erreur à null
    $erreurs = array();

    // SECTION CODE
    if ($section == 'code')
    {
        $email_recuperation == null ? die(header('Location: '.ROOT.'/MotDePasseOublie')) : null;

        if(isset($_POST['code_recuperation']))
        {
            $code_recuperation = $_POST['code_recuperation'];

            if (empty($code_recuperation))
            {
                $erreurs['code_recuperation'] = "Le champs est vide";
            } 
            elseif (VerifierRecuperation($connexion, $code_recuperation, $email_recuperation) == 0)
            {
                $erreurs['code_recuperation'] = "Le champs est incorrect";
            } 
            elseif (ConfirmerRecuperation($connexion, $email_recuperation)) 
            {
                die(header('Location: '.ROOT.'/MotDePasseOublie?section=changermdp'));
            }
        }
    // SECTION CHANGER MDP
    } elseif ($section == 'changermdp'){

        $email_recuperation == null ? die(header('Location: '.ROOT.'/MotDePasseOublie')) : null;

        if (isset($_POST['submit_changermdp']))
        {
            $mdp = isset($_POST['mdp']) ? $_POST['mdp'] : '';
            $mdp_confirm = isset($_POST['mdp_confirm']) ? $_POST['mdp_confirm'] : '';

            if (VerifierConfirmationRecuperation($connexion, $email_recuperation) == 0)
            {
                $erreurs['mdp'] = 'Veillez confirmer le mot de passe recu par mail';
            } 
            elseif(empty($mdp) || empty($mdp_confirm))
            {
                $erreurs['mdp'] = 'Les 2 champs doivent être renseignés';
            } 
            elseif ($mdp != $mdp_confirm)
            {
                $erreurs['mdp'] = 'Les 2 mot de passe sont différents';
            } 
            else 
            {
                $mdp = sha1($mdp);
                if (ModifierMdp($connexion, $email_recuperation, $mdp) && SupprimerRecuperation($connexion, $email_recuperation))
                {
                    unset($_SESSION['email_recuperation']);
                    die(header('Location: '.ROOT.'/Connexion'));
                }
            }
        }
        
    }

?>

<div class="signup__container">

    <div class="container__child signup__thumbnail">
        <div class="thumbnail__logo">
            <img class="logo__shape" width="25px" viewBox="0 0 634 479"src="<?= ROOT.'/Public/Images/ut1logo.svg';?>">
            <h1 class="logo__text">Help Desk</h1>
        </div>
        <div class="thumbnail__content text-center">
            <h1 class="heading--primary">Bienvenu sur Help Desk!</h1><br>
            <h2 class="heading--secondary">Trouvez la réponse à toutes vos questions en un rien de temps!</h2>
            <br>
        </div>
        <div class="thumbnail__links">
            <ul class="list-inline m-b-0 text-center">
            <li><a href="https://www.facebook.com/ut1capitole/" target="_blank"><i class="fa fa-facebook"></i></a></li>
            <li><a href="https://www.instagram.com/ut1capitole/" target="_blank"><fa class="fa fa-instagram"></fa></a></li>
            <li><a href="https://www.linkedin.com/school/universite-toulouse-1-capitole/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
            <li><a href="https://twitter.com/UT1Capitole" target="_blank"><i class="fa fa-twitter"></i></a></li>
            <li><a href="https://www.ut-capitole.fr/">UNIVERSITÉ TOULOUSE I CAPITOLE</a></li>
            </ul>
        </div>
        <div class="signup__overlay"></div>
    </div>

        <!-- SECTION (CODE DE RECUPERATION) -->
        <?php if ($section == 'code'): ?>

            <div class="container__child signup__form">
                <form id="form-code" action="" method="POST">
                    <p class="info-code">Un code de vérification à 8 chiffres vous à été envoyé par mail</p>
                    <div class="form-group">
                        <label for="code_verification">Code de verification</label>
                        <input class="form-control" type="text" name="code_recuperation" placeholder="Entrez votre code de vérification"/>
                        <span class="spanerror"><?= isset($erreurs['code_recuperation']) ? $erreurs['code_recuperation'] : '' ?></span>
                    </div>
                    <div class="m-t-lg">
                        <ul class="list-inline">
                            <li>
                                <input class="btn btn--form" type="submit" value="verifier" />
                            </li>
                        </ul>
                    </div>
                </form>  
            </div>
  
        <!-- SECTION (CHANGER MOT DE PASSE) -->
        <?php elseif ($section == 'changermdp'): ?>

            <div class="container__child signup__form">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="mdp">Nouveau Mot de Passe</label>
                        <input class="form-control" type="password" name="mdp"/>
                        <span id='missPassword'></span>
                    </div>
                    <div class="form-group">
                        <label for="mdpConfirm">Confirmation du Mot de Passe</label>
                        <input class="form-control" type="password" name="mdp_confirm"/>
                        <span id='missPasswordConfirm'></span>
                    </div>
                    <div class="form-group">
                        <span class="spanerror cgv"><?= isset($erreurs['mdp']) ? $erreurs['mdp'] : '' ?></span>
                    </div>
                    <div class="m-t-lg">
                        <ul class="list-inline">
                            <li>
                                <input class="btn btn--form" name="submit_changermdp" type="submit" value="Changer" />
                            </li>
                        </ul>
                    </div>
                </form>  
            </div>

        <!-- EMAIL DE RECUPERATION -->
        <?php else: ?>

            <div class="container__child signup__form">
                <form id="form-recup" action="" method="POST">
                    <div class="form-group">
                    <label for="email">Email</label>
                        <input class="form-control" type="text" name="email" placeholder="Entrez votre adresse email"/>
                        <span id="email" class="spanerror"></span>
                    </div>
                    <div class="">
                        <ul class="list-inline">
                            <li>
                                <input class="btn btn--form" type="submit" value="Recupérer" />
                            </li>
                        </ul>
                    </div>
                </form>  
            </div>

        <?php  endif; ?>



</div>

<script src="https://smtpjs.com/v3/smtp.js"></script>
<script src="<?= ROOT.'/Public/Js/recuperation_mdp.js';?>"></script>
