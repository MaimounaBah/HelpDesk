<?php
    !is_logged() ? die(header('Location: '.ROOT.'/Connexion')) : null;
     
    // VERIFICATION DU PRIVILEGE DU COMPTE
    if ($privilege_compte_session == 'etudiant')
    {
        include './App/Elements/NouveauTicketEtd.php';
    } 
    elseif($privilege_compte_session == 'agent_admin' || $privilege_compte_session == 'enseignant')
    {
        include './App/Elements/NouveauTicketEsnAdmin.php';
    }


    // PAGINATION
    if(isset($_GET['page']) && !empty($_GET['page'])){
        $page_courante = (int) strip_tags($_GET['page']);
    }else{
        $page_courante = 1;
    }

    $nombre_tickets = AfficherNombreTicketsEnvoyes($connexion, $id_compte_session);

    // On détermine le nombre de tickets par page
    $par_page = 5;

    // On calcule le nombre de pages total
    $pages = ceil($nombre_tickets / $par_page);

    // Calcul du 1er ticket de la page
    $premier = ($page_courante * $par_page) - $par_page;

    $afficher = AfficherTicketsEnvoyes($connexion, $id_compte_session, $premier, $par_page);

?>

<div class="head-ticket">

    <h1 class="title_profil">Tickets envoyés</h1>
    <a href="<?= ROOT.'/Dashboard/TicketsEnvoyes?modale=true' ?>" class="show-modal">Nouveau ticket</a>

</div>


<ul class="page">
    <li><a class="page__btn <?= $page_courante != 1 ? 'active' : '' ?>" href="<?= ROOT."/Dashboard/TicketsEnvoyes?page=".($page_courante - 1) ?>">
        <span class="material-icons">chevron_left</span>
    </a></li>
    <?php for ($page = 1; $page <= $pages; $page++): ?>
        <li>
            <a class="page__numbers <?= $page == $page_courante ? 'active' : '' ?> " href="<?= ROOT.'/Dashboard/TicketsEnvoyes?page='.$page ?>"><?= $page ?></a>
        </li>
    <?php endfor ?>
    <li><a class="page__btn <?= $page_courante != $pages ? 'active' : '' ?>" href="<?= ROOT."/Dashboard/TicketsEnvoyes?page=".($page_courante + 1) ?>"><span class="material-icons">chevron_right</span></a></li>
</ul>

<main>
    <table>
        <thead>
            <tr>
                <th>Catégorie</th>
                <th>Sujet</th>
                <th>Destinataire(s)</th>
                <th>Date</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
    
        <tbody>
            <?php
                for ($i=0; $i < count($afficher); $i++):
                    $id_ticket = $afficher[$i]['id_ticket'];
                    $sujet_ticket = $afficher[$i]['sujet_ticket'];
                    $id_categorie = $afficher[$i]['id_categorie'];
                    $categorie = AfficherUneCategorie($connexion, $id_categorie);
                    $nom_categorie = $categorie['nom_categorie'];
                    $statut_ticket = $afficher[$i]['date_cloture_ticket'];
                    $dateCreation = $afficher[$i]['date_creation_ticket'];
                    $destinataires = AfficherDestinatairesTicket($connexion, $id_ticket);
                    $destinataires_temp = " ";
                    $nombre_destinataires = count($destinataires);
                    $stop = $nombre_destinataires >= 2 ? 2 : 1;
                ?>
                        <tr>
                            <td data-title='Catégorie'><?= $nom_categorie ?></td>
                            <td data-title='Sujet'><?= $sujet_ticket ?></td>
                            <td class="td-destinataires" data-title='Destinataire(s)'>
                                
                                <span title= '<?php 
                                    for ($j= 0; $j < count($destinataires); $j++): 
                                        $destinataires_temp = $destinataires[$j]['email_compte'];
                                        echo $destinataires_temp."\n";
                                    endfor
                                    ?>'> <i class="material-icons dp48 grey left">supervisor_account</i>
                                
                                <?= $nombre_destinataires ?></span>
                            </td>
                            <td data-title='Date'><?= $dateCreation ?></td>
                            <td data-title='Statut'><?= $statut_ticket == null ? 'Non cloturé' : 'Cloruré' ?></td>
                            <td class='select'>
                            <a class='detail' href='<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket ?>'>Détail</a>
                            </td>
                        </tr>  
              <?php endfor ?>  
            
        </tbody>
    </table>
  
</main>

    


<script src="https://code.jquery.com/jquery-1.8.3.min.js"></script>
<script src="<?= ROOT.'/Public/Js/jquery.flexdatalist.min.js'?>"></script>
<script src="<?= ROOT.'/Public/Js/Nouveau_ticket.js'?>"></script>