-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2024 at 07:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `payrolldb`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL,
  `ManagerID` int(11) DEFAULT NULL,
  `Actions` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`DepartmentID`, `DepartmentName`, `ManagerID`, `Actions`) VALUES
(1, 'President', NULL, ''),
(2, 'CEO', NULL, ''),
(3, 'COO', NULL, ''),
(4, 'CFO', NULL, ''),
(5, 'HR', 5, ''),
(6, 'Marketing', 14, ''),
(7, 'Sales', 19, ''),
(8, 'IT Department', 7, ''),
(9, 'Finance', 23, ''),
(10, 'Operations', 27, '');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `EmployeeID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(15) NOT NULL,
  `HireDate` date NOT NULL,
  `JobTitle` varchar(50) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `ManagerID` int(11) DEFAULT NULL,
  `Actions` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`EmployeeID`, `FirstName`, `LastName`, `Email`, `PhoneNumber`, `HireDate`, `JobTitle`, `DepartmentID`, `ManagerID`, `Actions`) VALUES
(1, 'John', 'Doe', 'president@example.com', '1234567890', '2020-01-01', 'President', 1, NULL, ''),
(2, 'Jane', 'Smith', 'ceo@example.com', '1234567891', '2020-01-01', 'CEO', 2, 1, ''),
(3, 'Alan', 'Brown', 'coo@example.com', '1234567892', '2020-01-01', 'COO', 3, 2, ''),
(4, 'Alice', 'Johnson', 'cfo@example.com', '1234567893', '2020-01-01', 'CFO', 4, 2, ''),
(5, 'Mary', 'Williams', 'hr@example.com', '1234567894', '2020-01-01', 'HR Manager', 5, 2, ''),
(6, 'Susan', 'Anderson', 'hr_assistant@example.com', '1234567897', '2020-02-01', 'HR Assistant', 5, 5, ''),
(7, 'Diana', 'Morris', 'recruiter@example.com', '1234567898', '2020-02-15', 'Recruiter', 5, 5, ''),
(8, 'David', 'Wilson', 'it@example.com', '1234567896', '2020-01-01', 'IT Specialist', 8, 2, ''),
(9, 'James', 'Clark', 'network_admin@example.com', '1234567801', '2020-03-01', 'Network Administrator', 8, 7, ''),
(10, 'Chris', 'Lopez', 'security_analyst@example.com', '1234567802', '2020-04-01', 'IT Security Analyst', 8, 7, ''),
(11, 'Rebecca', 'Wright', 'software_engineer@example.com', '1234567803', '2020-05-01', 'Software Engineer', 8, 7, ''),
(12, 'Daniel', 'Green', 'it_support@example.com', '1234567804', '2020-06-01', 'IT Support Technician', 8, 7, ''),
(13, 'Jessica', 'Adams', 'marketing_manager@example.com', '1234567805', '2020-07-01', 'Marketing Manager', 6, 2, ''),
(14, 'Laura', 'Baker', 'content_creator@example.com', '1234567806', '2020-08-01', 'Content Creator', 6, 14, ''),
(15, 'Ryan', 'Martinez', 'seo_specialist@example.com', '1234567807', '2020-09-01', 'SEO Specialist', 6, 14, ''),
(16, 'Alex', 'Parker', 'social_media_manager@example.com', '1234567808', '2020-10-01', 'Social Media Manager', 6, 14, ''),
(17, 'Emma', 'Campbell', 'graphic_designer@example.com', '1234567809', '2020-11-01', 'Graphic Designer', 6, 14, ''),
(18, 'Ethan', 'Perez', 'sales_manager@example.com', '1234567810', '2021-01-01', 'Sales Manager', 7, 2, ''),
(19, 'Megan', 'Sanchez', 'account_executive@example.com', '1234567811', '2021-02-01', 'Account Executive', 7, 19, ''),
(20, 'Tyler', 'Reed', 'sales_rep@example.com', '1234567812', '2021-03-01', 'Sales Representative', 7, 19, ''),
(21, 'Samantha', 'Foster', 'sales_assistant@example.com', '1234567813', '2021-04-01', 'Sales Assistant', 7, 19, ''),
(22, 'Grace', 'Cruz', 'finance_manager@example.com', '1234567814', '2021-05-01', 'Finance Manager', 9, 4, ''),
(23, 'Benjamin', 'Garcia', 'accountant@example.com', '1234567815', '2021-06-01', 'Accountant', 9, 23, ''),
(24, 'Lucas', 'Torres', 'payroll_specialist@example.com', '1234567816', '2021-07-01', 'Payroll Specialist', 9, 23, ''),
(25, 'Natalie', 'Diaz', 'budget_analyst@example.com', '1234567817', '2021-08-01', 'Budget Analyst', 9, 23, ''),
(26, 'Eli', 'Sullivan', 'operations_manager@example.com', '1234567818', '2021-09-01', 'Operations Manager', 10, 3, ''),
(27, 'Madison', 'Lee', 'operations_analyst@example.com', '1234567819', '2021-10-01', 'Operations Analyst', 10, 27, ''),
(28, 'Caleb', 'Jenkins', 'supply_chain_manager@example.com', '1234567820', '2021-11-01', 'Supply Chain Manager', 10, 27, ''),
(29, 'Victoria', 'Ramirez', 'logistics_coordinator@example.com', '1234567821', '2021-12-01', 'Logistics Coordinator', 10, 27, ''),
(30, 'Liam', 'Harris', 'liam.harris@example.com', '1234567822', '2024-09-29', 'Data Analyst', 11, 2, '');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `PayrollID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `GrossPay` decimal(10,2) NOT NULL,
  `Deductions` decimal(10,2) NOT NULL,
  `NetPay` decimal(10,2) NOT NULL,
  `Actions` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`PayrollID`, `EmployeeID`, `FirstName`, `LastName`, `GrossPay`, `Deductions`, `NetPay`, `Actions`) VALUES
