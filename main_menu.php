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

// Fetch the user's first and last name from the database using the employee ID stored in the session
$employee_id = $_SESSION['employee_id'];

// Fetch the accessible reports
$accessible_reports = get_user_reports($employee_id);

$stmt = $conn->prepare("SELECT FirstName, LastName FROM user WHERE EmployeeID = ?");
if ($stmt) {
    $stmt->bind_param("i", $employee_id); // "i" for integer

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();
        $first_name = $user['FirstName'];
        $last_name = $user['LastName'];
    } else {
        // Handle case where user data is not found
        $first_name = "User";
        $last_name = "";
    }

    // Close the statement
    $stmt->close();
} else {
    // Handle errors with the prepared statement
    echo "Error preparing statement: " . $conn->error;
    exit();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main Menu</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<h3>Available Reports</h3>
    <ul>
        <?php foreach ($accessible_reports as $report_name => $report_file): ?>
            <li><a href="<?php echo $report_file; ?>"><?php echo $report_name; ?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php render_logout_button(); ?>
    <h1>Welcome <?php echo htmlspecialchars($first_name) . ' ' . htmlspecialchars($last_name); ?>!</h1>
    <p>Count of Stores: <?php echo $store_count; ?></p>
    <p>Count of Cities: <?php echo $city_count; ?></p>
    <p>Count of Districts: <?php echo $district_count; ?></p>
    <p>Count of Manufacturers: <?php echo $manufacturer_count; ?></p>
    <p>Count of Products: <?php echo $product_count; ?></p>
    <p>Count of Categories: <?php echo $category_count; ?></p>
    <p>Count of Holidays: <?php echo $holiday_count; ?></p>
</body>
</html>


