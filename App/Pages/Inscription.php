<?php

    is_logged() ? die(header('Location: '.ROOT.'/Dashboard')) : null;

    $nom = null;
    $prenom = null;
    $email = null;
    $statut = null;
    $ine_etudiant = null;
    $id_enseignant = null;
    $mdp = null;
    $confirm_mdp = null;
    $cgv = null ;
  

    if(!empty($_POST))
    {   

        $connexion = getConnection();

        $erreurs = [];
        $nom = isset($_POST['nom']) ? htmlspecialchars(trim(strip_tags($_POST['nom']))) : '';
        $prenom = isset($_POST['prenom']) ? htmlspecialchars(trim(strip_tags($_POST['prenom']))) : '';
        $email = isset($_POST['email']) ? htmlspecialchars(trim(strip_tags($_POST['email']))) : '';
        $statut = isset($_POST['statut']) ? htmlspecialchars(trim(strip_tags($_POST['statut']))) : '';
        $ine_etudiant = isset($_POST['ine_etudiant']) ? htmlspecialchars(trim(strip_tags($_POST['ine_etudiant']))) : '';
        $id_enseignant = isset($_POST['id_enseignant']) ? htmlspecialchars(trim(strip_tags($_POST['id_enseignant']))) : '';
        $mdp = isset($_POST['mdp']) ? htmlspecialchars(trim(($_POST['mdp']))) : '';
        $confirm_mdp = isset($_POST['confirm_mdp']) ? htmlspecialchars(trim(($_POST['confirm_mdp']))) : '';
        $cgv = isset($_POST['cgv']) ? $_POST['cgv'] : '';
        

        // VERIFICATION NOM
        empty($nom) ? $erreurs['nom'] = "Veuillez renseigner un nom" : null;


        // VERIFICATION PRENOM
        empty($prenom) ? $erreurs['prenom'] = "Veuillez renseigner un prenom" : null;


        // VERIFICATION EMAIL
        if (empty($email))
        {
            $erreurs['email'] = "Veuillez renseigner une adresse email";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $erreurs['email'] = "Veuillez renseigner une adresse email valide";
        } elseif (ExisteEmail($connexion, $email)){
            $erreurs['email'] = "L'adresse email est déja utilisée";
        }
        
        // VERIFICATION EMAIL
        if ($statut === 'etudiant')
        {
            empty($ine_etudiant) ? $erreurs['ine_etudiant'] = "Veuillez renseigner votre numéro INE" : null;
        } elseif ($statut === 'enseignant'){
            empty($id_enseignant) ? $erreurs['id_enseignant'] = "Veuillez renseigner votre identifiant enseignant" : null;
        }
        
         // Verification MDP       
        if (empty($mdp) OR empty($confirm_mdp))
        {
            $erreurs['mdp'] = "Les deux champs doivent être renseigner";

        } elseif($mdp != $confirm_mdp)
        {
            $erreurs['mdp'] = "les deux mots de passe ne correspondent pas ";

            //Verification robustesse mot de passe
        } elseif(strlen($mdp) < 8 && !preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[A-Z]).{8,50}$/', $mdp) )
        {
            $erreurs['mdp'] = "Votre mot de passe, doit contenir au moins 8 caractères, une majuscule, un chiffre et une munuscule.";
        }

        // Vérification acceptation CGU
        if($cgv != 'on')
        {
            $erreurs['cgv'] = "Veuillez accepter les conditions génénales d'utilisation";
        }

        // Insertion données base de données si aucune erreurs
        if (empty($erreurs)){

            $mdp = sha1($mdp);  // cryptage du mot de passe 

            $id_compte = CreerCompte($connexion, $mdp, $email, $statut);

            if($statut == 'etudiant')
            {
                CreerEtudiant($connexion, $ine_etudiant, $nom, $prenom, $id_compte) ? die(header('Location: '.ROOT.'/Connexion')) : null;
                                                                                    
            }elseif($statut == 'enseignant')
            {
                CreerEnseignant($connexion, $id_enseignant, $nom, $prenom, $id_compte) ? die(header('Location: '.ROOT.'/Connexion')) : null;
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
  
        <form method="POST">

            <div class="form-section">
                <div class="form-group">
                    <label>Nom</label>
                    <input class="form-control" type="text" name="nom" value="<?= $nom ?>" placeholder=""  />
                    <span class="spanerror"><?= isset($erreurs['nom']) ? $erreurs['nom'] : '' ?></span>
                </div>

                <div class="form-group">
                    <label>Prénom</label>
                    <input class="form-control" type="text" name="prenom" value="<?= $prenom ?>" placeholder=""  />
                    <span class="spanerror"><?= isset($erreurs['prenom']) ? $erreurs['prenom'] : '' ?></span>
                </div>
            </div>
            
            <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="mail" name="email" value="<?= $email ?>" placeholder=""  />
                    <span class="spanerror"><?= isset($erreurs['email']) ? $erreurs['email'] : '' ?></span>
            </div>

            <div class="form-section">
                <div class="form-group">
                    <label>Statut</label>
                    <select class="form-control"  name="statut" id="privelege">
                        <!-- <option select hidden value="">statut</option>  -->
                        <option <?= $statut == 'etudiant' || $statut == null ? 'selected' : ''?>  value="etudiant">Etudiant</option> 
                        <option <?= $statut == 'enseignant' ? 'selected' : ''?> value="enseignant">Enseignant</option>
                    </select>
                    
                </div>
                <div class="form-group etd <?= $statut == 'etudiant' || $statut == null ? 'active' : ''?>">
                    <label>INE</label>
                    <input class="form-control" name= 'ine_etudiant' type="text" value="<?= $statut == 'etudiant' ? $ine_etudiant : '' ?>" placeholder=""  />
                    <span class="spanerror"><?= isset($erreurs['ine_etudiant']) ? $erreurs['ine_etudiant'] : '' ?></span>
                </div>

                <div class="form-group esn <?= $statut == 'enseignant' ? 'active' : ''?>">
                        <label>Numéro </label>
                        <input class="form-control"  name="id_enseignant" type="text" value="<?= $statut == 'enseignant' ? $id_enseignant : '' ?>" placeholder=""  />
                        <span class="spanerror"><?= isset($erreurs['id_enseignant']) ? $erreurs['id_enseignant'] : '' ?></span>
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input class="form-control" type="password" name="mdp" placeholder=""  />
                    
                </div>

                <div class="form-group">
                    <label>Confirmation</label>
                    <input class="form-control" type="password" name="confirm_mdp"  placeholder=""  />
                   
                </div> 
                <span class="spanerror mdp"><?= isset($erreurs['mdp']) ? $erreurs['mdp'] : '' ?></span> 
                
            </div>

            <div class="form-group cgv">
                    <input type="checkbox" name="cgv" class="from-check-input" >
                    <label>
                        <a class="link-cgu" href="<?= ROOT.'/Cgu' ?>" target="_blank" rel="noopener noreferrer">Je reconnais avoir lu et compris les CGU et je les accepte (*)</a>
                    </label>
            </div>

            <div class="form-group">
                    <span class="spanerror cgv"><?= isset($erreurs['cgv']) ? $erreurs['cgv'] : '' ?></span>
            </div>


            <div class="form-group">
                <ul class="list-inline sign_link">
                    <li>
                        <input class="btn btn--form" type="submit" value="S'inscrire" />
                    </li>
                    <li>
                        <a class="signup__link" href="<?= ROOT.'/Connexion'?>">Dejà inscrit ?</a>
                    </li>
                </ul>
            </div>
        </form>  
    </div>
</div>

<script src="<?= ROOT.'/Public/Js/sign.js';?>"></script>
