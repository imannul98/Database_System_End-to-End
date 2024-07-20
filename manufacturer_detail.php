<?php
include 'config.php';
include 'helper.php';
check_login();

// Get the manufacturer name from the query string
$manufacturer_name = $_GET['manufacturer'];

// Fetch the summary information for the manufacturer
$sql_summary = "
    SELECT p.ManufacturerName,
           COUNT(DISTINCT p.PID) AS total_products,
           AVG(p.RetailPrice) AS avg_price,
           MIN(p.RetailPrice) AS min_price,
           MAX(p.RetailPrice) AS max_price
    FROM product p
    WHERE p.ManufacturerName = ?
    GROUP BY p.ManufacturerName";
$stmt_summary = $conn->prepare($sql_summary);
$stmt_summary->bind_param("s", $manufacturer_name);
$stmt_summary->execute();
$result_summary = $stmt_summary->get_result();
$summary = $result_summary->fetch_assoc();
$stmt_summary->close();

// Fetch the detailed product information for the manufacturer
$sql_detail = "
    SELECT p.PID,
           p.ProductName,
           GROUP_CONCAT(DISTINCT c.CategoryName ORDER BY c.CategoryName SEPARATOR ', ') AS categories,
           p.RetailPrice
    FROM product p
    JOIN product_category pc ON p.PID = pc.PID
    JOIN category c ON pc.CategoryName = c.CategoryName
    WHERE p.ManufacturerName = ?
    GROUP BY p.PID, p.ProductName, p.RetailPrice
    ORDER BY p.RetailPrice DESC";
$stmt_detail = $conn->prepare($sql_detail);
$stmt_detail->bind_param("s", $manufacturer_name);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manufacturer Detail - <?php echo htmlspecialchars($manufacturer_name); ?></title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h3>Available Reports</h3>
    <ul>
        <?php foreach (get_user_reports($_SESSION['employee_id']) as $report_name => $report_file): ?>
            <li><a href="<?php echo $report_file; ?>"><?php echo $report_name; ?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php render_logout_button(); ?>
    <h2>Manufacturer Detail - <?php echo htmlspecialchars($manufacturer_name); ?></h2>
    <?php if ($summary): ?>
        <p>Total Products: <?php echo htmlspecialchars($summary['total_products']); ?></p>
        <p>Average Price: <?php echo htmlspecialchars($summary['avg_price']); ?></p>
        <p>Minimum Price: <?php echo htmlspecialchars($summary['min_price']); ?></p>
        <p>Maximum Price: <?php echo htmlspecialchars($summary['max_price']); ?></p>
    <?php else: ?>
        <p>No summary information available for this manufacturer.</p>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Categories</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_detail->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['PID']); ?></td>
                <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                <td><?php echo htmlspecialchars($row['categories']); ?></td>
                <td><?php echo htmlspecialchars($row['RetailPrice']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

