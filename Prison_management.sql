-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2024 at 12:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prison_mgmt`
--
CREATE DATABASE IF NOT EXISTS `prison_mgmt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `prison_mgmt`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(10) NOT NULL,
  `admin_lname` varchar(50) NOT NULL,
  `admin_fname` varchar(50) NOT NULL,
  `admin_mname` varchar(50) NOT NULL,
  `admin_username` varchar(30) NOT NULL,
  `admin_password` varchar(30) NOT NULL,
  `admin_email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_lname`, `admin_fname`, `admin_mname`, `admin_username`, `admin_password`, `admin_email`) VALUES
(1, 'admin1', 'admin', '1', 'admin', '2222', 'admin1@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `admitted`
--

CREATE TABLE `admitted` (
  `con_id` int(10) NOT NULL,
  `con_fname` varchar(50) NOT NULL,
  `con_mname` varchar(50) NOT NULL,
  `con_lname` varchar(50) NOT NULL,
  `con_age` int(2) NOT NULL,
  `con_gender` char(1) NOT NULL,
  `con_case` varchar(200) NOT NULL,
  `con_date_admitted` date NOT NULL,
  `con_date_rel` date NOT NULL,
  `con_profile` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admitted`
--

INSERT INTO `admitted` (`con_id`, `con_fname`, `con_mname`, `con_lname`, `con_age`, `con_gender`, `con_case`, `con_date_admitted`, `con_date_rel`, `con_profile`) VALUES
(17, 'Ariza', 'Aguinaldo', 'Dela Cruz', 20, 'F', 'nambugbog', '2023-10-07', '2023-10-28', ''),
(19, 'John Hynes', 'Nice', 'Longares', 24, 'M', 'nanloko', '2023-10-03', '2023-10-14', ''),
(21, 'Marianne Nathalie Knycole', 'Reyes', 'Escover', 20, 'F', 'pinatay si sam', '2023-10-01', '2023-10-31', ''),
(22, 'Carlito', 'Pasco', 'Manuel', 21, 'M', 'Robbery', '2023-10-25', '2023-10-28', ''),
(23, 'Maui', 'Mama', 'Moreno', 20, 'M', 'nambugbog', '2023-10-03', '2023-10-30', ''),
(24, 'Cloe', 'Idk', 'Galicinao', 23, 'F', 'nambugbog', '2023-10-08', '2023-10-09', '');

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `visitor_id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `inmate_id` int(11) DEFAULT NULL,
  `visit_datetime` datetime DEFAULT NULL,
  `purpose` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor`
--

INSERT INTO `visitor` (`visitor_id`, `visitor_name`, `inmate_id`, `visit_datetime`, `purpose`) VALUES
(1, 'John', 21, '2024-05-25 12:04:05', 'Talk');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `admitted`
--
ALTER TABLE `admitted`
  ADD PRIMARY KEY (`con_id`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`visitor_id`),
  ADD KEY `inmate_id` (`inmate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admitted`
--
ALTER TABLE `admitted`
  MODIFY `con_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `visitor`
--
ALTER TABLE `visitor`
  MODIFY `visitor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `visitor`
--
ALTER TABLE `visitor`
  ADD CONSTRAINT `visitor_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `admitted` (`con_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
