<?php
    !is_logged() ? die(header('Location: '.ROOT.'/Connexion')) : null;


    $nom = $nom_utilisateur;
    $prenom = $prenom_utilisateur;
    $email_compte = $email_compte_session;

    // MODIFIER AVATAR
    if (isset($_GET['avatar']))
    {
        if($_GET['avatar'] == 'delete' && $id_photo_session != null)
        {
            $id_photo_session_tmp = $id_photo_session;
            RetirerPhotoCompte($connexion, $id_compte_session);
            SupprimerPhoto($connexion, $id_photo_session_tmp);
            SupprimerDossier($id_photo_session_tmp, 'App/Assets/ImagesProfiles') ? die(header('Location: '.ROOT.'/Dashboard/EditerProfil')) : null;
        } else {
            die(header('Location: '.ROOT.'/Dashboard/EditerProfil'));
        }
    }

    // MODIFIER INFORMATIONS
    if (isset($_POST['submit_modifications'])){

        $erreurs = array();

        $email_compte = isset($_POST['email']) ? htmlspecialchars(trim(strip_tags($_POST['email']))) : '';
        $nom = isset($_POST['nom']) ? htmlspecialchars(trim(strip_tags($_POST['nom']))) : '';
        $prenom = isset($_POST['prenom']) ? htmlspecialchars(trim(strip_tags($_POST['prenom']))) : '';
        $sexe = isset($_POST['sexe']) ? htmlspecialchars(trim(strip_tags($_POST['sexe']))) : '';
        $avatar = (isset($_FILES['avatar']) AND !empty($_FILES['avatar']['name'])) ? $_FILES['avatar'] : [];
        $id_promotion = isset($_POST['id_promotion']) ? $_POST['id_promotion'] : '';


         //Verification E-mail
        if (empty($email_compte))
        {
            $erreurs['email'] = "Veuillez renseigner une adresse email";
        } elseif (!filter_var($email_compte, FILTER_VALIDATE_EMAIL)){
            $erreurs['email'] = "Veuillez renseigner une adresse email valide";
        } elseif (ExisteAutreEmail($connexion, $email_compte, $id_compte_session)){
            $erreurs['email'] = "L'adresse email est déja utilisée";
        }

                //Verfication mdp
        // empty($mdp_compte) ? $erreurs['mdp'] = 'Veiillez renseigner ce champs' : null; 

        //Verification nom
        empty($nom) ? $erreurs['nom'] = 'Veuillez renseigner ce champs' : null; 

        //Verification prenom
        empty($prenom) ? $erreurs['prenom'] = 'Veiillez renseigner ce champs' : null; 

        //Verificaiton sexe
        empty($sexe) ? $erreurs['sexe'] = 'Veuillez renseigner ce champs' : null; 

        // Verification photo
        if (!empty($avatar)){
            $format_photo = pathinfo($avatar['name'], PATHINFO_EXTENSION);
            $libelle_photo = pathinfo($avatar['name'], PATHINFO_FILENAME);
            $taille_photo = $avatar['size'];
            $erreur = $avatar['error'];
            $tmp_name = $avatar['tmp_name'];
            $tailleMax = 2097152;
            $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');
            // $chemin = '/App/Assets/ImageProfil/'.$id_compte.'/'.$libelle_photo.'.'.$format_photo;
            if($taille_photo >= $tailleMax) { 
                 $erreurs['avatar'] = 'Votre photo de profil ne doit pas dépasser 2Mo';
            }elseif(!in_array($format_photo, $extensionsValides)){
                $erreurs['avatar'] = 'Votre photo de profil doit être au format jpg, jpeg, gif ou png';
            }elseif($erreur != 0){
                $erreurs['avatar'] = 'Erreur durant l\'importation de votre photo de profil';
            }
            
        }

        if(empty($erreurs))
        {
            // Si l'utilisateur à renseigné une photo
            $id_photo = $id_photo_session;
            if(!empty($avatar)){
                // Si l'utilisateur avait déja une photo
                if ($id_photo != null){
                    ModifierPhoto($connexion, $id_photo, $libelle_photo, $taille_photo, $format_photo);
                    EnregistrerFichier($id_photo, $libelle_photo, $format_photo, $tmp_name, 'App/Assets/ImagesProfiles');
                // Sinon
                } else {
                    $id_photo = CreerPhoto($connexion, $libelle_photo, $taille_photo, $format_photo);
                    EnregistrerFichier($id_photo, $libelle_photo, $format_photo, $tmp_name, 'App/Assets/ImagesProfiles');
                }
            }
            
            ModifierCompte($connexion, $id_compte_session, $email_compte, $id_photo);

            switch($privilege_compte_session) {
                case 'etudiant':
                      
                    ModifierUnEtudiant($connexion, $nom, $prenom, $sexe, $id_promotion, $id_compte_session);
                    die(header('Location: '.ROOT.'/Dashboard'));
                    break;
                case 'enseignant':

                    ModifierUnEnseignant($connexion, $nom, $prenom, $sexe, $id_compte_session);
                    die(header('Location: '.ROOT.'/Dashboard'));
                    break;
                case 'agent_admin':

                    ModifierUnAgentAdmin($connexion, $nom, $prenom, $sexe, $id_compte_session);
                    die(header('Location: '.ROOT.'/Dashboard'));
                    break;
                case 'administrateur':

                    ModifierUnAdministrateur($connexion, $nom, $prenom, $sexe, $id_compte_session);
                    die(header('Location: '.ROOT.'/Dashboard'));
                    break;
            }
        }
    }

    // MODIFIER MOT DE PASSE
    if (isset($_POST['submit_password'])){

        $mdp_compte = isset($_POST['mdp_compte']) ? htmlspecialchars(trim(strip_tags($_POST['mdp_compte']))) : '';
        $mdp_compte_confirm = isset($_POST['mdp_compte_confirm']) ? htmlspecialchars(trim(strip_tags($_POST['mdp_compte_confirm']))) : '';
        
        //Verfication mdp
        empty($mdp_compte) ? $erreurs['mdp_compte'] = 'Veuillez renseigner ce champs' : null; 
        empty($mdp_compte_confirm) ? $erreurs['mdp_compte_confirm'] = 'Veuillez renseigner ce champs' : null;
        
        if (empty($erreurs)){
            if ($mdp_compte != $mdp_compte_confirm){
                $erreurs['mdp_final'] = 'Les 2 mot de passe sont différents';
            }
            elseif(strlen($mdp_compte) < 8 && !preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[A-Z]).{8,50}$/', $mdp_compte)){

                $erreurs['mdp_final'] = "Votre mot de passe, doit contenir au moins 8 caractères, une majuscule, un chiffre et une miniscule.";
            }
            else{
                $mdp_compte = sha1($mdp_compte);
                if (ModifierMdp($connexion, $email_compte_session, $mdp_compte)){
                    die(header('Location: '.ROOT.'/Dashbord'));
                }
            }
        }

    }

