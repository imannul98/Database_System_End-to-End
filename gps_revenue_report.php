<?php
include 'config.php';
include 'navbar.php';
include 'helper.php';
check_login();

// Query to fetch GPS revenue report data
$sql = "
    SELECT p.PID,
           p.ProductName,
           p.RetailPrice,
           SUM(s.Quantity) AS total_units_sold,
           SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity ELSE 0 END) AS units_sold_at_discount,
           SUM(CASE WHEN d.DiscountPrice IS NULL THEN s.Quantity ELSE 0 END) AS units_sold_at_retail,
           SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity * d.DiscountPrice ELSE s.Quantity * p.RetailPrice END) AS actual_revenue,
           SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity * p.RetailPrice * 0.75 ELSE s.Quantity * p.RetailPrice END) AS predicted_revenue,
           (SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity * d.DiscountPrice ELSE s.Quantity * p.RetailPrice END) - 
           SUM(CASE WHEN d.DiscountPrice IS NOT NULL THEN s.Quantity * p.RetailPrice * 0.75 ELSE s.Quantity * p.RetailPrice END)) AS revenue_difference
    FROM product p
    JOIN sell s ON p.PID = s.PID
    LEFT JOIN discount d ON p.PID = d.PID AND s.Date = d.Date
    JOIN product_category pc ON p.PID = pc.PID
    JOIN category c ON pc.CategoryName = c.CategoryName
    WHERE c.CategoryName = 'GPS'
    GROUP BY p.PID, p.ProductName, p.RetailPrice
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
    <?php render_navbar(); ?>
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
                <td><?php echo $row['PID']; ?></td>
                <td><?php echo $row['ProductName']; ?></td>
                <td><?php echo $row['RetailPrice']; ?></td>
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
