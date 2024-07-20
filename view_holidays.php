<?php
include 'config.php';
include 'helper.php';
check_login();

// Get the employee ID from the session
$employee_id = $_SESSION['employee_id'];

// Fetch the accessible reports
$accessible_reports = get_user_reports($employee_id);

// Query to fetch manufacturer report data
$sql = "
    SELECT h.Date, h.HolidayName
    FROM holiday h";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manufacturer’s Product Report</title>
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
	<h2>Manufacturer’s Product Report</h2>
    <table>
        <thead>
            <tr>
                <th>Holiday Date</th>
                <th>Holiday Name</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['Date']; ?></td>
                <td><?php echo $row['HolidayName']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Log the report view
log_report_view($employee_id, "Manufacturer's Product Report");
?>