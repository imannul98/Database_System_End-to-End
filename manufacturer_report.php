<?php
include 'config.php';
check_login();

// Query to fetch manufacturer report data
$sql = "
    SELECT m.manufacturer_name,
           COUNT(p.product_id) AS total_products,
           AVG(p.retail_price) AS avg_price,
           MIN(p.retail_price) AS min_price,
           MAX(p.retail_price) AS max_price
    FROM manufacturer m
    JOIN product p ON m.manufacturer_id = p.manufacturer_id
    GROUP BY m.manufacturer_name
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
                <td><?php echo $row['manufacturer_name']; ?></td>
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
