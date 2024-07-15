<?php
session_start();

function check_login() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: login.php");
        exit();
    }
}

function log_audit($report_name, $conn) {
    $employee_id = $_SESSION['employee_id'];
    $timestamp = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO auditreport (TimeStamp, EmployeeID, ReportName) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sis", $timestamp, $employee_id, $report_name);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>
