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

// Fetch unique states
$states_result = $conn->query("SELECT DISTINCT State FROM city ORDER BY State");

$state = $_GET['state'] ?? '';

$sql = "
    SELECT s.StoreNumber,
           s.PhoneNumber,
           c.CityName,
           YEAR(se.Date) AS year,
           SUM(se.Quantity * IFNULL(d.DiscountPrice, p.RetailPrice)) AS total_revenue
    FROM store s
    JOIN city c ON s.CityName = c.CityName AND s.State = c.State
    JOIN sell se ON s.StoreNumber = se.StoreNumber
    JOIN product p ON se.PID = p.PID
    LEFT JOIN discount d ON se.PID = d.PID AND se.Date = d.Date
    WHERE c.State = '$state'
    GROUP BY s.StoreNumber, s.PhoneNumber, c.CityName, YEAR(se.Date)
    ORDER BY year ASC, total_revenue DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Store Revenue by Year by State</title>
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
    <h2>Store Revenue by Year by State</h2>
    <form method="get">
        <label for="state">State:</label>
        <select id="state" name="state">
            <?php while ($state_row = $states_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($state_row['State']); ?>" <?php if ($state_row['State'] == $state) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($state_row['State']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Submit</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Store Number</th>
                <th>Phone Number</th>
                <th>City Name</th>
                <th>Year</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['StoreNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['PhoneNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['CityName']); ?></td>
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
log_report_view($employee_id, 'Store Revenue by Year by State');
?>