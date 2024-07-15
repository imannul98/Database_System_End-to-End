<?php
include 'config.php';
include 'navbar.php';
include 'helper.php';
check_login();

// Fetch unique states
$states_result = $conn->query("SELECT DISTINCT State FROM city ORDER BY State");

$state = $_GET['state'] ?? '';

$sql = "
    SELECT s.StoreNumber,
           s.PhoneNumber,
           c.CityName,
           YEAR(se.Date) AS year,
           SUM(se.Quantity * IFNULL(d.DiscountPrice, p.RetailPrice)) AS total_revenue
    FROM store s
    JOIN city c ON s.CityName = c.CityName AND s.State = c.State
    JOIN sell se ON s.StoreNumber = se.StoreNumber
    JOIN product p ON se.PID = p.PID
    LEFT JOIN discount d ON se.PID = d.PID AND se.Date = d.Date
    WHERE c.State = '$state'
    GROUP BY s.StoreNumber, s.PhoneNumber, c.CityName, YEAR(se.Date)
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
    <?php render_navbar(); ?>
    <h2>Store Revenue by Year by State</h2>
    <form method="get">
        <label for="state">State:</label>
        <select id="state" name="state">
            <?php while ($state_row = $states_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($state_row['State']); ?>" <?php if ($state_row['State'] == $state) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($state_row['State']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Submit</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Store Number</th>
                <th>Phone Number</th>
                <th>City Name</th>
                <th>Year</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['StoreNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['PhoneNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['CityName']); ?></td>
                <td><?php echo htmlspecialchars($row['year']); ?></td>
                <td><?php echo htmlspecialchars($row['total_revenue']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
