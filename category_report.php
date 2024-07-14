<?php
include 'config.php';
check_login();

// Query to fetch category report data
$sql = "
    SELECT c.category_name,
           COUNT(pc.product_id) AS total_products,
           COUNT(DISTINCT p.manufacturer_id) AS total_manufacturers,
           AVG(p.retail_price) AS avg_price
    FROM category c
    JOIN product_category pc ON c.category_id = pc.category_id
    JOIN product p ON pc.product_id = p.product_id
    GROUP BY c.category_name
    ORDER BY c.category_name ASC";

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
                <td><?php echo $row['category_name']; ?></td>
                <td><?php echo $row['total_products']; ?></td>
                <td><?php echo $row['total_manufacturers']; ?></td>
                <td><?php echo $row['avg_price']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
