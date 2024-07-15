<?php
include 'config.php';
include 'navbar.php';
include 'helper.php';
check_login();

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
    WHERE c.CategoryName = 'Air Conditioning'
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
    <?php render_navbar(); ?>
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
