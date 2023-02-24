<?php
require_once 'vendor/autoload.php';

// Get $id_token via HTTPS POST.
$CLIENT_ID = "626536855617-es6i5nct122k3tbci1qghpfj21k5mavf.apps.googleusercontent.com";

$client = new Google_Client(['client_id' => $CLIENT_ID]); // Specify the CLIENT_ID of the app that accesses the backend
$id_token=$_POST["credential"];

if (!$id_token) {
    //echo "no token given";
    header('Location: index.php');
    exit();
}

try {
    $payload = $client->verifyIdToken($id_token);
} catch (\Google\Auth\Exception\InvalidArgumentException $e) {
    //echo "Invalid token";
    header('Location: index.php');
    exit();
}
if ($payload) {
    if (substr($payload['email'], -27) === '@studentilicei.leonexiii.it') {
        // continue
    } else {
        //echo "Invalid email";
        header('Location: index.php');
        setcookie("error", "Non &egrave; stato possibile accedere: bisogna essere studenti del Liceo per poter eseguire questa operazione. Se si tratta di un errore, controlla di aver utilizzato il giusto account scolastico.", time() + 120, "/"); // 1 giorno
        exit();
    }
} else {
    //echo "Invalid token";
    header('Location: index.php');
    exit();
}
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
$sessionid = generateRandomString(32);


setcookie("sessionid", $sessionid, time() + (86400 * 30), "/"); // 1 giorno

// load credentials and connect to database $conn
require 'mysqli.php';

// Prepare select statement to check if email already exists
$select_stmt = $conn->prepare("SELECT * FROM utenti WHERE mail = ?");
$select_stmt->bind_param("s", $payload['email']);
$select_stmt->execute();
$result = $select_stmt->get_result();
$user = $result->fetch_assoc(); // fetch data   

// Check if email already exists in the table
if ($result->num_rows > 0) {
    // Update sessionid
    //$update_stmt = $conn->prepare("UPDATE utenti SET sessionid = ? WHERE mail = ?");
    $update_stmt = $conn->prepare("UPDATE utenti SET sessionid=?, ultimo_accesso=NOW() WHERE mail=?");
    $update_stmt->bind_param("ss", $sessionid, $payload['email']);
    $update_stmt->execute();
    //echo "updates record  successfully";

} else {
    // Insert new account
    $insert_stmt = $conn->prepare("INSERT INTO utenti (mail, nome, cognome, sessionid) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("ssss", $payload['email'], $payload['given_name'], $payload['family_name'], $sessionid);
    $insert_stmt->execute();
    //echo "New record created successfully";

}
// Close connection
$conn->close();
header("Location: completa.php");

?>