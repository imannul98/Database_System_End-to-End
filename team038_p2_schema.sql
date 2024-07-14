-- The Following SQL Code is written and tested in MySQL

CREATE USER IF NOT EXISTS 'imannulh'@'localhost' IDENTIFIED BY 'gatech123';
CREATE USER IF NOT EXISTS 'nicoledb'@'localhost' IDENTIFIED BY 'gatech234';
CREATE USER IF NOT EXISTS 'wusif'@'localhost' IDENTIFIED BY 'gatech345';
CREATE USER IF NOT EXISTS 'ikhan'@'localhost' IDENTIFIED BY 'gatech456';
DROP DATABASE IF EXISTS `cs6400_su24_team038`;
SET default_storage_engine=InnoDB;
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS cs6400_su24_team038
DEFAULT CHARACTER SET utf8mb4
DEFAULT COLLATE utf8mb4_unicode_ci;

USE cs6400_su24_team038;
GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO 'imannulh'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO 'nicoledb'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO 'wusif'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO 'ikhan'@'localhost';
GRANT ALL PRIVILEGES ON `imannulh`.* TO 'imannulh'@'localhost';
GRANT ALL PRIVILEGES ON `cs6400_fa17_team038`.* TO 'imannulh'@'localhost';
FLUSH PRIVILEGES;

-- Tables
CREATE TABLE Category (
    CategoryName VARCHAR(255) NOT NULL,
    PRIMARY KEY (CategoryName)
);

-- Create Manufacturer table
CREATE TABLE Manufacturer (
    ManufacturerName VARCHAR(255) NOT NULL,
    PRIMARY KEY (ManufacturerName)
);

-- Create Product table
CREATE TABLE Product (
    PID INT NOT NULL AUTO_INCREMENT,
    ProductName VARCHAR(255) NOT NULL,
    RetailPrice DECIMAL(10, 2) NOT NULL,
    ManufacturerName VARCHAR(255) NOT NULL,
    StoreNumber INT NOT NULL,
    PRIMARY KEY (PID),
    FOREIGN KEY (ManufacturerName) REFERENCES Manufacturer(ManufacturerName)
);

-- Create Product_Category
CREATE TABLE Product_Category (
    PID INT NOT NULL,
    CategoryName VARCHAR(255) NOT NULL,
    PRIMARY KEY (PID, CategoryName),
    FOREIGN KEY (PID) REFERENCES Product(PID),
    FOREIGN KEY (CategoryName) REFERENCES Category(CategoryName)
);

-- Create Date Table
CREATE TABLE Date (
	Date DATE NOT NULL,
    PRIMARY KEY (Date)
);

-- Create Discount table
CREATE TABLE Discount (
    PID INT NOT NULL,
    Date DATE NOT NULL,
    DiscountPrice DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (PID, Date),
    FOREIGN KEY (PID) REFERENCES Product(PID),
    FOREIGN KEY (Date) REFERENCES Date(Date)
);


-- Create City Table
CREATE TABLE City (
    CityName VARCHAR(255) NOT NULL,
    State VARCHAR(255) NOT NULL,
    Population INT,
    PRIMARY KEY (CityName, State)
);


-- Create District Table
CREATE TABLE District (
    DistrictNumber INT NOT NULL,
    PRIMARY KEY (DistrictNumber)
);

-- Create Store table
CREATE TABLE Store (
    StoreNumber INT NOT NULL,
    PhoneNumber VARCHAR(20),
    DistrictNumber INT NOT NULL,
    CityName VARCHAR(255) NOT NULL,
    State VARCHAR(255) NOT NULL,
    PRIMARY KEY (StoreNumber),
    FOREIGN KEY (DistrictNumber) REFERENCES District(DistrictNumber),
    FOREIGN KEY (CityName, State) REFERENCES City(CityName, State)
);

-- Create Sell Table
CREATE TABLE Sell (
    PID INT NOT NULL,
    StoreNumber INT NOT NULL,
    Date DATE NOT NULL,
    Quantity INT NOT NULL,
    PRIMARY KEY (PID, StoreNumber, Date),
    FOREIGN KEY (PID) REFERENCES Product(PID),
    FOREIGN KEY (StoreNumber) REFERENCES Store(StoreNumber),
    FOREIGN KEY (Date) REFERENCES Date(Date)
);

-- Create User table
CREATE TABLE User (
    EmployeeID INT NOT NULL,
    Last4SSN CHAR(4) NOT NULL,
    LastName VARCHAR(255) NOT NULL,
    FirstName VARCHAR(255) NOT NULL,
    AuditViewFlag BOOLEAN NOT NULL,
    DistrictNumber INT NOT NULL,
    PRIMARY KEY (EmployeeID),
    FOREIGN KEY (DistrictNumber) REFERENCES District(DistrictNumber)
);

-- Create Reports table
CREATE TABLE Reports (
    ReportName VARCHAR(255) NOT NULL,
    PRIMARY KEY (ReportName)
);

-- Create AuditReport table
CREATE TABLE AuditReport (
    TimeStamp DATETIME NOT NULL,
    EmployeeID INT NOT NULL,
    ReportName VARCHAR(255) NOT NULL,
    PRIMARY KEY (TimeStamp, EmployeeID),
    FOREIGN KEY (EmployeeID) REFERENCES User(EmployeeID),
    FOREIGN KEY (ReportName) REFERENCES Reports(ReportName)
);

-- Create Holiday table
CREATE TABLE Holiday (
    Date DATE NOT NULL,
    HolidayName VARCHAR(255) NOT NULL,
    EmployeeID INT NOT NULL,
    PRIMARY KEY (Date, EmployeeID, HolidayName),
    FOREIGN KEY (EmployeeID) REFERENCES User(EmployeeID),
    FOREIGN KEY (Date) REFERENCES Date(Date)
);

-- Add constraints
ALTER TABLE Product
ADD CONSTRAINT CHK_RetailPrice CHECK (RetailPrice >= 0);

ALTER TABLE Discount
ADD CONSTRAINT CHK_DiscountPrice CHECK (DiscountPrice >= 0);

ALTER TABLE City
ADD CONSTRAINT CHK_Population CHECK (Population >= 0);

ALTER TABLE Sell
ADD CONSTRAINT CHK_Quantity CHECK (Quantity >= 0);

ALTER TABLE User
ADD CONSTRAINT CHK_Last4SSN CHECK (Last4SSN REGEXP '^[0-9]{4}$');