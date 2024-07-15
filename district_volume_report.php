<?php
include 'config.php';
include 'navbar.php';
include 'helper.php';
check_login();

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
    <?php render_navbar(); ?>
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
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['CategoryName']); ?></td>
                <td><?php echo htmlspecialchars($row['DistrictNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['total_units_sold']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
