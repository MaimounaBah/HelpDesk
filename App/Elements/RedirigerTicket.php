<?php

    /*  ---------  REDIRIGER UN TICKET  ---------  */

    $comptes = AfficherComptes($connexion, $id_compte_session);

    if (isset($_POST['submit_redirect']))
    {
        $comptes_ticket = array();
        $comptes_ticket_tmp = isset($_POST['comptes_ticket']) ? $_POST['comptes_ticket'] : '';

        // VERIFICATION COMPTES
        if (empty($comptes_ticket_tmp)){
            $erreurs['compte_ticket'] = "Veuillez selectionnez un compte";
        } elseif(sizeof(explode(',', $comptes_ticket_tmp)) > 3){
            $erreurs['compte_ticket'] = "Veuillez rentrer maximum 3 comptes";
        } else $comptes_ticket = explode(',', $comptes_ticket_tmp);

        if (empty($erreurs))
        {
            foreach($comptes_ticket as $value)
            {
                // On récupère l'identidiant du compte à partir de l'email
                $id_compte_destinataire = GetIdCompte($connexion, $value);
                RecevoirTicket($connexion, $id_ticket, $id_compte_destinataire);
            }
            die(header('Location: '.ROOT.'/Dashboard/DetailTicket?id='.$id_ticket));
        }
    }

?>


    <div class="one-post">

        <div class="post-head">
            <h1><?= $utilisateur_ticket['prenom_'.$privilege_compte_ticket].' '.$utilisateur_ticket['nom_'.$privilege_compte_ticket] ?></h1>
            <div class="date">
                <i class="fa fa-clock-o" aria-hidden="true"></i>
                <p><?= $ticket['date_creation_ticket'] ?></p>
            </div>

            <i class="material-icons dp48 grey left">reply</i>
        </div>

        <div class="post-content">
            <form action="" method="POST">
                <div class="form-group">

                    <label class="label">Destinataire</label>
                    <!-- COMPTES -->
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
                    <p class="spanerreur"><?= isset($erreurs['compte_ticket']) ? $erreurs['compte_ticket'] : '' ?></p>

                </div>

                <div class="form-group btn">
                    <a class="button" href="<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket ?>">Retour</a>
                    <button type="submit" name="submit_redirect">Rediriger</button>
                </div>
                
            </form>
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

<script src="https://code.jquery.com/jquery-1.8.3.min.js"></script>
<script src="<?= ROOT.'/Public/Js/jquery.flexdatalist.min.js'?>"></script>