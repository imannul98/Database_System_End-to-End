<?php
include 'config.php';
check_login();

$sql = "
    SELECT
        CASE
            WHEN c.population < 3700000 THEN 'Small'
            WHEN c.population BETWEEN 3700000 AND 6699999 THEN 'Medium'
            WHEN c.population BETWEEN 6700000 AND 8999999 THEN 'Large'
            ELSE 'Extra Large'
        END AS city_size,
        YEAR(se.sale_date) AS year,
        SUM(se.quantity * IFNULL(d.discount_price, p.retail_price)) AS total_revenue
    FROM city c
    JOIN store s ON c.city_id = s.city_id
    JOIN sell se ON s.store_id = se.store_id
    JOIN product p ON se.product_id = p.product_id
    LEFT JOIN discount d ON se.product_id = d.product_id AND se.sale_date = d.discount_date
    GROUP BY city_size, year
    ORDER BY city_size ASC, year ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revenue by Population</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h2>Revenue by Population</h2>
    <table>
        <thead>
            <tr>
                <th>City Size</th>
                <th>Year</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['city_size']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['total_revenue']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
