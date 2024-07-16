# CS6400_SU24_Team038 Project

## Overview

This project is a web-based application for managing various entities such as stores, cities, districts, manufacturers, products, categories, and holidays. 
The application provides functionalities to add, update, delete, and view records for these entities. 
It also includes user authentication and role-based access control.

## Features

- User authentication and session management
- Role-based access control
- CRUD operations for stores, cities, districts, manufacturers, products, categories, and holidays
- Dynamic statistics display
- Error handling and security measures
- Responsive design for better user experience

## Technologies Used

- PHP
- MySQL
- HTML
- CSS


## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/cs6400-2024-02-Team38-main.git
   ```

2. Navigate to the project directory:

   ```bash
   cd cs6400-2024-02-Team38-main
   ```

3. Import the database schema:

   ```sql
   mysql -u username -p database_name < cs6400_su24_team038.sql
   ```

4. Configure the database connection:

   Update the `config.php` file with your database credentials.

   ```php
   <?php
   $servername = "localhost";
   $username = "your_username";
   $password = "your_password";
   $dbname = "cs6400_su24_team038";

   // Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);

   // Check connection
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ?>
   ```

5. Start the web server and navigate to the project directory in your web browser.

## Usage

1. **Login:**
   - Navigate to the login page and enter your credentials.
   - If you do not have an account, contact the administrator to create one for you.

2. **Main Menu:**
   - After logging in, you will be redirected to the main menu where you can see various statistics about stores, cities, districts, manufacturers, products, categories, and holidays.

3. **Navigation:**
   - Use the navigation bar to access different sections of the application.

## File Structure

```
cs6400-2024-02-Team38-main/
├── authenticate.php
├── category_report.php
├── config.php
├── cs6400_su24_team038.sql
├── district_volume_report.php
├── gps_revenue_report.php
├── helper.php
├── login.php
├── logout.php
├── main.css
├── main_menu.php
├── manufacturer_report.php
├── navbar.php
├── revenue_population_report.php
├── store_revenue_report.php
├── team038_p2_schema.sql
└── Phase 1/
    ├── BuzzBuy IFD Diagram.pdf
    ├── EER Diagram BuzzBuy.pdf
    ├── EER Diagram BuzzBuy.png
    ├── README.md
    ├── team38_p1_eer.pdf
    ├── team38_p1_ifd.pdf
    ├── team38_p1_report.docx
    └── team38_p1_report.pdf
...

## Contact

For any questions or suggestions, please contact:

- Team Member 1: imannulh@gatech.edu
- Team Member 2: nicoledb@gatech.edu
- Team Member 3: wusif@gatech.edu
- Team Member 4: ikhan@gatech.edu
