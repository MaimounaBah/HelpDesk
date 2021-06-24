<?php

    /*  ---------  MODIFIER UN TICKET  ---------  */


    if (isset($_POST['submit_update']))
    {
        $id_categorie = isset($_POST['id_categorie_update']) ? $_POST['id_categorie_update'] : '';
        $sujet_ticket = isset($_POST['sujet_ticket_update']) ? $_POST['sujet_ticket_update'] : '';
        $contenu_ticket = isset($_POST['contenu_ticket_update']) ? $_POST['contenu_ticket_update'] : '';

        empty($id_categorie) ? $erreurs['id_categorie_update'] = "Veuillez renseigner ce champs" : null;
        empty($sujet_ticket) ? $erreurs['sujet_ticket_update'] = "Veuillez renseigner ce champs" : null;
        empty($contenu_ticket) ? $erreurs['contenu_ticket_update'] = "Veuillez renseigner ce champs" : null;

        if (empty($erreurs))
        {
            if(ModifierTicket($connexion, $id_ticket, $sujet_ticket, $contenu_ticket, $id_categorie))
            {
                die(header('Location: '.ROOT.'/Dashboard/DetailTicket?id='.$id_ticket));
            }
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
        </div>

        <div class="post-content">
            <form action="" method="POST">
                <div class="form-group">
                    <label for="">Catégorie</label>
                    <select name="id_categorie_update" id="">
                        <?php foreach(AfficherCategories($connexion) as $value): ?>
                            <option value="<?= $value['id_categorie']  ?>"><?= $value['nom_categorie'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="spanerreur"><?= isset($erreurs['id_categorie_update']) ? $erreurs['id_categorie_update'] : '' ?></span>
                </div>
                <div class="form-group">
                    <label for="">Sujet</label>
                    <input type="text" name="sujet_ticket_update" id="" value="<?= $ticket['sujet_ticket'] ?>">
                    <span class="spanerreur"><?= isset($erreurs['sujet_ticket_update']) ? $erreurs['sujet_ticket_update'] : '' ?></span>
                </div>
                <div class="form-group">
                    <label for="">Contenu</label>
                    <textarea name="contenu_ticket_update" id="" cols="30" rows="10"><?= $ticket['contenu_ticket'] ?></textarea>
                    <span class="spanerreur"><?= isset($erreurs['contenu_ticket_update']) ? $erreurs['contenu_ticket_update'] : '' ?></span>
                </div>
                <div class="form-group btn">
                    <a class="button" href="<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket ?>">Retour</a>
                    <button type="submit" name="submit_update">Modifier</button>
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