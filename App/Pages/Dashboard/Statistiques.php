<?php

    if (!is_logged())
    {
        die(header('Location: '.ROOT.'/Connexion'));
    } 
    elseif ($privilege_compte_session != 'administrateur')
    {
        die(header('Location: '.ROOT.'/Dashboard'));
    }

    include 'App/Functions/Stats.php';

    $connexion = getConnection();

    $nb_tickets = NombreTickets($connexion);
    $nb_tickets_non_cloture = NombreTicketsNonCLoture($connexion);
    $categories = CategorieNbTicket($connexion);
    $temps_moyen = TempsMoyenTickets($connexion);
    $evolution_nombre_ticket = EvolutionNombreTickets($connexion);

?>



<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

    google.charts.load('current', {'packages':['corechart']});
    google.charts.load('current', {'packages':['line']});
    google.charts.setOnLoadCallback(drawCategorieChart);
    google.charts.setOnLoadCallback(drawTicketsChart);

    function drawCategorieChart() {

        var data = google.visualization.arrayToDataTable([
            ['Categorie', 'Nombre de tickets'],
            <?php
                    foreach($categories as $key => $value)
                    {   
                        echo '["'.$value['nom_categorie'].'",    '.$value['nombres_tickets'].'],';
                    }
            ?>
        ]);

        var options = {
          title: 'Répartition des tickets par catégories',
          backgroundColor: 'F9F9F9',
          colors: ['#412234', '#6d466b', '#e7cee3', '#d53467', '#00afb9', '0081a7'],
          height: 350,
          
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }

    function drawTicketsChart() {

        var data = google.visualization.arrayToDataTable([
            ["string","tickets"],
            
            <?php 
                if (!empty($evolution_nombre_ticket))
                {
                    foreach($evolution_nombre_ticket as $value)
                    {
                        echo '["'.$value['mois'].'", '.$value['nombre_ticket'].'],';
                    }
                } else {
                    echo '[,],';
                }
                
            ?>
        ]);

        var options = 
        {
            title: "Evolution du nombre de ticket pour l'année scolaire en cours",
            backgroundColor: 'F9F9F9',
            
            is3D: true,
            pointSize: 5,
            
            height: 350,
            legend: {
                position: "none"
            },
            hAxis:{
                title: "Mois",
            },
            vAxis: {
                title: "Nombre de tickets",
                gridlines: { color: 'none' },
            }

        };

        var chart = new google.visualization.LineChart(document.getElementById('tickets_chart'));

        var formatter = new google.visualization.DateFormat({pattern: 'MMM'});
        formatter.format(data, 0);
        chart.draw(data, options);
    }

</script>

    
    <div class="page-head">
        <h1>Statistiques</h1>
        <!-- <p>(An example table + detail view scenario) </p> -->
    </div>

    <div class="all-counter">

        <div class="counter">
            <i class="material-icons dp48 grey left">library_books</i>
            <p><?= $nb_tickets ?></p>
            <span>Tickets</span>
        </div>
        <div class="counter">
            <i class="material-icons dp48 grey left">library_add_check</i>
            <p><?= $nb_tickets_non_cloture ?></p>
            <span>Ticket non cloturé</span>
        </div>

        <div class="counter">
            <i class="material-icons dp48 grey left">restore</i>
            <p id="tmp-avg"><?= $temps_moyen ?></p>
            <span>de temps moyen par ticket</span>
        </div>

    </div>

    <div class="stats-circle">
        <div id="piechart" class="chart" ></div>

        <div id="tickets_chart" class="ticket_chart" ></div>
    </div>
