<?php

    /*  ---------  NOUVEAU TICKET (ETUDIANT)  ---------  */

    $connexion = getConnection();

    $id_categorie_ticket = null;
    $sujet_ticket = null;
    $contenu_ticket = null;
    $destinataires = null;
    $id_promotion_ticket = null;
    
    if(isset($_POST['nouveau_ticket']))
    {

        $erreurs = array();
        $success = false;
        
        $pieces_jointes = array();
        $comptes_ticket = array();
        $id_categorie_ticket = isset($_POST['id_categorie_ticket']) ? htmlspecialchars(trim(strip_tags($_POST['id_categorie_ticket']))) : '';
        $sujet_ticket = isset($_POST['sujet_ticket']) ? htmlspecialchars(trim(strip_tags($_POST['sujet_ticket']))) : '';
        $contenu_ticket = isset($_POST['contenu_ticket']) ? htmlspecialchars(trim(strip_tags($_POST['contenu_ticket']))) : '';
        $comptes_ticket_tmp = isset($_POST['comptes_ticket']) ? htmlspecialchars(trim(strip_tags($_POST['comptes_ticket']))) : '';
        $pieces_jointes_tmp = isset($_FILES['pieces_jointes']) ? ReArrayFiles($_FILES['pieces_jointes']) : '';
        
        $id_promotion_ticket = isset($_POST['id_promotion_ticket']) ? $_POST['id_promotion_ticket'] : '';

        // VERIFICATION CATEGORIE
        empty($id_categorie_ticket) ? $erreurs['id_categorie_ticket'] = "champs renseigner la catégorie" : null;

        // VERIFICATION SUJET
        if(empty($sujet_ticket)){
            $erreurs['sujet_ticket'] = "Veuillez renseigner le sujet du ticket";
        } elseif(strlen($sujet_ticket) > 50){
            $erreurs['sujet_ticket'] = "Ce champs doit comporter moins de 50 caractères";
        }

        // VERIFICATION CONTENU
        empty($contenu_ticket) ? $erreurs['contenu_ticket'] = "Veuillez renseigner ce champ" : null;

        // VERIFICATION COMPTES
        if (empty($comptes_ticket_tmp)){
            $erreurs['compte_ticket'] = "Veuillez selectionnez un compte";
        } elseif(sizeof(explode(',', $comptes_ticket_tmp)) > 3){
            $erreurs['compte_ticket'] = "Veuillez rentrer maximum 3 comptes";
        } else $comptes_ticket = explode(',', $comptes_ticket_tmp);
   
        // Si il existe des pièces jointes, on vérifie celle ci
        if (!EmptyPiecesJointes($pieces_jointes_tmp))
        {
            // FORMATS AUTORISES
            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'docx', 'zip', 'rar', 'sql', 'txt', 'xlsx', 'xlsm', 'html', 'php', 'js'];

            for ($i = 0; $i < sizeof($pieces_jointes_tmp); $i++){

                $libelle_piece_jointe = pathinfo($pieces_jointes_tmp[$i]['name'], PATHINFO_FILENAME);
                $format_piece_jointe = pathinfo($pieces_jointes_tmp[$i]['name'], PATHINFO_EXTENSION);
                $taille_piece_jointe = $pieces_jointes_tmp[$i]['size'];
                $error = $pieces_jointes_tmp[$i]['error'];
                $tmp_name = $pieces_jointes_tmp[$i]['tmp_name'];

                // VERIFICATION FORMAT
                if (!in_array($format_piece_jointe, $allowed))
                {
                    $erreurs['pieces_jointes'][$i] = "Le type de format du fichier <strong>\"$libelle_piece_jointe.$format_piece_jointe\"</strong> n'est pas autorisé";
                // VERIFICATION ERREUR
                } else if ($error !== 0){
                    $erreurs['pieces_jointes'][$i] = "Il y a eu une erreur lors du chargement du fichier <strong> \"$libelle_piece_jointe.$format_piece_jointe\" </strong>";
                // VERIFICATION TAILLE
                } elseif ($taille_piece_jointe > 1000000){ // 1 Mo
                    $erreurs['pieces_jointes'][$i] = "Le fichier <strong> \"$libelle_piece_jointe.$format_piece_jointe\" </strong>est trop volumineux";
                } else {
                    $pieces_jointes[$i]['libelle_piece_jointe'] = $libelle_piece_jointe;
                    $pieces_jointes[$i]['format_piece_jointe'] = $format_piece_jointe;
                    $pieces_jointes[$i]['taille_piece_jointe'] = $taille_piece_jointe;
                    $pieces_jointes[$i]['tmp_name'] = $tmp_name;
                }
            }
        }


        if(empty($erreurs))
        {

            $id_ticket = CreerTicket($connexion, $sujet_ticket, $contenu_ticket, $id_compte_session, $id_categorie_ticket);

            foreach($comptes_ticket as $value)
            {
                // On récupère l'identidiant du compte à partir de l'email
                $id_compte_destinataire = GetIdCompte($connexion, $value);
                RecevoirTicket($connexion, $id_ticket, $id_compte_destinataire);
            }

            if (!empty($pieces_jointes))
            {
                // Pour chaque pieces jointes
                foreach ($pieces_jointes as $value)
                {
                    $libelle_piece_jointe = $value['libelle_piece_jointe'];
                    $format_piece_jointe = $value['format_piece_jointe'];
                    $taille_piece_jointe = $value['taille_piece_jointe'];
                    $tmp_name = $value['tmp_name'];

                    // On creer la piece jointe dans la base de donnée
                    $id_piece_jointe = CreerPieceJointeBD($connexion, $libelle_piece_jointe, $taille_piece_jointe, $format_piece_jointe);
                    
                    // // On enregistre la piece jointe dans un répertoire sur le serveur
                    if(EnregistrerFichier($id_piece_jointe, $libelle_piece_jointe, $format_piece_jointe, $tmp_name, 'App/Assets/PiecesJointes'))
                    {
                        ContientTicket($connexion, $id_ticket, $id_piece_jointe);
                    }
                } 
                die(header('Location: '.ROOT.'/Dashboard/TicketsEnvoyes'));
            } else die(header('Location: '.ROOT.'/Dashboard/TicketsEnvoyes'));

        }

    }



