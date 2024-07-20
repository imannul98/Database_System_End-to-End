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
include 'helper.php';
check_login();

// Fetch the user's access level to determine if they have access to all districts
$employee_id = $_SESSION['employee_id'];

// Fetch the accessible reports
$accessible_reports = get_user_reports($employee_id);

// Determine if the user has access to all districts
$district_count_result = $conn->query("SELECT COUNT(*) AS count FROM district");
$user_district_count_result = $conn->query("SELECT COUNT(*) AS count FROM user_district WHERE EmployeeID = $employee_id");

if ($district_count_result && $user_district_count_result) {
    $district_count = $district_count_result->fetch_assoc()['count'];
    $user_district_count = $user_district_count_result->fetch_assoc()['count'];
    $access_all_districts = ($district_count == $user_district_count);
} else {
    echo "Error fetching district counts: " . $conn->error;
    exit();
}

// Fetch all holidays from the database
$holidays = $conn->query("SELECT * FROM holiday ORDER BY Date ASC")->fetch_all(MYSQLI_ASSOC);

// Handle adding a new holiday
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $access_all_districts) {
    $holiday_date = $_POST['holiday_date'];
    $holiday_name = $_POST['holiday_name'];

    $stmt = $conn->prepare("INSERT INTO holiday (Date, HolidayName) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $holiday_date, $holiday_name);
        $stmt->execute();
        $stmt->close();
        header("Location: view_holidays.php");
        exit();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Holidays</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h3>Available Reports</h3>
    <ul>
        <?php foreach ($accessible_reports as $report_name => $report_file): ?>
            <li><a href="<?php echo $report_file; ?>"><?php echo $report_name; ?></a></li>
        <?php endforeach; ?>
    </ul>
	<h1>Existing Holidays</h1>
    <table>
        <thead>
            <tr>
                <th>Holiday Date</th>
                <th>Holiday Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($holidays as $holiday): ?>
                <tr>
                    <td><?php echo htmlspecialchars($holiday['Date']); ?></td>
                    <td><?php echo htmlspecialchars($holiday['HolidayName']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($access_all_districts): ?>
        <h2>Add a New Holiday</h2>
        <form action="view_holidays.php" method="post">
            <label for="holiday_date">Holiday Date:</label>
            <input type="date" id="holiday_date" name="holiday_date" required><br>
            <label for="holiday_name">Holiday Name:</label>
            <input type="text" id="holiday_name" name="holiday_name" required><br>
            <input type="submit" value="Add Holiday">
        </form>
    <?php endif; ?>
</body>
</html>
