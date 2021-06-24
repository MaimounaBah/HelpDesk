<?php 

    include './App/Database/Connect.php';
    include './App/Functions/Tools.php';
    include './App/Functions/Tickets.php';
    include './App/Functions/Comptes.php';


    // On démarre la sesson si elle n'est pas encore démarée
    init_session();

    $PROTOCOL = $_SERVER['REQUEST_SCHEME'];
    $SERVER_NAME = $_SERVER['SERVER_NAME'];
    $FILES_PATH = str_replace('/index.php', '', $_SERVER['PHP_SELF']);

    // RACINE DU SITE WEB | https://localhost/Fichiers

    define('ROOT', $PROTOCOL.'://'.$SERVER_NAME.$FILES_PATH);

    $pages = scandir('App/Pages/');
    
    $param = isset($_GET['p']) ? explode('/', $_GET['p']) : [];

    $page = !empty($param) ? $param[0] : '';
    
    if (in_array($page.'.php', $pages)) 
    {
        $page = 'App/Pages/'.$page.'.php';
    } 
  
    elseif(in_array($page, $pages))
    {
        $fichier = 'App/Pages/'.$page;
        $pages = scandir($fichier);
        $page = isset($param[1]) ? $param[1] : '';
        
        if (in_array($page.'.php', $pages))
        {
            $page = $fichier.'/'.$page.'.php';
        }
        else 
        {
            $page = $fichier.'/Index.php';
        }
    } 

    else 
    {

        $page = 'App/Pages/Connexion.php';
    }

    // NAVIGATION STYLE
    $nav = isset($_GET['p']) ? $_GET['p'] : null;


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HELP DESK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <?php if (!is_logged()) : ?>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.min.css'>
        <link rel="stylesheet" href="<?= ROOT.'/Public/Css/signin_signup.css'?>">
        <link rel="stylesheet" href="<?= ROOT.'/Public/Css/cgu.css'?>">
    <?php else: ?>
        <link rel="stylesheet" href="<?= ROOT.'/Public/Css/tickets.css'?>">
        <link rel="stylesheet" href="<?= ROOT.'/Public/Css/style.css'?>">
        <link rel="stylesheet" href="<?= ROOT.'/Public/Css/jquery.flexdatalist.css'?>">
        <link rel="stylesheet" href="<?= ROOT.'/Public/Css/edit_profile.css'?>">
    <?php endif; ?>

