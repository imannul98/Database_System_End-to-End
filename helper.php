<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function check_login() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: login.php");
        exit();
    }
}

function get_user_reports($employee_id) {
    global $conn;

    // Fetch the total number of districts
    $total_district_sql = "SELECT COUNT(DISTINCT DistrictNumber) AS total_district_count FROM district";
    $total_district_result = $conn->query($total_district_sql);
    $total_district_count = $total_district_result->fetch_assoc()['total_district_count'];

    // Fetch the number of districts the user has access to
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

    // Check if the user has access to all districts
    $access_all_districts = ($user_district_count == $total_district_count);

    // Fetch the AuditViewFlag for the user
    $audit_flag_sql = "
        SELECT AuditViewFlag
        FROM user
        WHERE EmployeeID = ?";
    $audit_flag_stmt = $conn->prepare($audit_flag_sql);
    $audit_flag_stmt->bind_param("i", $employee_id);
    $audit_flag_stmt->execute();
    $audit_flag_result = $audit_flag_stmt->get_result();
    $audit_view_flag = $audit_flag_result->fetch_assoc()['AuditViewFlag'] == 1;
    $audit_flag_stmt->close();

    $reports = [
        "View Holidays" => "view_holidays.php",
        "Manufacturer's Product Report" => "manufacturer_report.php",
        "Category Report" => "category_report.php",
        "Actual versus Predicted Revenue for GPS units" => "gps_revenue_report.php",
        "Air Conditioners on Groundhog Day" => "ac_groundhog_report.php",
    ];

    // Additional reports based on district access
    if ($access_all_districts) {
        $reports += [
            "Store Revenue by Year by State" => "store_revenue_report.php",
            "District with Highest Volume for each Category" => "district_volume_report.php",
            "Revenue by Population" => "revenue_population_report.php"
        ];
    }

    // Show audit report if the user has the audit view flag
    if ($audit_view_flag) {
        $reports["Audit Report"] = "view_audit_log.php";
    }

    return $reports;
}

function render_logout_button() {
    echo '<a href="logout.php" style="color: red; font-weight: bold;">Logout</a>';
}

function log_report_view($employee_id, $report_name) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO auditreport (TimeStamp, EmployeeID, ReportName) VALUES (CURRENT_TIMESTAMP, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("is", $employee_id, $report_name);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
}
?>
