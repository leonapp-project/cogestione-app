<?php

error_reporting(-1);
ini_set('display_errors', 'On');

//connect to database
require 'mysqli.php';
// Retrieve the list of activities from the database
$sql = "SELECT * FROM attivita ORDER BY ora ASC";
$result = $conn->query($sql);

// Group the activities by hour
$activities = array();
while ($row = $result->fetch_assoc()) {
    $hour = $row['ora'];
    if (!isset($activities[$hour])) {
        $activities[$hour] = array();
    }
    array_push($activities[$hour], $row);
}
$many_activities = count($activities);
$blocks = array();
foreach ($activities as $hour => $data) {
    if(in_array($hour, $blocks)) {
        continue;
    }
    array_push($blocks, $hour);
}
$many_blocks = count($activities);

// SELEZIONE NOME
$session_id = $_COOKIE['sessionid'];
$select_stmt = $conn->prepare("SELECT * FROM utenti WHERE sessionid = ?");
$select_stmt->bind_param("s", $session_id);
$select_stmt->execute();
$result = $select_stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    //se manca qualche informazione, reindirizza a completa.php
    if(!isset($user['classe']) || !isset($user['sezione']) || !isset($user['indirizzo'])) {
        header('Location: completa.php');
    }
    $nome = $user['nome'];
    $id_prenotante = $user['id'];
    $classe = $user['classe'];
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/x-icon" href="./src/img/logo.png">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title>Cogestione 2023</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <script src="https://kit.fontawesome.com/b188873eef.js" crossorigin="anonymous"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./src/css/selezione.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="./src/css/button.css">

    <script>
        const numero_blocchi = <?php echo $many_blocks; ?>;
        const numero_attivita = <?php echo $many_activities; ?>;
        var stato_blocchi = new Array(numero_blocchi);

        //selezione_corrente = 2;

        /*
         * 0 = blocco non selezionato
         * 1 = blocco selezionato
         * 2 = blocco completato
         * 3 = blocco bloccato (non implementato in quanto non serve)
        */
       
        function recupera_stato_blocchi() {
<?php

//controlla se l'utente ha già almeno una prenotazione nel database
$select_stmt = $conn->prepare("SELECT * FROM prenotazioni_attivita WHERE id_prenotante = ?");
$select_stmt->bind_param("i", $id_prenotante);
$select_stmt->execute();
$prenotazion_risultato = $select_stmt->get_result();
if($prenotazion_risultato->num_rows > 0) {
    echo "for(i=0, l=1; i<numero_blocchi; i++, l++) {stato_blocchi[i] = 2;}return;";
} else {
    echo "for(i=0; i<numero_blocchi; i++) {
        stato_blocchi[i] = 0;
    }
    ";
    if(isset($_COOKIE['resume'])) {
        $good_selections = array();
        $resume = $_COOKIE['resume'];
        //Set the stato_blocchi array to the resume cookie values that are not null
        $resume = explode(";", $resume);
        //print_r($resume);
        for($i=0; $i<count($resume); $i++) {
            if(isset($resume[$i])) {
                $good_selections[$i] = $resume[$i];
            }
        }
        $good_selections = array_values($good_selections);
        for($i=0; $i<count($good_selections); $i++) {
            $esploso = explode(":", $good_selections[$i]);
            if(!isset($esploso[1])) {
                continue;
            }
            echo "         stato_blocchi[".$i."] = ".$esploso[1].";\n";
        }
    } else {
        echo "stato_blocchi[0] = 1;";
    }

}

//se un'attivita ha zero posti disponibili, la disabilita
$select_stmt = $conn->prepare("SELECT * FROM attivita");
$select_stmt->execute();
$result = $select_stmt->get_result();

