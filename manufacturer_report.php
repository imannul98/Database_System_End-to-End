<?php
include 'config.php';
include 'navbar.php';
include 'helper.php';
check_login();

// Query to fetch manufacturer report data
$sql = "
    SELECT m.ManufacturerName,
           COUNT(p.PID) AS total_products,
           AVG(p.RetailPrice) AS avg_price,
           MIN(p.RetailPrice) AS min_price,
           MAX(p.RetailPrice) AS max_price
    FROM manufacturer m
    JOIN product p ON m.ManufacturerName = p.ManufacturerName
    GROUP BY m.ManufacturerName
    ORDER BY avg_price DESC
    LIMIT 100";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manufacturer’s Product Report</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <?php render_navbar(); ?>
    <h2>Manufacturer’s Product Report</h2>
    <table>
        <thead>
            <tr>
                <th>Manufacturer Name</th>
                <th>Total Products</th>
                <th>Average Price</th>
                <th>Minimum Price</th>
                <th>Maximum Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['ManufacturerName']; ?></td>
                <td><?php echo $row['total_products']; ?></td>
                <td><?php echo $row['avg_price']; ?></td>
                <td><?php echo $row['min_price']; ?></td>
                <td><?php echo $row['max_price']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
