<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_POST['employee_id'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM user WHERE EmployeeID = ? AND Password = ?");
    if ($stmt) {
        $stmt->bind_param("is", $employee_id, $password); // "i" for integer, "s" for string

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Valid login credentials
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['employee_id'] = $employee_id;

            header("Location: main_menu.php");
            exit();
        } else {
            // Invalid login credentials
            $login_error = "Invalid login credentials. Please try again.";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Handle errors with the prepared statement
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <form action="authenticate.php" method="post">
        <label for="employee_id">Employee ID:</label>
        <input type="number" id="employee_id" name="employee_id" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
    <?php
    if ($login_error != '') {
        echo '<p style="color: red;">' . $login_error . '</p>';
    }
    ?>
</body>
</html>
