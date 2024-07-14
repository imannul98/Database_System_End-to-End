<?php
include 'config.php';
check_login();

// Query to fetch GPS revenue report data
$sql = "
    SELECT p.product_id,
           p.product_name,
           p.retail_price,
           SUM(s.quantity) AS total_units_sold,
           SUM(CASE WHEN d.discount_price IS NOT NULL THEN s.quantity ELSE 0 END) AS units_sold_at_discount,
           SUM(CASE WHEN d.discount_price IS NULL THEN s.quantity ELSE 0 END) AS units_sold_at_retail,
           SUM(CASE WHEN d.discount_price IS NOT NULL THEN s.quantity * d.discount_price ELSE s.quantity * p.retail_price END) AS actual_revenue,
           SUM(CASE WHEN d.discount_price IS NOT NULL THEN s.quantity * p.retail_price * 0.75 ELSE s.quantity * p.retail_price END) AS predicted_revenue,
           (SUM(CASE WHEN d.discount_price IS NOT NULL THEN s.quantity * d.discount_price ELSE s.quantity * p.retail_price END) - 
           SUM(CASE WHEN d.discount_price IS NOT NULL THEN s.quantity * p.retail_price * 0.75 ELSE s.quantity * p.retail_price END)) AS revenue_difference
    FROM product p
    JOIN sell s ON p.product_id = s.product_id
    LEFT JOIN discount d ON p.product_id = d.product_id AND s.sale_date = d.discount_date
    JOIN product_category pc ON p.product_id = pc.product_id
    JOIN category c ON pc.category_id = c.category_id
    WHERE c.category_name = 'GPS'
    GROUP BY p.product_id, p.product_name, p.retail_price
    HAVING ABS(revenue_difference) > 200
    ORDER BY revenue_difference DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Actual vs Predicted Revenue for GPS Units</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h2>Actual vs Predicted Revenue for GPS Units</h2>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Retail Price</th>
                <th>Total Units Sold</th>
                <th>Units Sold at Discount</th>
                <th>Units Sold at Retail</th>
                <th>Actual Revenue</th>
                <th>Predicted Revenue</th>
                <th>Revenue Difference</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['product_id']; ?></td>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo $row['retail_price']; ?></td>
                <td><?php echo $row['total_units_sold']; ?></td>
                <td><?php echo $row['units_sold_at_discount']; ?></td>
                <td><?php echo $row['units_sold_at_retail']; ?></td>
                <td><?php echo $row['actual_revenue']; ?></td>
                <td><?php echo $row['predicted_revenue']; ?></td>
                <td><?php echo $row['revenue_difference']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
