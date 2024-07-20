<?php
include 'config.php';
include 'helper.php';

check_login();

// Get the employee ID from the session
$employee_id = $_SESSION['employee_id'];

// Fetch the accessible reports
$accessible_reports = get_user_reports($employee_id);

// Fetch the districts the user has access to
$district_access_sql = "
    SELECT DistrictNumber
    FROM user_district
    WHERE EmployeeID = ?";
$district_stmt = $conn->prepare($district_access_sql);
$district_stmt->bind_param("i", $employee_id);
$district_stmt->execute();
$district_result = $district_stmt->get_result();

$districts = [];
while ($row = $district_result->fetch_assoc()) {
    $districts[] = $row['DistrictNumber'];
}

$district_stmt->close();

if (empty($districts)) {
    echo "You do not have access to any districts.";
    exit();
}

$district_numbers = implode(',', $districts);

// Query to fetch air conditioners on Groundhog Day report data
$sql = "
    SELECT 
        YEAR(s.Date) AS year,
        SUM(s.Quantity) AS total_units_sold,
        SUM(s.Quantity) / 365 AS avg_units_sold_per_day,
        SUM(CASE WHEN DATE_FORMAT(s.Date, '%m-%d') = '02-02' THEN s.Quantity ELSE 0 END) AS units_sold_on_groundhog_day
    FROM sell s
    JOIN product_category pc ON s.PID = pc.PID
    JOIN category c ON pc.CategoryName = c.CategoryName
    JOIN store st ON s.StoreNumber = st.StoreNumber
    WHERE c.CategoryName = 'Air Conditioning'
    AND st.DistrictNumber IN ($district_numbers)
    GROUP BY YEAR(s.Date)
    ORDER BY year ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Air Conditioners on Groundhog Day</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<?php render_main_menu_button(); ?>
<h3>Available Reports</h3>
    <ul>
        <?php foreach ($accessible_reports as $report_name => $report_file): ?>
            <li><a href="<?php echo $report_file; ?>"><?php echo $report_name; ?></a></li>
        <?php endforeach; ?>
    </ul>
	<?php render_logout_button(); ?>
    <h2>Air Conditioners on Groundhog Day</h2>
    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th>Total Units Sold</th>
                <th>Average Units Sold per Day</th>
                <th>Units Sold on Groundhog Day</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['year']); ?></td>
                <td><?php echo htmlspecialchars($row['total_units_sold']); ?></td>
                <td><?php echo htmlspecialchars($row['avg_units_sold_per_day']); ?></td>
                <td><?php echo htmlspecialchars($row['units_sold_on_groundhog_day']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Log the report view
log_report_view($employee_id, 'Air Conditioners on Groundhog Day?');
?>