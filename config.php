<?php
$servername = "localhost";
$username = "nicoledb";
$password = "gatech234";
$dbname = "BuzzBuy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check if user is logged in
function check_login() {
    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
        exit();
    }
}
?>