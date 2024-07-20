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

// Fetch unique years and months from the sell table
$years_result = $conn->query("SELECT DISTINCT YEAR(Date) AS year FROM sell ORDER BY year");
$months_result = $conn->query("SELECT DISTINCT MONTH(Date) AS month FROM sell ORDER BY month");

$year = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';

// Subquery to find the maximum total units sold for each category
$subquery = "
    SELECT 
        c.CategoryName,
        d.DistrictNumber,
        SUM(se.Quantity) AS total_units_sold
    FROM category c
    JOIN product_category pc ON c.CategoryName = pc.CategoryName
    JOIN product p ON pc.PID = p.PID
    JOIN sell se ON p.PID = se.PID
    JOIN store s ON se.StoreNumber = s.StoreNumber
    JOIN district d ON s.DistrictNumber = d.DistrictNumber
    WHERE YEAR(se.Date) = '$year' AND MONTH(se.Date) = '$month'
    GROUP BY c.CategoryName, d.DistrictNumber
";

// Main query to select the district with the highest volume for each category
$sql = "
    SELECT 
        subquery.CategoryName,
        subquery.DistrictNumber,
        subquery.total_units_sold
    FROM (
        $subquery
    ) AS subquery
    JOIN (
        SELECT 
            CategoryName,
            MAX(total_units_sold) AS max_units_sold
        FROM (
            $subquery
        ) AS inner_subquery
        GROUP BY CategoryName
    ) AS max_subquery ON subquery.CategoryName = max_subquery.CategoryName AND subquery.total_units_sold = max_subquery.max_units_sold
    ORDER BY subquery.CategoryName ASC
";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>District with Highest Volume for Each Category</title>
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
    <h2>District with Highest Volume for Each Category</h2>
    <form method="get">
        <label for="year">Year:</label>
        <select id="year" name="year">
            <option value="">Select Year</option>
            <?php while ($year_row = $years_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($year_row['year']); ?>" <?php if ($year_row['year'] == $year) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($year_row['year']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="month">Month:</label>
        <select id="month" name="month">
            <option value="">Select Month</option>
            <?php while ($month_row = $months_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($month_row['month']); ?>" <?php if ($month_row['month'] == $month) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($month_row['month']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Submit</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Category Name</th>
                <th>District Number</th>
                <th>Total Units Sold</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['CategoryName']); ?></td>
                <td><?php echo htmlspecialchars($row['DistrictNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['total_units_sold']); ?></td>
                <td><a href="district_volume_details.php?year=<?php echo urlencode($year); ?>&month=<?php echo urlencode($month); ?>&category=<?php echo urlencode($row['CategoryName']); ?>&district=<?php echo urlencode($row['DistrictNumber']); ?>">View Details</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Log the report view
log_report_view($employee_id, 'District with Highest Volume for each Category');
?>
