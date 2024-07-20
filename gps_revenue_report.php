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

// Query to fetch GPS revenue report data
$sql = "
    SELECT p.PID,
           p.ProductName,
           p.RetailPrice,
           SUM(s.Quantity) AS total_units_sold,
           SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity ELSE 0 END) AS units_sold_at_discount,
           SUM(CASE WHEN d.DiscountPrice IS NULL THEN s.Quantity ELSE 0 END) AS units_sold_at_retail,
           SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity * d.DiscountPrice ELSE s.Quantity * p.RetailPrice END) AS actual_revenue,
           SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity * p.RetailPrice * 0.75 ELSE s.Quantity * p.RetailPrice END) AS predicted_revenue,
           (SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity * d.DiscountPrice ELSE s.Quantity * p.RetailPrice END) - 
           SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity * p.RetailPrice * 0.75 ELSE s.Quantity * p.RetailPrice END)) AS revenue_difference
    FROM product p
    JOIN sell s ON p.PID = s.PID
    LEFT JOIN discount d ON p.PID = d.PID AND s.Date = d.Date
    JOIN product_category pc ON p.PID = pc.PID
    JOIN category c ON pc.CategoryName = c.CategoryName
    JOIN store st ON s.StoreNumber = st.StoreNumber
    WHERE c.CategoryName = 'GPS'
    AND st.DistrictNumber IN ($district_numbers)
    GROUP BY p.PID, p.ProductName, p.RetailPrice
    HAVING ABS(revenue_difference) > 200
    ORDER BY revenue_difference DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Actual vs Predicted Revenue for GPS Units</title>
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
    <h2>Actual vs Predicted Revenue for GPS Units</h2>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Retail Price</th>
                <th>Total Units Sold</th>
                <th>Units Sold at Discount</th>
                <th>Units Sold at Retail</th>
                <th>Actual Revenue</th>
                <th>Predicted Revenue</th>
                <th>Revenue Difference</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['PID']); ?></td>
                <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                <td><?php echo htmlspecialchars($row['RetailPrice']); ?></td>
                <td><?php echo htmlspecialchars($row['total_units_sold']); ?></td>
                <td><?php echo htmlspecialchars($row['units_sold_at_discount']); ?></td>
                <td><?php echo htmlspecialchars($row['units_sold_at_retail']); ?></td>
                <td><?php echo htmlspecialchars($row['actual_revenue']); ?></td>
                <td><?php echo htmlspecialchars($row['predicted_revenue']); ?></td>
                <td><?php echo htmlspecialchars($row['revenue_difference']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Log the report view
log_report_view($employee_id, 'Actual versus Predicted Revenue for GPS units');
?>