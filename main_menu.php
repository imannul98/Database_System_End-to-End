<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: authenticate.php");
    exit();
}

include 'config.php';
include 'navbar.php';
include 'helper.php';
check_login();

// Fetch counts from the database
$store_count = $conn->query("SELECT COUNT(*) AS count FROM store")->fetch_assoc()['count'];
$city_count = $conn->query("SELECT COUNT(*) AS count FROM city")->fetch_assoc()['count'];
$district_count = $conn->query("SELECT COUNT(*) AS count FROM district")->fetch_assoc()['count'];
$manufacturer_count = $conn->query("SELECT COUNT(*) AS count FROM manufacturer")->fetch_assoc()['count'];
$product_count = $conn->query("SELECT COUNT(*) AS count FROM product")->fetch_assoc()['count'];
$category_count = $conn->query("SELECT COUNT(*) AS count FROM category")->fetch_assoc()['count'];
$holiday_count = $conn->query("SELECT COUNT(*) AS count FROM holiday")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main Menu</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <?php render_navbar(); ?>
    <h1>Welcome, Employee ID: <?php echo htmlspecialchars($_SESSION['employee_id']); ?>!</h1>
    <p>Stores: <?php echo $store_count; ?></p>
    <p>Cities: <?php echo $city_count; ?></p>
    <p>Districts: <?php echo $district_count; ?></p>
    <p>Manufacturers: <?php echo $manufacturer_count; ?></p>
    <p>Products: <?php echo $product_count; ?></p>
    <p>Categories: <?php echo $category_count; ?></p>
    <p>Holidays: <?php echo $holiday_count; ?></p>
</body>
</html>

