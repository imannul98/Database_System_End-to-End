<?php
include 'config.php';
check_login();

$year = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';

$sql = "
    SELECT c.category_name,
           d.district_name,
           SUM(se.quantity) AS total_units_sold
    FROM category c
    JOIN product_category pc ON c.category_id = pc.category_id
    JOIN product p ON pc.product_id = p.product_id
    JOIN sell se ON p.product_id = se.product_id
    JOIN store s ON se.store_id = s.store_id
    JOIN district d ON s.district_id = d.district_id
    WHERE YEAR(se.sale_date) = '$year' AND MONTH(se.sale_date) = '$month'
    GROUP BY c.category_name, d.district_name
    ORDER BY c.category_name ASC, total_units_sold DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>District with Highest Volume for Each Category</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h2>District with Highest Volume for Each Category</h2>
    <form method="get">
        <label for="year">Year:</label>
        <input type="text" id="year" name="year">
        <label for="month">Month:</label>
        <input type="text" id="month" name="month">
        <button type="submit">Submit</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Category Name</th>
                <th>District Name</th>
                <th>Total Units Sold</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['category_name']; ?></td>
                <td><?php echo $row['district_name']; ?></td>
                <td><?php echo $row['total_units_sold']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
