-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025 at 08:20 AM
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
-- Database: `inventory-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `property_number` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `model_number` int(11) NOT NULL,
  `serial_number` int(11) NOT NULL,
  `acquisition_date_cost` varchar(255) NOT NULL,
  `person_accountable` varchar(255) NOT NULL,
  `status` varchar(266) NOT NULL,
  `signature_of_inventory_team_date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `property_number`, `description`, `model_number`, `serial_number`, `acquisition_date_cost`, `person_accountable`, `status`, `signature_of_inventory_team_date`) VALUES
(1, 'wertwertwert3423423', 'trial', 34567890, 34, ' 567fgyu', 'yuiiy', 'yuiui', 'yuiyuiyuigyhuiyu'),
(2, '2020-1-08-05-070-03-0001-2017', 'radio transreciever', 31242342, 34223423, 'dec 21 2020 325,000', 'erroljohnpardillo', 'unserviceable', 'erroljohnpardillo'),
(3, '2020-1-08-05-070-03-2341-2017', 'laptop', 31223442, 2147483647, 'dec 21 2020 68,000', 'erroljohnpardillo', 'unserviceable', 'erroljohnpardillo'),
(4, '2020-1-08-05-070-03-030-0001', 'printer', 2147483647, 2147483647, 'april 13 2016 55,000', 'erroljohnpardillo', 'unserviceable', 'erroljohnpardillo');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `property-number-index` (`property_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