</head>
<body>
    <!-- NON CONNECTEE -->
    <?php 
        if (!is_logged()):
           
             include $page;
    
        else:

            $id_compte_session = $_SESSION['auth']['id_compte'];

            // COMPTE

            $connexion = getConnection();
            $compte_session = AfficherUnCompte($connexion, $id_compte_session);
            $privilege_compte_session = $compte_session['privilege_compte'];
            $email_compte_session = $compte_session['email_compte'];
            $id_photo_session = $compte_session['id_photo'];


            $statut_string = $privilege_compte_session == 'etudiant' ? 'Etudiant' : ($privilege_compte_session == 'enseignant' ? 'Enseignant' : ($privilege_compte_session == 'agent_admin' ? 'Agent administratif' : ($privilege_compte_session == 'administrateur' ? 'Administrateur' : null)));

            // UTILISATEUR
            $utilisateur = AfficherUtilisateur($connexion, $id_compte_session, $privilege_compte_session);
            $id_utilisateur = $utilisateur['id_'.$privilege_compte_session];
            $nom_utilisateur = $utilisateur['nom_'.$privilege_compte_session];
            $prenom_utilisateur = $utilisateur['prenom_'.$privilege_compte_session];
            $sexe_utilisateur = $utilisateur['sexe_'.$privilege_compte_session];
            
            //PROMOTION
            if ($privilege_compte_session == 'etudiant') {
                $id_promotion_session = $utilisateur['id_promotion'];
            }

            // VERIFICATION PHOTO
            if ($id_photo_session != null){

                // Information convernant la photo de l'utilisateur connecté
                $photo_session = AfficherUnePhoto($connexion, $id_photo_session);
                $libelle_photo_session = $photo_session['libelle_photo'];
                $format_photo_session = $photo_session['format_photo'];
                $href_photo = ROOT.'/App/Assets/ImagesProfiles/'.$id_photo_session.'/'.$libelle_photo_session.'.'.$format_photo_session;
            
            } else{
                $href_photo = ROOT.'/App/Assets/ImagesProfiles/default.png';
            }

            // NOTIFICATIONS

            $notification_tickets = AfficherTicketsNonVue($connexion, $id_compte_session);
            $notification_reponses = AfficherTicketsEnvoyesAvecReponses($connexion, $id_compte_session);
            
            $nombre_notifications = sizeof($notification_tickets) + sizeof($notification_reponses);

           
    ?>  

    <!-- CONNECTEE -->


        <!-- NAVBAR -->
        <nav>
            <i id="toggle" class="material-icons dp48 left">menu</i>
            <div>

                <div class="notification-icon">
                    <i class="material-icons dp48">notifications</i>
                    <?php if ($nombre_notifications > 0): ?>
                        <span class="num-count"><?= $nombre_notifications ?></span>
                    <?php endif ?>
                </div>

                <div class="profile">
                    <span class="user-photo">
                    <img src="<?= $href_photo ?>" alt="" srcset="">
                    </span>
                    <span class="first-name right"><?= $prenom_utilisateur.' '.$nom_utilisateur ?></span>
                </div>
            </div>
        </nav>


        <!-- MAIN -->
        <main class="main">

            <!-- MENU -->
    
            <ul class="menu active">
                
                <li><a href="<?= ROOT.'/Dashboard' ?>"><i class="material-icons dp48 gray left">dashboard</i> Tableau de bord</a></li>

                <li class="dropdown"><i class="material-icons dp48 grey left">question_answer</i> Tickets <i class="material-icons dp48">expand_more</i></li>
                <ul class="active">
                    <li class="active">
                        <a href="<?= ROOT.'/Dashboard/TicketsRecus' ?>">reçus 
                            <?php if (sizeof($notification_tickets) > 0): ?>
                                <span class="num-count gray-bg"><?= sizeof($notification_tickets) ?></span>
                            <?php endif ?>
                        </a>
                    </li>
                    <li><a href="<?= ROOT.'/Dashboard/TicketsEnvoyes' ?>">envoyés</a></li>
                    
                </ul>
                <?php if ($privilege_compte_session == "administrateur"): ?>
                    <li><a href="<?= ROOT.'/Dashboard/Statistiques' ?>"><i class="material-icons dp48 gray left">timeline</i> Statistiques </a></li>
                <?php endif; ?>
                <footer class="contact">

                    <p>Si vous rencontrez des problème, veuillez contacter les administrateurs du site</p>
                    <div>

                    <?php $email_administrateurs = AfficherEmailAdministrateurs($connexion); 
                            foreach($email_administrateurs as $value):
                        ?>
                            <p><?= $value['email_compte'] ?></p>
                        <?php endforeach ?>
                    </div>
                </footer>
            </ul>

            
            <!-- CONTENT -->
            <!-- <div class="contenttot"> -->
                <div class="content">
                <?php 
                    include $page;
                ?>
                </div>

            <!-- </div> -->

            <!-- NOTIFICATIONS -->
            
            <div class="notification-container">
                <h3>Notifications</h3>
                <div class="notification-area">
                    <!--reponse ticket non-vus-->
                    <?php
                            foreach($notification_reponses as $value):
                                $id_reponse_ticket = $value['id_ticket'];
                                $nombre_reponse = $value['reponse_ticket'];
                    ?>
                        <?php if($nombre_reponse > 0): ?>
                                <div class='notification new' for='size_1'>
                                    <div><em><?= $nombre_reponse ?></em> nouvelle reponse pour votre <a href='<?= ROOT.'/Dashboard/DetailTicket?id='.$id_reponse_ticket ?>'>ticket</a></div>
                                    <div class='creation-ticket'>
                                        <p></p>
                                    </div> 
                                </div>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php foreach($notification_tickets as $value): 
                        
                            $id_ticket_expediteur = $value['id_ticket'];
                            $date_creation_ticket = $value['date_creation_ticket'];
                            $id_compte_expediteur = $value['id_compte'];
                            $privilege_expediteur = $value['privilege_compte'];
                            $utilisateur_expediteur = AfficherUtilisateur($connexion, $id_compte_expediteur, $privilege_expediteur);
                            $prenom_expediteur = $utilisateur_expediteur['prenom_'.$privilege_expediteur];
                            $nom_expediteur = $utilisateur_expediteur['nom_'.$privilege_expediteur];
                    ?>     
                                <div class='notification new' for='size_1'>
                                    <div><em>1</em> nouveaux <a href='<?= ROOT.'/Dashboard/DetailTicket?id='.$id_ticket_expediteur ?>'>ticket</a> envoyé par <?= $prenom_expediteur.' '.$nom_expediteur ?></div>
                                    <div class='creation-ticket'>
                                        <i class='material-icons dp48'>schedule</i>
                                        <p><?= $date_creation_ticket ?></p>
                                    </div> 
                                </div>
                    <?php endforeach ?>
                </div>
            </div>

            <!-- PROFIL -->
            <div class="profile-container">

                <a class="right"><i class="material-icons dp48 right">settings</i></a>

                <span class="user-photo left">
                    <img src="<?= $href_photo ?>" alt="" srcset="">
                </span>

                <h1 class="user-name"><a><?= $prenom_utilisateur.' '.$nom_utilisateur ?></a></h1>
                <p><?= $statut_string ?></p>

                <hr />
                <button class="button secondary-button left"><a href="<?= ROOT.'/Dashboard/EditerProfil'?>">Mon profil</a></button>
                <button class="button primary-button right"><a href="<?= ROOT.'/Deconnexion'?>">Déconnexion</a></button>
                
            </div>

        </main>

        


    <?php endif; ?>

    
    <script src="<?= ROOT.'/Public/Js/script.js'?>"></script> 


</body>
</html>