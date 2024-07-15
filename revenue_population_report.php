<?php
include 'config.php';
include 'navbar.php';
include 'helper.php';
check_login();

$sql = "
    SELECT
        CASE
            WHEN c.Population < 3700000 THEN 'Small'
            WHEN c.Population BETWEEN 3700000 AND 6699999 THEN 'Medium'
            WHEN c.Population BETWEEN 6700000 AND 8999999 THEN 'Large'
            ELSE 'Extra Large'
        END AS city_size,
        YEAR(se.Date) AS year,
        SUM(se.Quantity * IFNULL(d.DiscountPrice, p.RetailPrice)) AS total_revenue
    FROM city c
    JOIN store s ON c.CityName = s.CityName AND c.State = s.State
    JOIN sell se ON s.StoreNumber = se.StoreNumber
    JOIN product p ON se.PID = p.PID
    LEFT JOIN discount d ON se.PID = d.PID AND se.Date = d.Date
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
    <?php render_navbar(); ?>
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
