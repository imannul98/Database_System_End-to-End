<?php

// written by GTusername1

session_start();
if (empty($_SESSION['employee_id']) ){
    header("Location: login.php");
    die();
}else{
    header("Location: main_menu.php");
    die();
}
?>