?>

<?php if (isset($_GET['modale'])):
        if($_GET['modale'] == true):

            $categories = AfficherCategories($connexion);
            $comptes = AfficherComptes($connexion, $id_compte_session);
?>


        <form id="form-ticket" action="" class="sky-form" method="POST" enctype="multipart/form-data"/>
            <header>Nouveau Ticket</header>
            
            <fieldset>
                
                <section class="form-section">
                    <!-- categorie -->
                    <div>
                        <label class="label">Categorie</label>
                        <label class="select">
                            <select name="id_categorie_ticket" id="">
                                <option value=""></option>                                    
                                <?php foreach($categories as $categorie): 
                                        $id_categorie = $categorie['id_categorie'];
                                        $nom_categorie = $categorie['nom_categorie'];
                                ?>
                                        <option <?= $id_categorie_ticket == $id_categorie ? 'selected' : '' ?> value='<?= $id_categorie ?>'><?= $nom_categorie ?></option>
                                    
                                <?php endforeach ?>
                            </select>
                        </label>
                        <p id="id_categorie" class="erreur"><?= isset($erreurs['id_categorie_ticket']) ? $erreurs['id_categorie_ticket'] : '' ?></p>
                    </div>
                    <!-- sujet -->
                    <div>
                        <label class="label">Sujet</label>
                        <label class="input">
                            <input type="text" value="<?= $sujet_ticket ?>" name="sujet_ticket" id="">
                        </label>
                        <p id="sujet_ticket" class="erreur"><?= isset($erreurs['sujet_ticket']) ? $erreurs['sujet_ticket'] : '' ?></p>
                    </div>
                </section>

                <div>
                    <label class="label">Destinataire</label>

                    <!-- COMPTES -->
                    <div class="compte <?= $radio_destinataires == 'compte' || $radio_destinataires == null ? 'active' : ''?>">
                        <label class="input">
                            <input type="text" placeholder="Choissez un compte"

                            class="flexdatalist form-control"
                            data-min-length="1"
                            data-searchContain="true"
                            multiple="multiple"

                            list="comptes" name="comptes_ticket">
                            <datalist id="comptes">
                                <?php foreach ($comptes as $compte):
                                        $id_compte_destinataire = $compte['id_compte'];
                                        $email_compte = $compte['email_compte'];
                                ?>
                                        <option value='<?= $id_compte_destinataire ?>'><?= $email_compte ?></option>
                                    
                                <?php endforeach ?>
                            </datalist>
                        </label>
                        <p class="erreur"><?= isset($erreurs['compte_ticket']) ? $erreurs['compte_ticket'] : '' ?></p>

                    </div>
                    
                </div>

                
                <div>
                    <label class="label">Contenu</label>
                    <label class="textarea textarea-resizable">
                        <textarea value="" name="contenu_ticket"  rows="3"><?= $contenu_ticket ?></textarea>
                    </label>
                    <p id="contenu_ticket" class="erreur"><?= isset($erreurs['contenu_ticket']) ? $erreurs['contenu_ticket'] : '' ?></p>
                </div>
                
                <div>
                    <label class="label">Pièces jointes</label>
                    
                    <label for="file" class="input input-file">
                        <input type="file" name="pieces_jointes[]" multiple id="">
                    </label>
                    <div id="pieces_jointes">
                        <!-- <p id ="" class="erreur"></p> -->
                    </div>
                    
                    <?php
                        if (isset($erreurs['pieces_jointes']))
                        {
                            foreach ($erreurs['pieces_jointes'] as $value)
                            {
                                echo "<p class='erreur'>$value</p>";
                            }
                        }
                    ?>
                </div>					
                    
                
            </fieldset>

            <footer>
                <!-- <input class="button" type="s" value="Retour"> -->
                <button  type="submit" name="nouveau_ticket" class="submit button">Envoyer</button>
                <a href="<?= ROOT.'/Dashboard/TicketsEnvoyes' ?>" class="close-modale button button-secondary">Fermer</a>
            </footer>
        </form>

<?php  endif;
        endif;
?>