(1, 1, 'John', 'Doe', 0.00, 0.00, 0.00, ''),
(2, 2, 'Jane', 'Smith', 0.00, 0.00, 0.00, ''),
(3, 3, 'Alan', 'Brown', 0.00, 0.00, 0.00, ''),
(4, 4, 'Alice', 'Johnson', 0.00, 0.00, 0.00, ''),
(5, 5, 'Mary', 'Williams', 0.00, 0.00, 0.00, ''),
(6, 6, 'Susan', 'Anderson', 0.00, 0.00, 0.00, ''),
(7, 7, 'Diana', 'Morris', 0.00, 0.00, 0.00, ''),
(8, 8, 'David', 'Wilson', 0.00, 0.00, 0.00, ''),
(9, 9, 'James', 'Clark', 0.00, 0.00, 0.00, ''),
(10, 10, 'Chris', 'Lopez', 0.00, 0.00, 0.00, ''),
(11, 11, 'Rebecca', 'Wright', 0.00, 0.00, 0.00, ''),
(12, 12, 'Daniel', 'Green', 0.00, 0.00, 0.00, ''),
(13, 13, 'Jessica', 'Adams', 0.00, 0.00, 0.00, ''),
(14, 14, 'Laura', 'Baker', 0.00, 0.00, 0.00, ''),
(15, 15, 'Ryan', 'Martinez', 0.00, 0.00, 0.00, ''),
(16, 16, 'Alex', 'Parker', 0.00, 0.00, 0.00, ''),
(17, 17, 'Emma', 'Campbell', 0.00, 0.00, 0.00, ''),
(18, 18, 'Ethan', 'Perez', 0.00, 0.00, 0.00, ''),
(19, 19, 'Megan', 'Sanchez', 0.00, 0.00, 0.00, ''),
(20, 20, 'Tyler', 'Reed', 0.00, 0.00, 0.00, ''),
(21, 21, 'Samantha', 'Foster', 0.00, 0.00, 0.00, ''),
(22, 22, 'Grace', 'Cruz', 0.00, 0.00, 0.00, ''),
(23, 23, 'Benjamin', 'Garcia', 0.00, 0.00, 0.00, ''),
(24, 24, 'Lucas', 'Torres', 0.00, 0.00, 0.00, ''),
(25, 25, 'Natalie', 'Diaz', 0.00, 0.00, 0.00, ''),
(26, 26, 'Eli', 'Sullivan', 0.00, 0.00, 0.00, ''),
(27, 27, 'Madison', 'Lee', 0.00, 0.00, 0.00, ''),
(28, 28, 'Caleb', 'Jenkins', 0.00, 0.00, 0.00, ''),
(29, 29, 'Victoria', 'Ramirez', 0.00, 0.00, 0.00, ''),
(30, 30, 'Liam', 'Harris', 0.00, 0.00, 0.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `terms` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `email`, `password`, `terms`) VALUES
('admin', 'admin@payrolldb.com', '12345', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`DepartmentID`),
  ADD KEY `ManagerID` (`ManagerID`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD UNIQUE KEY `FirstName` (`FirstName`,`LastName`,`Email`,`PhoneNumber`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `ManagerID` (`ManagerID`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`PayrollID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `username` (`username`,`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `PayrollID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `employeeDepartmentIDConnection` FOREIGN KEY (`DepartmentID`) REFERENCES `employees` (`DepartmentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employeeManagerIDConnection` FOREIGN KEY (`ManagerID`) REFERENCES `employees` (`ManagerID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
