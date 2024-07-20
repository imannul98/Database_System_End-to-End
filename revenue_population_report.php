<?php
include 'config.php';
include 'helper.php';
check_login();

// Get the employee ID from the session
$employee_id = $_SESSION['employee_id'];

// Fetch the accessible reports
$accessible_reports = get_user_reports($employee_id);

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

// Fetch the total number of districts
$total_district_sql = "SELECT COUNT(DISTINCT DistrictNumber) AS total_district_count FROM district";
$total_district_result = $conn->query($total_district_sql);
$total_district_count = $total_district_result->fetch_assoc()['total_district_count'];

// Check if the user has access to all districts
if ($user_district_count < $total_district_count) {
    echo "You do not have access to this report.";
    exit();
}

// Query to fetch revenue by population data
$sql = "
    SELECT
        CASE
            WHEN c.Population < 3700000 THEN 'Small'
            WHEN c.Population BETWEEN 3700000 AND 6699999 THEN 'Medium'
            WHEN c.Population BETWEEN 6700000 AND 8999999 THEN 'Large'
            ELSE 'Extra Large'
        END AS city_size,
        YEAR(se.Date) AS year,
        SUM(se.Quantity * IFNULL(d.DiscountPrice, p.RetailPrice)) AS total_revenue
    FROM city c
    JOIN store s ON c.CityName = s.CityName AND c.State = s.State
    JOIN sell se ON s.StoreNumber = se.StoreNumber
    JOIN product p ON se.PID = p.PID
    LEFT JOIN discount d ON se.PID = d.PID AND se.Date = d.Date
    GROUP BY city_size, year
    ORDER BY FIELD(city_size, 'Small', 'Medium', 'Large', 'Extra Large'), year ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revenue by Population</title>
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
    <h2>Revenue by Population</h2>
    <table>
        <thead>
            <tr>
                <th>City Size</th>
                <th>Year</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['city_size']); ?></td>
                <td><?php echo htmlspecialchars($row['year']); ?></td>
                <td><?php echo htmlspecialchars($row['total_revenue']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Log the report view
log_report_view($employee_id, 'Revenue by Population');
?>