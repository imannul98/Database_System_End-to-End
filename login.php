<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h2>Login</h2>
    <form action="authenticate.php" method="post">
        <label for="employee_id">Employee ID:</label><br>
        <input type="text" id="employee_id" name="employee_id"><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>