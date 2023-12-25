-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 25, 2023 at 08:45 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dtt_exam`
--

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

CREATE TABLE `facility` (
  `facility_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `creation_date` date NOT NULL,
  `location_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `facility`
--

INSERT INTO `facility` (`facility_id`, `name`, `creation_date`, `location_id`) VALUES
(5, 'Sydney Facility', '2023-12-19', 1),
(6, 'Helsinki Facility', '2023-12-19', 3),
(7, 'Amsterdam Facility', '2023-12-18', 2),
(8, 'Lappeenranta Facility Updated', '2023-12-25', 2);

-- --------------------------------------------------------

--
-- Table structure for table `facilitytags`
--

CREATE TABLE `facilitytags` (
  `facility_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `facilitytags`
--

INSERT INTO `facilitytags` (`facility_id`, `tag_id`) VALUES
(5, 3),
(6, 1),
(6, 2),
(6, 3),
(7, 2),
(7, 3),
(8, 2),
(8, 3);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `phone_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `city`, `address`, `zip_code`, `country_code`, `phone_number`) VALUES
(1, 'Sydney', '575 Sydney, Barkley Road', '1655', 'AUS', '01950498055'),
(2, 'Amasterdam', '36 Amsterdam Road', '1884', 'NED', '01974684658'),
(3, 'Helsinki', '5 C Punkerikatu', '58380', 'FIN', '01465789544');

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE `tag` (
  `tag_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`tag_id`, `name`) VALUES
(2, 'Business'),
(3, 'Finance'),
(1, 'IT');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `facility`
--
ALTER TABLE `facility`
  ADD PRIMARY KEY (`facility_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `facilitytags`
--
ALTER TABLE `facilitytags`
  ADD PRIMARY KEY (`facility_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `facility`
--
ALTER TABLE `facility`
  MODIFY `facility_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `facility`
--
ALTER TABLE `facility`
  ADD CONSTRAINT `facility_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`);

--
-- Constraints for table `facilitytags`
--
ALTER TABLE `facilitytags`
  ADD CONSTRAINT `facilitytags_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `facility` (`facility_id`),
  ADD CONSTRAINT `facilitytags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
