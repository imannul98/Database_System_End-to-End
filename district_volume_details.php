<?php
include 'config.php';
include 'helper.php';
check_login();

// Get the employee ID from the session
$employee_id = $_SESSION['employee_id'];

$year = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';
$category = $_GET['category'] ?? '';
$district = $_GET['district'] ?? '';

// Fetch the store details for the selected district, category, year, and month
$sql = "
    SELECT 
        distinct(s.StoreNumber),
        s.CityName,
        s.State
    FROM store s
    JOIN sell se ON s.StoreNumber = se.StoreNumber
    JOIN product p ON se.PID = p.PID
    JOIN product_category pc ON p.PID = pc.PID
    JOIN category c ON pc.CategoryName = c.CategoryName
    WHERE YEAR(se.Date) = ? AND MONTH(se.Date) = ? AND c.CategoryName = ? AND s.DistrictNumber = ?
    ORDER BY s.StoreNumber ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi", $year, $month, $category, $district);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Details for <?php echo htmlspecialchars($category); ?> in District <?php echo htmlspecialchars($district); ?> for <?php echo htmlspecialchars($year); ?>/<?php echo htmlspecialchars($month); ?></title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<h3>Available Reports</h3>
    <ul>
        <?php foreach (get_user_reports($employee_id) as $report_name => $report_file): ?>
            <li><a href="<?php echo $report_file; ?>"><?php echo $report_name; ?></a></li>
        <?php endforeach; ?>
    </ul>
	<?php render_logout_button(); ?>
    <h2>Details for <?php echo htmlspecialchars($category); ?> in District <?php echo htmlspecialchars($district); ?> for <?php echo htmlspecialchars($year); ?>/<?php echo htmlspecialchars($month); ?></h2>
    <table>
        <thead>
            <tr>
                <th>Store Number</th>
                <th>City</th>
                <th>State</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['StoreNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['CityName']); ?></td>
                <td><?php echo htmlspecialchars($row['State']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

