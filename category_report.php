<?php
include 'config.php';
include 'helper.php';
check_login();

// Get the employee ID from the session
$employee_id = $_SESSION['employee_id'];

// Fetch the accessible reports
$accessible_reports = get_user_reports($employee_id);

// Query to fetch category report data
$sql = "
    SELECT c.CategoryName,
           COUNT(pc.PID) AS total_products,
           COUNT(DISTINCT p.ManufacturerName) AS total_manufacturers,
           AVG(p.RetailPrice) AS avg_price
    FROM category c
    JOIN product_category pc ON c.CategoryName = pc.CategoryName
    JOIN product p ON pc.PID = p.PID
    GROUP BY c.CategoryName
    ORDER BY c.CategoryName ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Category Report</title>
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
    <h2>Category Report</h2>
    <table>
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Total Products</th>
                <th>Total Manufacturers</th>
                <th>Average Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['CategoryName']; ?></td>
                <td><?php echo $row['total_products']; ?></td>
                <td><?php echo $row['total_manufacturers']; ?></td>
                <td><?php echo $row['avg_price']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Log the report view
log_report_view($employee_id, "Category Report");
?>