<?php
include 'config.php';
check_login();

$state = $_GET['state'] ?? '';

$sql = "
    SELECT s.store_id,
           s.store_address,
           c.city_name,
           YEAR(se.sale_date) AS year,
           SUM(se.quantity * IFNULL(d.discount_price, p.retail_price)) AS total_revenue
    FROM store s
    JOIN city c ON s.city_id = c.city_id
    JOIN sell se ON s.store_id = se.store_id
    JOIN product p ON se.product_id = p.product_id
    LEFT JOIN discount d ON se.product_id = d.product_id AND se.sale_date = d.discount_date
    WHERE c.state = '$state'
    GROUP BY s.store_id, s.store_address, c.city_name, YEAR(se.sale_date)
    ORDER BY year ASC, total_revenue DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Store Revenue by Year by State</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h2>Store Revenue by Year by State</h2>
    <form method="get">
        <label for="state">State:</label>
        <select id="state" name="state">
            <!-- Add options for each state -->
            <option value="NY">New York</option>
            <option value="CA">California</option>
            <!-- Add more states as needed -->
        </select>
        <button type="submit">Submit</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Store ID</th>
                <th>Store Address</th>
                <th>City Name</th>
                <th>Year</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['store_id']; ?></td>
                <td><?php echo $row['store_address']; ?></td>
                <td><?php echo $row['city_name']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['total_revenue']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