if($result->num_rows > 0) {
    $classe = $user['classe'];
    while($row = $result->fetch_assoc()) {
        $ora = $row['id'];

        if($row['posti'] == 0) {
            echo "document.querySelector('.attivita-";
            echo $ora;
            echo "').classList.add('disabled-activity');";
        }
       
        //se la $row['riservato] è uguae a BIENNIO e la classe è 3,4,5, disabilita l'attività
        if($row['riservato'] == "BIENNIO" && ($classe == 3 || $classe == 4 || $classe == 5)) {
            echo "\ndocument.querySelector('.attivita-";
            echo $ora;
            echo "').classList.add('disabled-activity');";
        }
        //se la $row['riservato] è uguae a TRIENNIO e la classe è 1,2, disabilita l'attività
        if($row['riservato'] == "TRIENNIO" && ($classe == 1 || $classe == 2)) {
            echo "\ndocument.querySelector('.attivita-";
            echo $ora;
            echo "').classList.add('disabled-activity');";
        }
    }
}



?>
        }
        function recupera_selezione_blocchi() {
            //se l'utente nel database ha prenotazioni, inserisci per ogni act-id l'attivita-id selezionata dentro il dizionario attivita_selezionate = {}
var attivita_selezionate = {};
<?php
if($prenotazion_risultato->num_rows > 0) {
    while($row = $prenotazion_risultato->fetch_assoc()) {
        $id_blocco = $row['id_blocco'];
        $id_attivita = $row['id_attivita'];
        echo "attivita_selezionate['".$id_blocco."'] = '".$id_attivita."';\n";
    }
}
?>
//ora per ogni blocco imposta il suo titolo al titolo dell'attività selezionata
for(var i=1; i<=numero_blocchi; i++) {
    if(attivita_selezionate[i] != null) {
        nome_att = document.querySelector(".attivita-"+attivita_selezionate[i]).querySelector(".activity--name").innerHTML;
        document.querySelector(".act-"+i+" .selezione-titolo").innerHTML = nome_att;

        document.querySelector(".selezione-blocco-"+i).value = attivita_selezionate[i];
    }
}
        }
        function disableButtons() {
            for(var i=1; i<=numero_attivita; i++) {
                document.querySelector(".act-"+i).classList.add("disabled_block_button");
            }
        }

        function resumeStatus() {
            //disableButtons();
            for(i=0, l=1; i<numero_blocchi; i++, l++) {
                console.log("act-"+l+stato_blocchi[i]);
                if(stato_blocchi[i] == 0) {
                    console.log("act-"+l+" non selezionato");
                    document.querySelector(".act-"+l).classList.add("disabled_block_button");
                } else if(stato_blocchi[i] == 1) {
                    console.log("act-"+l+" selezionato");
                    document.querySelector(".act-"+l).classList.add("selezionato");
                    document.querySelector(".act-"+l).classList.add("show");
                    document.querySelector(".act-"+l).querySelector(".rows").classList.add("show");
                } else if(stato_blocchi[i] == 2) {
                    document.querySelector(".act-"+l).classList.add("completato");
                    document.querySelector(".act-"+l).classList.remove("show");
                    document.querySelector(".act-"+l).querySelector(".rows").classList.remove("show");
                }
            }
            
        }

        function activityClick(id) {
            const wrapper = document.querySelector(".act-"+id);
            const rows = wrapper.querySelector(".rows");
            wrapper.classList.toggle("show");
            rows.classList.toggle("show");
        }

        function selezionaAttivita(id_blocco, id_attivita) {
            console.log("Selezionato blocco "+id_blocco+" attivita "+id_attivita);
            var blocco_corrente = document.querySelector(".act-"+id_blocco);
            stato_blocchi[id_blocco-1] = 2;
            blocco_corrente.classList.add("completato");
            blocco_corrente.classList.remove("show");
            blocco_corrente.classList.remove("selezionato");
            blocco_corrente.querySelector(".rows").classList.remove("show");

            //imposta la variabile nascosta
            document.querySelector(".selezione-blocco-"+id_blocco).value = id_attivita;
            //cambia il selezione-titolo del blocco selezionato con il titolo di ciò che è stato selezionato
            console.log(id_attivita);
            // Specifica che la attività è stata selezionata
            //document.querySelector(".attivita-"+id_attivita).classList.toggle
            nome_att = document.querySelector(".attivita-"+id_attivita).querySelector(".activity--name").innerHTML;
            document.querySelector(".act-"+id_blocco+" .selezione-titolo").innerHTML = nome_att;

            if(id_blocco == numero_blocchi) {
                return;
            } else {
                //determina il blocco successivo che deve essere nello stato diverso da 2
                var numero_blocco_successivo = 0;
                for(var i=id_blocco; i<=numero_blocchi; i++) {
                    if(stato_blocchi[i] != 2) {
                        numero_blocco_successivo = i+1;
                        break;
                    }
                }
                if(numero_blocco_successivo > numero_blocchi) {
                    return;
                }
                var blocco_successivo = document.querySelector(".act-"+numero_blocco_successivo);
                blocco_successivo.classList.add("selezionato");
                blocco_successivo.classList.remove("disabled_block_button");
                blocco_successivo.classList.add("show");
                blocco_successivo.querySelector(".rows").classList.add("show");
                stato_blocchi[numero_blocco_successivo] = 1;
            }

            

            //resumeStatus();
        }
    </script>

