<?php

$anno = $_POST['year'];
$plesso = $_POST['degree'];
$sezione = $_POST['section'];

// load credentials and connect to database $conn
require 'mysqli.php';

$session_id = $_COOKIE['sessionid'];

// Prepare select statement to check if email already exists
$select_stmt = $conn->prepare("SELECT * FROM utenti WHERE sessionid = ?");
$select_stmt->bind_param("s", $session_id);
$select_stmt->execute();
$result = $select_stmt->get_result();
$user = $result->fetch_assoc();
$nome = $user['nome'];
if(isset($user['classe']) && isset($user['sezione']) && isset($user['indirizzo'])) {
    echo "set";
    header('Location: selezione.php');
    exit();
}
// Check if email already exists in the table
if ($result->num_rows > 0) {
    echo $_POST['degree'];
    echo $_POST['year'];
    echo $_POST['section'];
    // Update sessionid
    //$update_stmt = $conn->prepare("UPDATE utenti SET sessionid = ? WHERE mail = ?");
    if (isset($_POST['degree']) && isset($_POST['year']) && isset($_POST['section'])) {
        $update_stmt = $conn->prepare("UPDATE utenti SET classe=?, sezione=?, indirizzo=? WHERE sessionid=?");
        $update_stmt->bind_param("ssss", $anno, $sezione, strtoupper($plesso), $session_id);
        $update_stmt->execute();
        header('Location: selezione.php');
    }
} else {
    //echo "Invalid token";
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html> 
<html>
<head>
    <link rel="icon" type="image/x-icon" href="./src/img/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <title>Ciao 
<?php 
echo $nome;
?> 
- Cogestione 2023</title>

    <script src="https://kit.fontawesome.com/b188873eef.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="./src/css/completa.css">
    <link rel="stylesheet" href="./src/css/button.css">

    <script>
        function showSection() {
            var op = document.getElementById("selezione").getElementsByTagName("option");
            for (var i = 0; i < op.length; i++) {
            // lowercase comparison for case-insensitivity
            if(document.getElementsByName("degree")[0].value == "Scientifico") {
                op[1].disabled = false;
                op[2].disabled = false;
                document.getElementById("section-field").style.display = "inline-block";
                document.getElementById("section-field").style.visibility = "visible";
                console.log("mostra scientifico")

            } else {
                op[0].selected = true;
                document.getElementById("section-field").style.display = "none";
                document.getElementById("section-field").style.visibility = "hidden";
                op[0].disabled = false;
                op[1].disabled = true;
                op[2].disabled = true;
            }
            }
        }
    </script>
</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="title">Ciao 
<?php
echo $nome;
?>👋
            </h1>
            <h2 class="subtitle">Specifica la tua classe</h2>
            <form method="post" action="completa.php">
                <div class="field inline-div">
                    <label class="label">Anno</label>
                    <div class="contro">
                        <div class="select">
                            <select id="classe" name="year">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field inline-div">
                    <label class="label">Plesso</label>
                    <div class="control">
                        <div class="select">
                            <select name="degree" onChange="showSection();">
                                <option>Classico</option>
                                <option>Scientifico</option>
                                <option>Sportivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="field inline-div" id="section-field" style="visibility: hidden;">
                    <label class="label">Sezione</label>
                    <div class="control">
                        <div class="select">
                            <select id ="selezione" name="section">
                                <option>A</option>
                                <option>B</option>
                                <option>C</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button class="button is-link" type="submit">Continua <i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</body>
</html>