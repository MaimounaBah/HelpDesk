<?php
    !is_logged() ? die(header('Location: '.ROOT.'/Connexion')) : null;

    include 'App/Functions/Stats.php';

    $nombre_tickets_envoyes = AfficherNombreTicketsEnvoyes($connexion, $id_compte_session);
    $nombre_tickets_recus = AfficherNombreTicketsRecus($connexion, $id_compte_session);

    $nombre_tickets_envoyes_semaine = NombreTicketsEnvoyesSemaine($connexion, $id_compte_session);
    $nombre_tickets_recus_semaine = NombreTicketsRecusSemaine($connexion, $id_compte_session);
    
?>

<h1>Espace personnel</h1>

<div class="home-container">

    <div id="chartjs-radar">
            <canvas id="canvas"></canvas>
    </div>


    <div class="all-counter-home">

        <div class="counter">
            <i class="material-icons dp48 grey left">library_books</i>
            <p><?= $nombre_tickets_envoyes ?></p>
            <span>Tickets envoyés</span>
        </div>
        <div class="counter">
            <i class="material-icons dp48 grey left">library_add_check</i>
            <p><?= $nombre_tickets_recus ?></p>
            <span>Ticket reçus</span>
        </div>

    </div>

</div>



<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js'></script>


<script>

    var labels = []
    

    for (let i = 0; i < 7; i++) {

        var date = new Date()
        date.setDate(date.getDate() - i)
        let dateLocale = date.toLocaleString('fr-FR',{
            weekday: 'long',
            month: 'long',
            day: 'numeric',
        });

        labels.push(dateLocale)
    }

    const data = 
    {

        labels: labels,
        datasets: 
        [
            {
                label: 'Tickets envoyés',
                data: [

                    <?php 
                        foreach($nombre_tickets_envoyes_semaine as $value){
                            echo $value['nombre_tickets'].',';
                        }
                    ?>
                ],
                fill: true,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgb(255, 99, 132)',
                pointBackgroundColor: 'rgb(255, 99, 132)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(255, 99, 132)'
            }, 
            {
                label: 'Tickets reçus',
                data: [

                    <?php 
                        foreach($nombre_tickets_recus_semaine as $value){
                            echo $value['nombre_tickets'].',';
                        }
                    ?>
                ],
                fill: true,
                backgroundColor: 'rgba(95,158,160, 0.2)',
                borderColor: 'rgb(95,158,160)',
                pointBackgroundColor: 'rgb(95,158,160)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(95,158,160)'
            }
        ]
    };


    const config = 
    {
        type: 'radar',
        data: data,
        options: 
        {
            title: 
            {
                display: true,
                text: 'Acticité au cours des 7 derniers jours' 
            },
            legend: 
            {
                position: 'bottom',
            },
            elements: 
            {
                line: 
                {
                    borderWidth: 3
                }
            }
        },
    };



    window.onload = function () {
    window.myRadar = new Chart(document.getElementById("canvas"), config);
    };

</script>





