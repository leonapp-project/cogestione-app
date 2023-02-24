<?php
$servername = "localhost";
$username = "leonapp";
$password = "";
$dbname = "leonapp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>