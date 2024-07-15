<?php
include 'config.php';
include 'navbar.php';
include 'helper.php';
check_login();

check_login();

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
    <?php render_navbar(); ?>
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
