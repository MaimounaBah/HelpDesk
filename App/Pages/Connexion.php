<?php
    is_logged() ? die(header('Location: '.ROOT.'/Dashboard')) : null;

    $email = null;

    if (isset($_POST['submit_connexion']))
    {
        $connexion = getConnection();
        $erreurs = array();

        $email = isset($_POST['email']) ? htmlspecialchars(strip_tags($_POST['email'])) : '';
        $mdp = isset($_POST['mdp']) ? htmlspecialchars(strip_tags($_POST['mdp'])) : '';

        empty($email) ? $erreurs['email'] = 'Veuillez renseigner ce champs' : null; 
        empty($mdp) ? $erreurs['mdp'] = 'Veuillez renseigner ce champs' : null; 

        if (empty($erreurs))
        {
            !ExisteEmail($connexion, $email) ? $erreurs['connexion'] = 'Identifiant Invalide': null; 
            
            if (empty($erreurs))
            {
                
                if (VerifierMdp($connexion, $email, $mdp))
                {
                    $id_compte = GetIdCompte($connexion, $email);
                    $_SESSION['auth']['id_compte'] = $id_compte;
                    die(header('Location: '.ROOT.'/Dashboard'));

                } else 
                {
                    $erreurs['connexion'] = 'Identifiant Invalide';
                }
            }
        }

    }
 

?>

<div class="signup__container">

    <div class="container__child signup__thumbnail">
        <div class="thumbnail__logo">
            <img class="logo__shape" width="25px" viewBox="0 0 634 479" src="<?= ROOT.'/Public/Images/ut1logo.svg';?>">
            <h1 class="logo__text">Help Desk</h1>
        </div>
        <div class="thumbnail__content text-center">
            <h1 class="heading--primary">Bienvenue sur Help Desk!</h1><br>
            <h2 class="heading--secondary">Trouvez la réponse à toutes vos questions en un rien de temps!</h2>
            <br>
        </div>
        <div class="thumbnail__links">
                <ul class="list-inline m-b-0 text-center">
                <li><a href="https://www.ut-capitole.fr/">UNIVERSITÉ TOULOUSE I CAPITOLE</a></li><br>
                <li><a href="https://www.facebook.com/ut1capitole/" target="_blank"><i class="fa fa-facebook"></i></a></li>
                <li><a href="https://www.instagram.com/ut1capitole/" target="_blank"><fa class="fa fa-instagram"></fa></a></li>
                <li><a href="https://www.linkedin.com/school/universite-toulouse-1-capitole/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                <li><a href="https://twitter.com/UT1Capitole" target="_blank"><i class="fa fa-twitter"></i></a></li>
                </ul>
        </div>
        <div class="signup__overlay"></div>
    </div>

    <div class="container__child signup__form">
        <form action="#" method="POST">
                <!-- <br>
                <br>
                <br> -->
            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-control" type="text" name="email" value="<?= $email ?>" placeholder=""/>
                <span class="spanerror mdpconnexion"><?= isset($erreurs['email']) ? $erreurs['email'] : '' ?></span>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input class="form-control"  type="password" name="mdp" placeholder=""/>
                <span class="spanerror mdpconnexion"><?= isset($erreurs['mdp']) ? $erreurs['mdp'] : '' ?></span>
                <a class="signup__link" href="<?= ROOT.'/MotDePasseOublie' ?>">Mot de passe oublié</a>
            </div>
            <div class="form-group">
                <span class="spanerror invalidmdp"><?= isset($erreurs['connexion']) ? $erreurs['connexion'] : '' ?></span>
                
            </div>
            <div class="">
                <ul class="list-inline sign_link">
                    <li>
                        <input class="btn btn--form" name="submit_connexion" type="submit" value="Connexion" />
                    </li>
                    <li>
                        <a class="signup__link" href="<?= ROOT.'/Inscription' ?>">Je n'ai pas de compte</a>
                    </li>
                    
                </ul>
            </div>
        </form>  
    </div>
</div>

<script src="<?= ROOT.'/Public/Js/connexion.js';?>"></script>