</head>

<body>
<section class="section">
        <div class="container">
            <h1 class="title"><?php echo $nome;?>,</h1>
            <h2 class="subtitle">Seleziona le attività  a cui vuoi partecipare</h2>
            <form action="submit.php" method="POST">
                <?php
                // Display the activities grouped by hour
                $counter = 0;
                    foreach ($activities as $hour => $group) {
                    $counter += 1;
                    echo "<div class='activity-wrapper act-$counter' >";
                    echo "<h3 class='ora title is-4' onclick='activityClick($counter)'> <span class='selezione-titolo'> Ora $hour </span> <i class='fa-solid fa-chevron-right'></i> </h3>";
                    echo "<div class='rows'>";
                    foreach ($group as $activity) {
                        echo "<div class='activity attivita-".$activity['id']." '>";
                            echo "<h3 class='activity--name'>" . $activity['nome'] . "</h3>";
                            echo "<div class=''>";
                                echo "<p class=' orario'><i class='fa-solid fa-clock'></i> ".$activity['orario']."</p>";
                                echo "<p class=' luogo'><i class='fa-solid fa-location-dot'></i> ".$activity['luogo']."</p>";
                                echo "<p class=' rimanenti'><i class='fa-solid fa-user'></i> " . $activity['posti'] . " posti rimanenti</p>";
                                echo "<p class=' descrizione'>".$activity['descrizione']."</p>";
                            echo "</div>";

                            echo "<input type='button' class='button is-pulled-right' value='seleziona' onclick='selezionaAttivita(".$counter.",".$activity['id'].");' />";
                        echo "</div>";
                    }
                    echo "</div>";
                    echo "<input type='hidden' class='selezione-blocco-".$counter."' name='selezione-blocco-".$counter."' value=''/>";
                    echo "</div>";
                }

                ?>
                <div class="field is-grouped">
                    <div class="control">
                        <input type="submit" value="Invia" class="button is-link">
                    </div>
                </div>
            </form>
        </div>
    </section>
        <script>
            recupera_stato_blocchi();
            recupera_selezione_blocchi();
            resumeStatus();
        </script>
        <?php
    if(isset($_COOKIE['error'])) {
        echo '<div id="notifica">
        <div class="notification is-danger is-light">
            <button class="delete" onClick="document.getElementById(\'notifica\').style.display=\'none\'"></button>
            '.$_COOKIE['error'].'
        </div>
        </div>';
    }
    unset($_COOKIE['error']);
    ?>
</body>
</html>

<?php
$conn->close();
?>