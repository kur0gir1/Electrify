-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2024 at 01:21 PM
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
-- Table structure for table `consumers`
--

CREATE TABLE `consumers` (
  `consumer_id` int(11) NOT NULL,
  `meter_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_details` varchar(50) DEFAULT NULL,
  `address` text NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','consumer') DEFAULT 'consumer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumers`
--

INSERT INTO `consumers` (`consumer_id`, `meter_id`, `first_name`, `last_name`, `account_number`, `email`, `contact_details`, `address`, `username`, `password`, `role`) VALUES
(1, NULL, 'Gilbert', 'Lerion', 'AC75003020', '12312321@gmail.com', '12323213', 'Bacolod City', 'gilbert7500', '111222', 'consumer'),
(2, NULL, 'Nye', 'Nye', 'AC66184876', 'gilbert.lerion@lccbonline.edu.ph', '09232323', 'Bacolod City', 'nye6618', '123', 'consumer'),
(3, NULL, 'testing', 'test', 'AC98966100', '12312321@gmail.com', 'sksksk', 'Baculod', 'testing9896', 'AC98966100', 'consumer');

-- --------------------------------------------------------

--
-- Table structure for table `consumption_records`
--

CREATE TABLE `consumption_records` (
  `record_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `energy_consumed` decimal(10,2) NOT NULL,
  `status` enum('Completed','Pending','Failed','Overdue') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumption_records`
--

INSERT INTO `consumption_records` (`record_id`, `consumer_id`, `date`, `energy_consumed`, `status`) VALUES
(1, 1, '2024-12-16', 44.00, 'Completed'),
(2, 1, '2025-01-02', 43.00, 'Overdue'),
(3, 1, '2024-12-05', 231.00, 'Pending'),
(4, 2, '2024-12-21', 32.00, 'Failed'),
(5, 2, '2024-12-28', 321.00, 'Pending'),
(6, 1, '2024-12-17', 222.00, 'Completed'),
(7, 1, '2024-11-19', 32.00, 'Failed'),
(8, 2, '2024-11-06', 3.00, 'Completed'),
(9, 2, '2024-10-15', 1.00, 'Overdue');

-- --------------------------------------------------------

--
-- Table structure for table `electricitymeters`
--

CREATE TABLE `electricitymeters` (
  `meter_id` int(11) NOT NULL,
  `manufacture_date` date DEFAULT NULL,
  `installation_date` date DEFAULT NULL,
  `consumer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `electricitymeters`
--

INSERT INTO `electricitymeters` (`meter_id`, `manufacture_date`, `installation_date`, `consumer_id`) VALUES
(1, '2024-12-15', '2024-12-15', 1),
(2, '2024-12-15', '2024-12-15', 2),
(3, '2024-12-15', '2024-12-15', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('consumer','admin') NOT NULL DEFAULT 'consumer',
  `terms` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `terms`) VALUES
(1, 'admin', 'admin@payrolldb.com', '12345', 'admin', 1),
(2, 'admen', 'admen@gmail.com', 'admenadmen', 'admin', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `consumers`
--
ALTER TABLE `consumers`
  ADD PRIMARY KEY (`consumer_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `meter_id` (`meter_id`) USING BTREE;

--
-- Indexes for table `consumption_records`
--
ALTER TABLE `consumption_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `consumer_id` (`consumer_id`);

--
-- Indexes for table `electricitymeters`
--
ALTER TABLE `electricitymeters`
  ADD PRIMARY KEY (`meter_id`),
  ADD KEY `fk_consumer_new` (`consumer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consumers`
--
ALTER TABLE `consumers`
  MODIFY `consumer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `consumption_records`
--
ALTER TABLE `consumption_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `electricitymeters`
--
ALTER TABLE `electricitymeters`
  MODIFY `meter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consumers`
--
ALTER TABLE `consumers`
  ADD CONSTRAINT `consumers_ibfk_1` FOREIGN KEY (`meter_id`) REFERENCES `electricitymeters` (`meter_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_meter` FOREIGN KEY (`meter_id`) REFERENCES `electricitymeters` (`meter_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consumption_records`
--
ALTER TABLE `consumption_records`
  ADD CONSTRAINT `consumption_records_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `consumers` (`consumer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `electricitymeters`
--
ALTER TABLE `electricitymeters`
  ADD CONSTRAINT `fk_consumer` FOREIGN KEY (`consumer_id`) REFERENCES `consumers` (`consumer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consumer_new` FOREIGN KEY (`consumer_id`) REFERENCES `consumers` (`consumer_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