?>


<h1 class="title_profil">Editer votre profil</h1>


<div class="container_profil">
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="grid">
            <!-- AVATAR -->
            <div class="textarea-group">
                <!-- <label for="bio">Image</label><br> -->
                <img src="<?= $href_photo ?>" alt="" srcset="" width="45%" height="auto">   
                
                <?php if ($id_photo_session != NULL) : ?>
                    <a id="supprimer" href="<?= ROOT.'/Dashboard/EditerProfil?avatar=delete'?>">
                        <i class="material-icons dp48 grey left">delete</i>
                        <span>Supprimer</span>
                    </a>
                <?php endif ?>

                <input type="file" name="avatar" width="50%">
                <span class="spanerreur"><?= isset($erreurs['avatar']) ? $erreurs['avatar'] : '' ?></span>
            </div>

            <!-- PROMOTION -->
            <div class="element-group promotion-group">
                <?php if ($privilege_compte_session == 'etudiant') : ?>
                <label for="id_promotion">Promotion</label>
                <select name="id_promotion"> 
                    <?php $promo = AfficherPromotions($connexion);
                        foreach($promo  as $value): ?>
                            <option <?= $value['id_promotion'] == $id_promotion_session ? 'selected' : '' ?> value="<?= $value['id_promotion'] ?>"><?= $value['nom_promotion'] ?></option>
                    <?php endforeach ?>
                </select>
                <span class="spanerreur"><?= isset($erreurs['id_promotion']) ? $erreurs['id_promotion'] : '' ?></span>
                <?php endif ?>
            </div>

            <!-- STATUT -->
            <div class="element-group statut-group">
                <label for="Statut">Statut</label>
                <input id="statut" type="text" name="statut" value="<?= $statut_string ?>" placeholder="" disabled="disabled"/>
            </div>

            <!-- SEXE -->
            <div class="element-group sexe-group">
                <label for="zip">Sexe</label>
                <select name="sexe" value="<?=$sexe_utilisateur ?>"> 
                    <?php foreach(Sexe() as $key => $value): ?>
                        <option <?= $key == $sexe_utilisateur ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach ?>
                </select>
                <span class="spanerreur"><?= isset($erreurs['sexe']) ? $erreurs['sexe'] : '' ?></span>
            </div>

            <!-- NOM -->
            <div class="element-group nom-group">
                <label for="name">Nom</label>
                <input id="name" name="nom" type="text" onkeyup="checkNom(this.value);" placeholder="" value="<?=$nom_utilisateur ?>">
                <span class="spanerreur"><?= isset($erreurs['nom']) ? $erreurs['nom'] : '' ?></span>
            </div>

            <!-- PRENOM -->
            <div class="element-group prenom-group">
                <label for="first-name">Prénom</label>
                <input id="first-name" name="prenom" type="text" value="<?=$prenom_utilisateur ?>">
                <span class="spanerreur"><?= isset($erreurs['prenom']) ? $erreurs['prenom'] : '' ?></span>
            </div>

            <!-- EMAIL -->
            <div class="element-group email-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= $email_compte ?>"  width="">
                <span class="spanerreur"><?= isset($erreurs['email']) ? $erreurs['email'] : '' ?></span>
            </div>
        
            <div class="button-container_profil">
            <input class="button_profil" name="submit_modifications" type="submit" value="Modifier" />
            </div>
        </div>
    </form>	
</div>

<div class="container_password">
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="grid">
            
            <!-- MDP -->
            <div class="element-group mdp-group">
                <label for="mdp_compte">Mot de passe</label>
                <input id="mdp" name="mdp_compte" type="password" placeholder="********">
                <span class="spanerreur"><?= isset($erreurs['mdp_compte']) ? $erreurs['mdp_compte'] : '' ?></span>
            </div>

            <!-- MDP CONFIRMER -->
            <div class="element-group mdp-group-modifier">
                <label for="mdp_compte_confirm">Confirmer</label>
                <input id="mdp" name="mdp_compte_confirm" type="password" placeholder="********">
                <span class="spanerreur"><?= isset($erreurs['mdp_compte_confirm']) ? $erreurs['mdp_compte_confirm'] : '' ?></span>
            </div>
            
            <!-- <div class="spanerreurmdp"> -->
                <span class="spanerreurmdp"><?= isset($erreurs['mdp_final']) ? $erreurs['mdp_final'] : '' ?></span>
            <!-- </div> -->

            <div class="button-container_password">
            <input class="button_password" name="submit_password" type="submit" value="Modifier" />
            </div>
        </div>
    </form>	
</div>