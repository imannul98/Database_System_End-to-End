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

// Fetch the user's audit log flag to determine if they can view the audit log
$employee_id = $_SESSION['employee_id'];

// Fetch the accessible reports
$accessible_reports = get_user_reports($employee_id);

$stmt = $conn->prepare("SELECT AuditViewFlag FROM user WHERE EmployeeID = ?");
if ($stmt) {
    $stmt->bind_param("i", $employee_id); // "i" for integer
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $audit_view_flag = $user['AuditViewFlag'];
    } else {
        $audit_view_flag = 0;
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

// Redirect if the user does not have the audit view flag
if (!$audit_view_flag) {
    header("Location: main_menu.php");
    exit();
}

// Function to check if a user has access to all districts
function has_access_all_districts($employee_id, $conn) {
    $district_access_sql = "
        SELECT COUNT(DISTINCT DistrictNumber) AS user_district_count
        FROM user_district
        WHERE EmployeeID = ?";
    $district_stmt = $conn->prepare($district_access_sql);
    $district_stmt->bind_param("i", $employee_id);
    $district_stmt->execute();
    $district_result = $district_stmt->get_result();
    $user_district_count = $district_result->fetch_assoc()['user_district_count'];
    $district_stmt->close();

    $total_district_sql = "SELECT COUNT(DISTINCT DistrictNumber) AS total_district_count FROM district";
    $total_district_result = $conn->query($total_district_sql);
    $total_district_count = $total_district_result->fetch_assoc()['total_district_count'];

    return ($user_district_count == $total_district_count);
}

// Fetch the most recent 100 audit log records
$audit_logs = $conn->query("
    SELECT ar.TimeStamp, ar.EmployeeID, ar.ReportName, u.FirstName, u.LastName
    FROM auditreport ar
    JOIN user u ON ar.EmployeeID = u.EmployeeID
    ORDER BY ar.TimeStamp DESC, ar.EmployeeID ASC
    LIMIT 100
")->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Audit Log</title>
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
    <h1>Audit Log</h1>
    <table>
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Report Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($audit_logs as $log): ?>
                <?php
                $log_employee_id = $log['EmployeeID'];
                $access_all_districts = has_access_all_districts($log_employee_id, $conn);
                ?>
                <tr<?php echo ($access_all_districts) ? ' style="background-color: yellow;"' : ''; ?>>
                    <td><?php echo htmlspecialchars($log['TimeStamp']); ?></td>
                    <td><?php echo htmlspecialchars($log['EmployeeID']); ?></td>
                    <td><?php echo htmlspecialchars($log['LastName'] . ', ' . $log['FirstName']); ?></td>
                    <td><?php echo htmlspecialchars($log['ReportName']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
<?php
$conn->close();
?>
