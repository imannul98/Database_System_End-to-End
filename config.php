<?php
$servername = "localhost";
$username = "nicoledb";
$password = "gatech234";
$dbname = "cs6400_su24_team038";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Make the connection global
$GLOBALS['conn'] = $conn;

?>

