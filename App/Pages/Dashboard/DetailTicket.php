<?php

    // On vérifie si l'utilisateur est connecté
    if (!is_logged())
    {
        die(header('Location: '.ROOT.'/Connexion'));
    } 
    // On vérifie la variebles $_GET['id']
    elseif (!isset($_GET['id']))
    {
        die(header('Location: '.ROOT.'/Dashboard'));
    } 
    // On verifie l'existance du ticket dans la base de donnée
    elseif (!ExisteTicket($connexion, htmlspecialchars(strip_tags($_GET['id']))))
    {
        die(header('Location: '.ROOT.'/Dashboard'));
    }
    // On vérifie que l'utilisateur à le droit de voir le ticket 
    elseif (!EstExpediteur($connexion, $id_compte_session, $_GET['id']) && !EstDestinataire($connexion, $id_compte_session, $_GET['id']))
    {
        die(header('Location: '.ROOT.'/Dashboard'));
    }


    // On initialise la connexion
    $connexion = getConnection();
    
    $id_ticket = $_GET['id'];

    // Nombre de destinataire ayant vu le ticket
    $nombre_vues_ticket = NombreVueTicket($connexion, $id_ticket);

    // Si le compte connecté fait partie des destinataires
    if (EstDestinataire($connexion, $id_compte_session, $id_ticket))
    {
        // Une vue est rajouté au ticket
        ModifierVueTicket($connexion, $id_compte_session, $id_ticket);
    }
    elseif (EstExpediteur($connexion, $id_compte_session, $id_ticket)){
        ModifierVueReponse($connexion, $id_ticket);
    }
    
    // On récupère les pièces jointes du tickets
    $pieces_jointes_ticket = AfficherPiecesJointesTickets($connexion, $id_ticket);
    
    // Ticket en cours
    $ticket = AfficherUnTicket($connexion, $id_ticket);

    // Compte expéditeur du ticket
    $compte_ticket = AfficherUnCompte($connexion, $ticket['id_compte']);

    // privilège du compte expéditeur du ticket
    $privilege_compte_ticket = $compte_ticket['privilege_compte'];
    
    // Utilisateur du ticket (etudiant/enseignant/agent_admin)
    $utilisateur_ticket = AfficherUtilisateur($connexion, $compte_ticket['id_compte'], $compte_ticket['privilege_compte']);
    
    // ACTIONS
    if(isset($_GET['action']))
    {
        $action = $_GET['action'];

        switch ($action) {
            // DELETE
            case 'delete':
                if (EstExpediteur($connexion, $id_compte_session, $id_ticket))
                {
                    SupprimerTicketExpediteur($connexion, $id_compte_session, $id_ticket);
                    if(VerifSuppressionTicket($connexion, $id_ticket))
                    {
                        SupprimerTicket($connexion, $id_ticket);
                        die(header('Location: '.ROOT.'/Dashboard/TicketsEnvoyes'));

                    } else {
                        die(header('Location: '.ROOT.'/Dashboard/TicketsEnvoyes'));
                    }
                } 
                elseif (EstDestinataire($connexion, $id_compte_session, $id_ticket))
                {
                    SupprimerTicketDestinataire($connexion, $id_compte_session, $id_ticket);
                    if(VerifSuppressionTicket($connexion, $id_ticket))
                    {
                        SupprimerTicket($connexion, $id_ticket);
                        die(header('Location: '.ROOT.'/Dashboard/TicketsRecus'));

                    } else {
                        die(header('Location: '.ROOT.'/Dashboard/TicketsRecus'));
                    }
                }
                break;
            // UPDATE
            case 'update':
                if (EstExpediteur($connexion, $id_compte_session, $id_ticket) && $nombre_vues_ticket == 0)
                {
                    include "./App/Elements/ModifierTicket.php";

                } else die(header('Location: '.ROOT.'/Dashboard/DetailTicket?id='.$id_ticket));
                break;
            // REDIRECT
            case 'redirect':
                if (EstDestinataire($connexion, $id_compte_session, $id_ticket))
                {
                    echo "redirect";
                    include "./App/Elements/RedirigerTicket.php";

                } else die(header('Location: '.ROOT.'/Dashboard/DetailTicket?id='.$id_ticket));
                break;
            // CLOTURE
            case 'cloture':
                if (EstExpediteur($connexion, $id_compte_session, $id_ticket))
                {
                    CloturerTicket($connexion, $id_ticket);
                    die(header('Location: '.ROOT.'/Dashboard/DetailTicket?id='.$id_ticket));
                } else die(header('Location: '.ROOT.'/Dashboard/DetailTicket?id='.$id_ticket));
                break;
        }
    }


    if(isset($_POST['submitReponse'])){

        $contenu_reponse = isset($_POST['contenu_reponse']) ? htmlspecialchars(trim(strip_tags($_POST['contenu_reponse']))) : '';
        $pieces_jointes_tmp = isset($_FILES['pieces_jointes']) ? ReArrayFiles($_FILES['pieces_jointes']) : [];
        $pieces_jointes = array();

        //Vérification de la saisie de la réponse 
        if(empty($contenu_reponse)){
            $erreurs['contenu_reponse'] = "Veuillez renseigner le contenu de la réponse";
        } 

        // Si il ya des pièces jointes
        if (!EmptyPiecesJointes($pieces_jointes_tmp))
        {
            // formats autorisés
            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'docx', 'zip', 'rar', 'sql', 'txt', 'xlsx', 'xlsm', 'html', 'php'];

            for ($i = 0; $i < sizeof($pieces_jointes_tmp); $i++){
                
                $libelle_piece_jointe = pathinfo($pieces_jointes_tmp[$i]['name'], PATHINFO_FILENAME);
                $format_piece_jointe = pathinfo($pieces_jointes_tmp[$i]['name'], PATHINFO_EXTENSION);
                $taille_piece_jointe = $pieces_jointes_tmp[$i]['size'];
                $error = $pieces_jointes_tmp[$i]['error'];
                $tmp_name = $pieces_jointes_tmp[$i]['tmp_name'];

                if (!in_array($format_piece_jointe, $allowed))
                {
                    $erreurs['pieces_jointes'][$i] = "Le type de format du fichier <strong>\"$libelle_piece_jointe.$format_piece_jointe\"</strong> n'est pas autorisé";
                } else if ($error !== 0){
                    $erreurs['pieces_jointes'][$i] = "Il y a eu une erreur lors du chargement du fichier <strong> \"$libelle_piece_jointe.$format_piece_jointe\" </strong>";
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

        if(empty($erreurs)){

            //On creer la réponse dans la base de données
            $id_reponse = CreerReponse($connexion, $contenu_reponse, $id_compte_session, $id_ticket);

            // On verifie si le tickets contient des pieces jointes
            if (!empty($pieces_jointes))
            {
                // Pour chaque pieces jointes
                foreach ($pieces_jointes as $value)
                {
                    // pre_r($value);

                    $libelle_piece_jointe = $value['libelle_piece_jointe'];
                    $format_piece_jointe = $value['format_piece_jointe'];
                    $taille_piece_jointe = $value['taille_piece_jointe'];
                    $tmp_name = $value['tmp_name'];

                    // // On creer la piece jointe dans la base de donnée
                    $id_piece_jointe = CreerPieceJointeBD($connexion, $libelle_piece_jointe, $taille_piece_jointe, $format_piece_jointe);
                    
                    pre_r($id_piece_jointe);
                    // // // On enregistre la piece jointe dans un répertoire sur le serveur
                    if(EnregistrerFichier($id_piece_jointe, $libelle_piece_jointe, $format_piece_jointe, $tmp_name, 'App/Assets/PiecesJointes'))
                    {
                        ContientReponse($connexion, $id_reponse, $id_piece_jointe);
                    }
                }

                die(header('Location: '.ROOT.'/Dashboard/DetailTicket?id='.$id_ticket));

            } else die(header('Location: '.ROOT.'/Dashboard/DetailTicket?id='.$id_ticket));
            
        }

    }
?>


<?php if(!isset($_GET['action'])): ?>

    <!-- DETAIL DU TICKET -->

    <div class="one-post">

        <div class="post-head">
            <h1><?= $utilisateur_ticket['prenom_'.$privilege_compte_ticket].' '.$utilisateur_ticket['nom_'.$privilege_compte_ticket] ?></h1>
            <div class="date">
                <i class="fa fa-clock-o" aria-hidden="true"></i>
                <p><?= $ticket['date_creation_ticket'] ?></p>
            </div>
            

            <!-- ACTION -->
            <span id="show-menu">
                <i id="menu-detail" class="material-icons dp48">more_vert</i>
            </span>
            
            <div class="ellipsis-menu">
                <?php if (EstExpediteur($connexion, $id_compte_session, $id_ticket)): ?>
                    <?php if ($nombre_vues_ticket == 0): ?>
                        <p><a href="<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket.'&action=update' ?>">Modifier</a></p>
                    <?php endif ?>
                        <?php if($ticket['date_cloture_ticket'] == null): ?>
                            <p><a href="<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket.'&action=cloture' ?>">Cloturer</a></p>
                        <?php endif ?>
                    <p><a href="<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket.'&action=delete' ?>">Supprimer</a></p>
                <?php elseif (EstDestinataire($connexion, $id_compte_session, $id_ticket)): ?>
                    <?php if($ticket['date_cloture_ticket'] == null): ?>
                        <p><a href="<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket.'&action=redirect' ?>">Rediriger</a></p>
                    <?php endif ?>
                    <p><a href="<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket.'&action=delete' ?>">Supprimer</a></p>
                <?php endif ?>
            </div>

        </div>

        <div class="post-content">
            <h2><?= AfficherUneCategorie($connexion, $ticket['id_categorie'])['nom_categorie']?></h2>
            <h3><?= $ticket['sujet_ticket'] ?></h3>
            <p><?= $ticket['contenu_ticket'] ?></p>

            <!-- CLOTURE -->
            <?php if($ticket['date_cloture_ticket'] != null): ?>
                <div class="cloture-ticket">
                    <p>cloruré</p>
                    <i id="menu-detail" class="material-icons dp48">do_not_disturb_alt</i>
                </div>
            <?php endif ?>

            <!-- VUES -->
            <div class="vus-ticket">
                <i id="menu-detail" class="material-icons dp48">visibility</i>
                <p><?= $nombre_vues_ticket ?></p>
            </div>
        </div>

        <!-- PIECE(S) JOINTE(S) DU TICKET -->
        <?php if(sizeof($pieces_jointes_ticket) > 0): ?>
            <div class="pieces-jointes">
                <?php   
                    foreach($pieces_jointes_ticket as $value)
                    {
                        $id_piece_jointe = $value['id_piece_jointe'];
                        $libelle_piece_jointe = $value['libelle_piece_jointe'];
                        $format_piece_jointe = $value['format_piece_jointe'];
                        $href_piece_jointe = ROOT.'/App/Assets/PiecesJointes/'.$id_piece_jointe.'/'.$libelle_piece_jointe.'.'.$format_piece_jointe;

                        echo "<a href='$href_piece_jointe' download>$libelle_piece_jointe.$format_piece_jointe</a>";
                    }
                ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- CREER UNE REPONSE -->

    <?php if($ticket['date_cloture_ticket'] == null): ?>
        <div class="form-reponse">

            <div >
                <img src="<?= $href_photo ?>" alt="">
            </div>

            <form action="" method="POST"  enctype="multipart/form-data">

                <div class="form-group">
                    <textarea name="contenu_reponse" id="contenu" cols="70" rows="5" placeholder = "Ecrivez votre réponse à ce ticket ici ..."></textarea>
                    
                    <span class="spanerreur"><?= isset($erreurs['contenu_reponse']) ? $erreurs['contenu_reponse'] : '' ?></span>
                </div>

                <div class="form-group pg">
                    <div>  
                        <label for="file">
                        <input type="file" name="pieces_jointes[]" multiple id="">
                        </label>
                        <?php
                            if (isset($erreurs['pieces_jointes']))
                            {
                                foreach ($erreurs['pieces_jointes'] as $value)
                                {
                                    echo "<p class='spanerreur'>$value</p>";
                                }
                            }
                        ?>
                    </div>
                    <button type="submit" name="submitReponse">Répondre</button>
                    
                </div>
            
            </form>
            
        </div>
    <?php endif ?>
    <!-- REPONSES -->
    <div class="all-reponses">

        <?php
            $reponses = AfficherReponse($connexion, $id_ticket);

            foreach($reponses as $reponse):
            
                $id_reponse = $reponse['id_reponse'];
                $contenu_reponse = $reponse['contenu_reponse'];
                $date_creation_reponse = $reponse['date_creation_reponse'];
                $id_compte_reponse = $reponse['id_compte'];
                $compte_reponse = AfficherUnCompte($connexion, $id_compte_reponse);
                $privige_compte_reponse = $compte_reponse['privilege_compte'];
                $utilisateur_reponse = AfficherUtilisateur($connexion, $id_compte_reponse, $privige_compte_reponse); 
                $nom_utilisateur_reponse = $utilisateur_reponse['nom_'.$privige_compte_reponse];
                $prenom_utilisateur_reponse = $utilisateur_reponse['prenom_'.$privige_compte_reponse];
                $pieces_jointes_reponse = AfficherPiecesJointesReponse($connexion, $id_reponse);
                $id_photo_reponse = $compte_reponse['id_photo'];
                
                if ($id_photo_reponse != null){
                    $photo_reponse = AfficherUnePhoto($connexion, $id_photo_reponse);
                    $libelle_photo_reponse = $photo_reponse['libelle_photo'];
                    $format_photo_reponse = $photo_reponse['format_photo'];
                    $href_photo_reponse = ROOT.'/App/Assets/ImagesProfiles/'.$id_photo_reponse.'/'.$libelle_photo_reponse.'.'.$format_photo_reponse;

                } else {
                   $href_photo_reponse = ROOT.'/App/Assets/ImagesProfiles/default.png';
                }
                $photo_reponse = AfficherUnePhoto($connexion, $id_photo_reponse);

        ?>
                    <div class='reponse'>
                        <div>
                            <img src='<?= $href_photo_reponse ?>' alt=''>
                        </div>

                        <div>
                            <div class='head'>
                                <h1><?= $prenom_utilisateur_reponse.' '.$nom_utilisateur_reponse ?></h1>
                                <div class='date'>
                                    <i class='fa fa-clock-o' aria-hidden='true'></i>
                                    <p><?= $date_creation_reponse ?></p>
                                </div>
                            </div>
                            <div class='contenu'>
                                <p><?= $contenu_reponse ?></p>
                            </div>
                            <!-- PIECE(S) JOINTE(S) DES REPONSES -->
                            <?php if (sizeof($pieces_jointes_reponse) > 0): ?>
                                <div class='pieces-jointes'>
                                    <?php foreach($pieces_jointes_reponse as $value):
                                        
                                            $id_piece_jointe = $value['id_piece_jointe'];
                                            $libelle_piece_jointe = $value['libelle_piece_jointe'];
                                            $format_piece_jointe = $value['format_piece_jointe'];
                                            $href_piece_jointe = ROOT.'/App/Assets/PiecesJointes/'.$id_piece_jointe.'/'.$libelle_piece_jointe.'.'.$format_piece_jointe;
                                    ?>
                                        <a href='<?= $href_piece_jointe ?>' download><?= $libelle_piece_jointe.'.'.$format_piece_jointe ?></a>
                                        
                                    <?php endforeach ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                
           <?php endforeach ?>

 
    </div>

<?php endif ?>
    
<script src="<?= ROOT.'/Public/Js/tickets.js'?>"></script>