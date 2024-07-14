<?php
include 'config.php';
check_login();

// Fetch counts from database
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
    <h1>Welcome, <?php echo $_SESSION['employee_id']; ?>!</h1>
    <p>Stores: <?php echo $store_count; ?></p>
    <p>Cities: <?php echo $city_count; ?></p>
    <p>Districts: <?php echo $district_count; ?></p>
    <p>Manufacturers: <?php echo $manufacturer_count; ?></p>
    <p>Products: <?php echo $product_count; ?></p>
    <p>Categories: <?php echo $category_count; ?></p>
    <p>Holidays: <?php echo $holiday_count; ?></p>
    <h2>Available Reports</h2>
    <ul>
        <li><a href="manufacturer_report.php">Manufacturer’s Product Report</a></li>
        <li><a href="category_report.php">Category Report</a></li>
        <li><a href="gps_revenue_report.php">Actual vs Predicted Revenue for GPS Units</a></li>
        <li><a href="ac_groundhog_report.php">Air Conditioners on Groundhog Day</a></li>
        <li><a href="store_revenue_report.php">Store Revenue by Year by State</a></li>
        <li><a href="district_volume_report.php">District with Highest Volume for Each Category</a></li>
        <li><a href="revenue_population_report.php">Revenue by Population</a></li>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>
