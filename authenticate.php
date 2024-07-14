<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_POST['employee_id'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE employee_id = '$employee_id' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        $_SESSION['employee_id'] = $employee_id;
        header("Location: main_menu.php");
    } else {
        echo "Invalid login credentials.";
    }
}
?>