<?php
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

?>

<!DOCTYPE html>
<html>

<head>
    <title>Cogestione 2023</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <script src="https://kit.fontawesome.com/b188873eef.js" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./src/css/selezione.css">
    <link rel="stylesheet" href="./src/css/button.css">

    <style>
        .big-div {
            width: 95%;
            margin: 20px auto;
            border-radius: 20px;
            box-shadow: 10px 10px 20px 0px rgba(0, 0, 0, 0.75);
        }

        .big-div:hover {
            background-color: #ADD8E6;
        }
        .disabled_block_button {
            pointer-events: none;
            opacity: 0.8;
        }
        .disabled_block_button h3 {
        }
        .blocco {
            cursor: pointer;
            max-width: 100%;
            margin-left: 1rem;
            margin-right: 1em;
            margin-bottom: 2em;
            border-radius: 10px;
            border: 1px solid black;
        }
        .blocco h3 {
            margin: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            
            font-size: 1.5rem;
        }
        .blocco.selezionato {
            opacity: 1;
            background-color: #F0F8FF;
            pointer-events: all;
        }
        .blocco.completato {
            opacity: 1;
            background-color: green;
            pointer-events: all;
        }
        .contenuti {
            display: none;
        }
        .show {
            display: block;
        }
        .show i {
            transform: rotate(90deg);
        }


    </style>

    <script>
        const numero_blocchi = <?php echo $many_blocks; ?>;
        const numero_attivita = <?php echo $many_activities; ?>;
        selezione_corrente = 2;

        function disableButtons() {
            for(var i=1; i<=numero_attivita; i++) {
                //document.querySelector(".act-"+i).style.pointerEvents = "none";
                document.querySelector(".act-"+i).classList.add("disabled_block_button");
            }
        }

        function resumeStatus() {
            disableButtons();
            for(var i=1; i<selezione_corrente; i++) {
                document.querySelector(".act-"+i).classList.add("completato");
            }
            document.querySelector(".act-"+selezione_corrente).classList.add("selezionato");
        }

        function bloccoClick(id) {
            const wrapper = document.querySelector(".act-"+id);
            const rows = wrapper.querySelector(".contenuti");
            const arrow = wrapper.querySelector("i");
            wrapper.classList.toggle("show");
            rows.classList.toggle("show");
                
        }
    </script>

</head>

<body>
<?php
// Display the activities grouped by hour
$counter = 0;
    foreach ($activities as $hour => $group) {
    $counter += 1;
    echo "<div class='act-$counter blocco' >";
    echo "<h3 onClick='bloccoClick($counter);'>Blocco $hour<i class='fa-solid fa-chevron-right'></i></h3>";
    echo "<div class='contenuti'>";
    foreach ($group as $activity) {
        echo "<h3 class=' is-3'>".$activity['nome']."</h3>";
        echo "<p class='subtitle is-5'>".$activity['descrizione']."</p>";
    }
    echo "</div>";
    //echo "attivita $counter";
    echo "</div>";
    }

?>
        <script>
            resumeStatus();
        </script>
</body>

</html>

<?php
$conn->close();
?>