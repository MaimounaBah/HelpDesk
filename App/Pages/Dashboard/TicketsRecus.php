<?php
    !is_logged() ? die(header('Location: '.ROOT.'/Connexion')) : null;


    // PAGINATION
    if(isset($_GET['page']) && !empty($_GET['page'])){
        $page_courante = (int) strip_tags($_GET['page']);
    }else{
        $page_courante = 1;
    }

    $nombre_tickets = AfficherNombreTicketsRecus($connexion, $id_compte_session);

    // On détermine le nombre de tickets par page
    $par_page = 5;

    // On calcule le nombre de pages total
    $pages = ceil($nombre_tickets / $par_page);

    // Calcul du 1er ticket de la page
    $premier = ($page_courante * $par_page) - $par_page;

    $afficher = AfficherTicketsRecus($connexion, $id_compte_session, $premier, $par_page);


?>

<h1>Tickets recus</h1>


<ul class="page">
    <li><a class="page__btn <?= $page_courante != 1 ? 'active' : '' ?>" href="<?= ROOT."/Dashboard/TicketsRecus?page=".($page_courante - 1) ?>">
        <span class="material-icons">chevron_left</span>
    </a></li>
    <?php for ($page = 1; $page <= $pages; $page++): ?>
        <li>
            <a class="page__numbers <?= $page == $page_courante ? 'active' : '' ?> " href="<?= ROOT.'/Dashboard/TicketsRecus?page='.$page ?>"><?= $page ?></a>
        </li>
    <?php endfor ?>
    <li><a class="page__btn <?= $page_courante != $pages ? 'active' : '' ?>" href="<?= ROOT."/Dashboard/TicketsRecus?page=".($page_courante + 1) ?>"><span class="material-icons">chevron_right</span></a></li>
</ul>


<main>
    <table>
        <thead>
            <tr>
                <th>Catégorie</th>
                <th>Sujet</th>
                <th>Expéditeur</th>
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
                    $vu_ticket =  $afficher[$i]['vu_ticket'];
                    $statut_ticket = $afficher[$i]['date_cloture_ticket'];
                    $date_creation_ticket = $afficher[$i]['date_creation_ticket'].'<br>';
                    $expediteur =  AfficherExpediteurTicket($connexion,  $id_ticket)['email_compte'].'<br>'; 
            ?> 
                        <tr class='<?= $vu_ticket ? 'vu-ticket' : '' ?>'>
                            <td data-title='Catégorie'><?= $nom_categorie ?></td>
                            <td data-title='Sujet'><?= $sujet_ticket ?></td>
                            <td data-title='Expéditeur'><p><?= $expediteur ?></p></td>
                            <td data-title='Date'><?= $date_creation_ticket ?></td>
                            <td data-title='Statut'><?= $statut_ticket == null ? 'Non cloturé' : 'Cloruré' ?></td>
                            <td class='select'>
                            <a class='detail' href='<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket ?>'>Détail</a>
                            </td>
                        </tr>
                    
               <?php endfor ?>
            
            
        </tbody>
    </table>
  
</main>

