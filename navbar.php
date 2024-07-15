<?php
function render_navbar() {
    echo '
    <div class="navbar">
        <a href="main_menu.php">Home</a>
        <div class="dropdown">
            <button class="dropbtn">Reports 
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
                <a href="manufacturer_report.php">Manufacturer’s Product Report</a><br>
                <a href="category_report.php">Category Report</a><br>
                <a href="gps_revenue_report.php">Actual vs Predicted Revenue for GPS Units</a><br>
                <a href="ac_groundhog_report.php">Air Conditioners on Groundhog Day</a><br>
                <a href="store_revenue_report.php">Store Revenue by Year by State</a><br>
                <a href="district_volume_report.php">District with Highest Volume for Each Category</a><br>
                <a href="revenue_population_report.php">Revenue by Population</a><br>
            </div>
        </div> 
        <a href="logout.php">Logout</a>
    </div>';
}
?>
