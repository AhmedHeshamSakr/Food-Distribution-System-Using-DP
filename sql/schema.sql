-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2024 at 11:30 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fds`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` enum('Country','State','City','Neighborhood') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `badge`
--

CREATE TABLE `badge` (
  `badgeID` int(11) NOT NULL,
  `badgeName` varchar(255) DEFAULT NULL,
  `badgeLvl` int(11) DEFAULT NULL,
  `expiryDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cooking`
--

CREATE TABLE `cooking` (
  `cookID` int(11) NOT NULL,
  `mealID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coordinating`
--

CREATE TABLE `coordinating` (
  `userID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivering`
--

CREATE TABLE `delivering` (
  `deliveryGuyID` int(11) NOT NULL,
  `deliveryID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `deliveryID` int(11) NOT NULL,
  `deliveryDate` date DEFAULT NULL,
  `startLocation` int(11) DEFAULT NULL,
  `endLocation` int(11) DEFAULT NULL,
  `deliveryGuy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliveryguy`
--

CREATE TABLE `deliveryguy` (
  `userID` int(11) NOT NULL,
  `vehicleID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donor`
--

CREATE TABLE `donor` (
  `userID` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `paymentMethod` int(11) DEFAULT NULL,
  `donationAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `eventID` int(11) NOT NULL,
  `eventDate` date DEFAULT NULL,
  `eventLocation` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meal`
--

CREATE TABLE `meal` (
  `mealID` int(11) NOT NULL,
  `needOfDelevery` tinyint(1) DEFAULT NULL,
  `nOFMeals` int(11) DEFAULT NULL,
  `mealDescription` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `userID` int(11) NOT NULL,
  `userTypeID` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNo` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `reportID` int(11) NOT NULL,
  `personINname` varchar(255) DEFAULT NULL,
  `personINaddress` varchar(255) DEFAULT NULL,
  `phoneINno` varchar(20) DEFAULT NULL,
  `status` enum('Pending','Acknowledged','In Progress','Completed') DEFAULT 'Pending',
  `description` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `recognized` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`reportID`, `personINname`, `personINaddress`, `phoneINno`, `status`, `description`, `is_deleted`, `recognized`) VALUES
(1, 'John Doe', '123 Main St', '5551234567', 'Acknowledged', 'This is a description of the report.', 1, 0),
(2, 'John Doe', '123 Main St', '5551234567', 'Acknowledged', 'This is a description of the report.', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reporting`
--

CREATE TABLE `reporting` (
  `userID` int(11) NOT NULL,
  `reportID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userbadge`
--

CREATE TABLE `userbadge` (
  `userID` int(11) NOT NULL,
  `badgeID` int(11) NOT NULL,
  `dateAwarded` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `vehicleID` int(11) NOT NULL,
  `licensePlateNo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

CREATE TABLE `volunteer` (
  `userID` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `badge` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteering`
--

CREATE TABLE `volunteering` (
  `userID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `badge`
--
ALTER TABLE `badge`
  ADD PRIMARY KEY (`badgeID`);

--
-- Indexes for table `cooking`
--
ALTER TABLE `cooking`
  ADD PRIMARY KEY (`cookID`,`mealID`),
  ADD KEY `mealID` (`mealID`);

--
-- Indexes for table `coordinating`
--
ALTER TABLE `coordinating`
  ADD PRIMARY KEY (`eventID`,`userID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `delivering`
--
ALTER TABLE `delivering`
  ADD PRIMARY KEY (`deliveryGuyID`,`deliveryID`),
  ADD KEY `deliveryID` (`deliveryID`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`deliveryID`);

--
-- Indexes for table `deliveryguy`
--
ALTER TABLE `deliveryguy`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `vehicleID` (`vehicleID`);

--
-- Indexes for table `donor`
--
ALTER TABLE `donor`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`eventID`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `meal`
--
ALTER TABLE `meal`
  ADD PRIMARY KEY (`mealID`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`reportID`);

--
-- Indexes for table `reporting`
--
ALTER TABLE `reporting`
  ADD PRIMARY KEY (`userID`,`reportID`),
  ADD KEY `reportID` (`reportID`);

--
-- Indexes for table `userbadge`
--
ALTER TABLE `userbadge`
  ADD PRIMARY KEY (`userID`,`badgeID`),
  ADD KEY `badgeID` (`badgeID`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicleID`),
  ADD UNIQUE KEY `licensePlateNo` (`licensePlateNo`);

--
-- Indexes for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `volunteering`
--
ALTER TABLE `volunteering`
  ADD PRIMARY KEY (`eventID`,`userID`),
  ADD KEY `userID` (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badge`
--
ALTER TABLE `badge`
  MODIFY `badgeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `deliveryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meal`
--
ALTER TABLE `meal`
  MODIFY `mealID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `address` (`id`);

--
-- Constraints for table `cooking`
--
ALTER TABLE `cooking`
  ADD CONSTRAINT `cooking_ibfk_1` FOREIGN KEY (`cookID`) REFERENCES `volunteer` (`userID`),
  ADD CONSTRAINT `cooking_ibfk_2` FOREIGN KEY (`mealID`) REFERENCES `meal` (`mealID`);

--
-- Constraints for table `coordinating`
--
ALTER TABLE `coordinating`
  ADD CONSTRAINT `coordinating_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `person` (`userID`),
  ADD CONSTRAINT `coordinating_ibfk_2` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`);

--
-- Constraints for table `delivering`
--
ALTER TABLE `delivering`
  ADD CONSTRAINT `delivering_ibfk_1` FOREIGN KEY (`deliveryGuyID`) REFERENCES `volunteer` (`userID`),
  ADD CONSTRAINT `delivering_ibfk_2` FOREIGN KEY (`deliveryID`) REFERENCES `delivery` (`deliveryID`);

--
-- Constraints for table `deliveryguy`
--
ALTER TABLE `deliveryguy`
  ADD CONSTRAINT `deliveryguy_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `volunteer` (`userID`),
  ADD CONSTRAINT `deliveryguy_ibfk_2` FOREIGN KEY (`vehicleID`) REFERENCES `vehicle` (`vehicleID`);

--
-- Constraints for table `donor`
--
ALTER TABLE `donor`
  ADD CONSTRAINT `donor_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `person` (`userID`);

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`email`) REFERENCES `person` (`email`);

--
-- Constraints for table `reporting`
--
ALTER TABLE `reporting`
  ADD CONSTRAINT `reporting_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `person` (`userID`),
  ADD CONSTRAINT `reporting_ibfk_2` FOREIGN KEY (`reportID`) REFERENCES `report` (`reportID`);

--
-- Constraints for table `userbadge`
--
ALTER TABLE `userbadge`
  ADD CONSTRAINT `userbadge_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `person` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `userbadge_ibfk_2` FOREIGN KEY (`badgeID`) REFERENCES `badge` (`badgeID`) ON DELETE CASCADE;

--
-- Constraints for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD CONSTRAINT `volunteer_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `person` (`userID`);

--
-- Constraints for table `volunteering`
--
ALTER TABLE `volunteering`
  ADD CONSTRAINT `volunteering_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `volunteer` (`userID`),
  ADD CONSTRAINT `volunteering_ibfk_2` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
