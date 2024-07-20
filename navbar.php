<?php
function render_navbar($conn) {
    // Get the employee ID from the session
    $employee_id = $_SESSION['employee_id'];

    // Fetch the reports the user has access to
    $reports_sql = "
        SELECT r.ReportName, r.ReportFile
        FROM reports r
        JOIN user_reports ur ON r.ReportID = ur.ReportID
        WHERE ur.UserID = ?";
    $reports_stmt = $conn->prepare($reports_sql);
    $reports_stmt->bind_param("i", $employee_id);
    $reports_stmt->execute();
    $reports_result = $reports_stmt->get_result();
    
    echo '
    <div class="navbar">
        <a href="main_menu.php">Home</a>
        <div class="dropdown">
            <button class="dropbtn">Reports 
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">';
    
    while ($report = $reports_result->fetch_assoc()) {
        echo '<a href="' . htmlspecialchars($report['ReportFile']) . '">' . htmlspecialchars($report['ReportName']) . '</a><br>';
    }

    echo '
            </div>
        </div> 
        <a href="logout.php">Logout</a>
    </div>';

    $reports_stmt->close();
}
?>
