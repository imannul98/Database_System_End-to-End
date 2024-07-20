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
    SELECT p.ManufacturerName,
           COUNT(DISTINCT p.PID) AS total_products,
           AVG(p.RetailPrice) AS avg_price,
           MIN(p.RetailPrice) AS min_price,
           MAX(p.RetailPrice) AS max_price
    FROM product p
    GROUP BY p.ManufacturerName
    ORDER BY avg_price DESC
    LIMIT 100";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manufacturer’s Product Report</title>
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
    <h2>Manufacturer’s Product Report</h2>
    <table>
        <thead>
            <tr>
                <th>Manufacturer Name</th>
                <th>Total Products</th>
                <th>Average Price</th>
                <th>Minimum Price</th>
                <th>Maximum Price</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['ManufacturerName']); ?></td>
                <td><?php echo htmlspecialchars($row['total_products']); ?></td>
                <td><?php echo htmlspecialchars($row['avg_price']); ?></td>
                <td><?php echo htmlspecialchars($row['min_price']); ?></td>
                <td><?php echo htmlspecialchars($row['max_price']); ?></td>
                <td><a href="manufacturer_detail.php?manufacturer=<?php echo urlencode($row['ManufacturerName']); ?>">View Details</a></td>
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
