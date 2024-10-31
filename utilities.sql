-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2024 at 07:20 PM
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
-- Database: `utilities`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `consumer_id` int(11) DEFAULT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `consumer_id`, `address`) VALUES
(1, 1, '123 Elm Street, Springfield'),
(2, 2, '456 Oak Avenue, Metropolis');

-- --------------------------------------------------------

--
-- Table structure for table `consumers`
--

CREATE TABLE `consumers` (
  `consumer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `contact_details` varchar(50) DEFAULT NULL,
  `meter_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumers`
--

INSERT INTO `consumers` (`consumer_id`, `name`, `account_number`, `contact_details`, `meter_id`) VALUES
(1, 'John Doe', 'AC12345', '123-456-7890', 1),
(2, 'Jane Smith', 'AC67890', '098-765-4321', 2);

-- --------------------------------------------------------

--
-- Table structure for table `dailyconsumptionrecords`
--

CREATE TABLE `dailyconsumptionrecords` (
  `record_id` int(11) NOT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `meter_id` int(11) DEFAULT NULL,
  `date` varchar(10) DEFAULT NULL,
  `energy_consumed` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dailyconsumptionrecords`
--

INSERT INTO `dailyconsumptionrecords` (`record_id`, `account_number`, `meter_id`, `date`, `energy_consumed`) VALUES
(1, 'AC12345', 1, '2024-10-01', 5.00),
(2, 'AC67890', 2, '2024-10-01', 6.00);

-- --------------------------------------------------------

--
-- Table structure for table `electricitymeters`
--

CREATE TABLE `electricitymeters` (
  `meter_id` int(11) NOT NULL,
  `manufacture_date` varchar(10) DEFAULT NULL,
  `installation_date` varchar(10) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `electricitymeters`
--

INSERT INTO `electricitymeters` (`meter_id`, `manufacture_date`, `installation_date`, `address_id`) VALUES
(1, '2023-01-15', '2023-02-01', 1),
(2, '2022-07-20', '2022-08-05', 2);

-- --------------------------------------------------------

--
-- Table structure for table `monthlyconsumptionrecords`
--

CREATE TABLE `monthlyconsumptionrecords` (
  `record_id` int(11) NOT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `meter_id` int(11) DEFAULT NULL,
  `month_start_date` varchar(10) DEFAULT NULL,
  `energy_consumed` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthlyconsumptionrecords`
--

INSERT INTO `monthlyconsumptionrecords` (`record_id`, `account_number`, `meter_id`, `month_start_date`, `energy_consumed`) VALUES
(1, 'AC12345', 1, '2024-10-01', 120.50),
(2, 'AC67890', 2, '2024-10-01', 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `monthlypay`
--

CREATE TABLE `monthlypay` (
  `payment_id` int(11) NOT NULL,
  `consumer_id` int(11) DEFAULT NULL,
  `total_energy_consumed` decimal(10,2) NOT NULL,
  `installation_fee` decimal(10,2) DEFAULT 0.00,
  `taxes` decimal(10,2) DEFAULT 0.00,
  `miscellaneous_fees` decimal(10,2) DEFAULT 0.00,
  `total_amount_due` decimal(10,2) GENERATED ALWAYS AS (`total_energy_consumed` + `installation_fee` + `taxes` + `miscellaneous_fees`) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthlypay`
--

INSERT INTO `monthlypay` (`payment_id`, `consumer_id`, `total_energy_consumed`, `installation_fee`, `taxes`, `miscellaneous_fees`) VALUES
(1, 1, 120.50, 20.00, 15.00, 5.00),
(2, 2, 150.00, 25.00, 18.00, 7.00);

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

-- --------------------------------------------------------

--
-- Table structure for table `weeklyconsumptionrecords`
--

CREATE TABLE `weeklyconsumptionrecords` (
  `record_id` int(11) NOT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `meter_id` int(11) DEFAULT NULL,
  `week_start_date` varchar(10) DEFAULT NULL,
  `energy_consumed` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weeklyconsumptionrecords`
--

INSERT INTO `weeklyconsumptionrecords` (`record_id`, `account_number`, `meter_id`, `week_start_date`, `energy_consumed`) VALUES
(1, 'AC12345', 1, '2024-09-30', 35.00),
(2, 'AC67890', 2, '2024-09-30', 42.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `consumer_id` (`consumer_id`);

--
-- Indexes for table `consumers`
--
ALTER TABLE `consumers`
  ADD PRIMARY KEY (`consumer_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `meter_monthly` (`meter_id`);

--
-- Indexes for table `dailyconsumptionrecords`
--
ALTER TABLE `dailyconsumptionrecords`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `account_number` (`account_number`),
  ADD KEY `meter_id` (`meter_id`);

--
-- Indexes for table `electricitymeters`
--
ALTER TABLE `electricitymeters`
  ADD PRIMARY KEY (`meter_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `monthlyconsumptionrecords`
--
ALTER TABLE `monthlyconsumptionrecords`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `account_number` (`account_number`),
  ADD KEY `meter_id` (`meter_id`);

--
-- Indexes for table `monthlypay`
--
ALTER TABLE `monthlypay`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `consumer_id` (`consumer_id`);

--
-- Indexes for table `weeklyconsumptionrecords`
--
ALTER TABLE `weeklyconsumptionrecords`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `account_number` (`account_number`),
  ADD KEY `meter_id` (`meter_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `consumers`
--
ALTER TABLE `consumers`
  MODIFY `consumer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dailyconsumptionrecords`
--
ALTER TABLE `dailyconsumptionrecords`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `electricitymeters`
--
ALTER TABLE `electricitymeters`
  MODIFY `meter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `monthlyconsumptionrecords`
--
ALTER TABLE `monthlyconsumptionrecords`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `monthlypay`
--
ALTER TABLE `monthlypay`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `weeklyconsumptionrecords`
--
ALTER TABLE `weeklyconsumptionrecords`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `consumers` (`consumer_id`);

--
-- Constraints for table `consumers`
--
ALTER TABLE `consumers`
  ADD CONSTRAINT `meter_daily` FOREIGN KEY (`meter_id`) REFERENCES `dailyconsumptionrecords` (`meter_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `meter_monthly` FOREIGN KEY (`meter_id`) REFERENCES `monthlyconsumptionrecords` (`meter_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `meter_weekly` FOREIGN KEY (`meter_id`) REFERENCES `weeklyconsumptionrecords` (`meter_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dailyconsumptionrecords`
--
ALTER TABLE `dailyconsumptionrecords`
  ADD CONSTRAINT `dailyconsumptionrecords_ibfk_1` FOREIGN KEY (`account_number`) REFERENCES `consumers` (`account_number`),
  ADD CONSTRAINT `dailyconsumptionrecords_ibfk_2` FOREIGN KEY (`meter_id`) REFERENCES `electricitymeters` (`meter_id`);

--
-- Constraints for table `electricitymeters`
--
ALTER TABLE `electricitymeters`
  ADD CONSTRAINT `electricitymeters_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`);

--
-- Constraints for table `monthlyconsumptionrecords`
--
ALTER TABLE `monthlyconsumptionrecords`
  ADD CONSTRAINT `monthlyconsumptionrecords_ibfk_1` FOREIGN KEY (`account_number`) REFERENCES `consumers` (`account_number`),
  ADD CONSTRAINT `monthlyconsumptionrecords_ibfk_2` FOREIGN KEY (`meter_id`) REFERENCES `electricitymeters` (`meter_id`);

--
-- Constraints for table `monthlypay`
--
ALTER TABLE `monthlypay`
  ADD CONSTRAINT `monthlypay_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `consumers` (`consumer_id`);

--
-- Constraints for table `weeklyconsumptionrecords`
--
ALTER TABLE `weeklyconsumptionrecords`
  ADD CONSTRAINT `weeklyconsumptionrecords_ibfk_1` FOREIGN KEY (`account_number`) REFERENCES `consumers` (`account_number`),
  ADD CONSTRAINT `weeklyconsumptionrecords_ibfk_2` FOREIGN KEY (`meter_id`) REFERENCES `electricitymeters` (`meter_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
