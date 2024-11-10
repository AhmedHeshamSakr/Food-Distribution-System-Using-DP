-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 05, 2024 at 11:04 AM
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
-- Database: `FDS`
--

-- --------------------------------------------------------

--
-- Table structure for table `Address`
--

CREATE TABLE `Address` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` enum('Country','State','City','Neighborhood') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Badge`
--

CREATE TABLE `Badge` (
  `badgeID` int(11) NOT NULL,
  `badgeName` varchar(255) DEFAULT NULL,
  `badgeLvl` int(11) DEFAULT NULL,
  `expiryDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Cooking`
--

CREATE TABLE `Cooking` (
  `cookID` int(11) NOT NULL,
  `mealID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Coordinating`
--

CREATE TABLE `Coordinating` (
  `userID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Delivering`
--

CREATE TABLE `Delivering` (
  `deliveryGuyID` int(11) NOT NULL,
  `deliveryID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Delivery`
--

CREATE TABLE `Delivery` (
  `deliveryID` int(11) NOT NULL,
  `deliveryDate` date DEFAULT NULL,
  `startLocation` int(11) DEFAULT NULL,
  `endLocation` int(11) DEFAULT NULL,
  `deliveryGuy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `DeliveryGuy`
--

CREATE TABLE `DeliveryGuy` (
  `userID` int(11) NOT NULL,
  `vehicleID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Donor`
--

CREATE TABLE `Donor` (
  `userID` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `paymentMethod` int(11) DEFAULT NULL,
  `donationAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Event`
--

CREATE TABLE `Event` (
  `eventID` int(11) NOT NULL,
  `eventDate` date DEFAULT NULL,
  `eventLocation` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Login`
--

CREATE TABLE `Login` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Meal`
--

CREATE TABLE `Meal` (
  `mealID` int(11) NOT NULL,
  `needOfDelevery` tinyint(1) DEFAULT NULL,
  `nOFMeals` int(11) DEFAULT NULL,
  `mealDescription` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Person`
--

CREATE TABLE `Person` (
  `userID` int(11) NOT NULL,
  `userTypeID` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNo` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Report`
--

CREATE TABLE `Report` (
  `reportID` int(11) NOT NULL,
  `personName` varchar(255) DEFAULT NULL,
  `personAddress` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phoneNo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Reporting`
--

CREATE TABLE `Reporting` (
  `userID` int(11) NOT NULL,
  `reportID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `UserBadge`
--

CREATE TABLE `UserBadge` (
  `userID` int(11) NOT NULL,
  `badgeID` int(11) NOT NULL,
  `dateAwarded` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Vehicle`
--

CREATE TABLE `Vehicle` (
  `vehicleID` int(11) NOT NULL,
  `licensePlateNo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Volunteer`
--

CREATE TABLE `Volunteer` (
  `userID` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `badge` int(11) DEFAULT NULL,
  `nationalID` varchar(255) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Volunteering`
--

CREATE TABLE `Volunteering` (
  `userID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Address`
--
ALTER TABLE `Address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `Badge`
--
ALTER TABLE `Badge`
  ADD PRIMARY KEY (`badgeID`);

--
-- Indexes for table `Cooking`
--
ALTER TABLE `Cooking`
  ADD PRIMARY KEY (`cookID`,`mealID`),
  ADD KEY `mealID` (`mealID`);

--
-- Indexes for table `Coordinating`
--
ALTER TABLE `Coordinating`
  ADD PRIMARY KEY (`eventID`,`userID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `Delivering`
--
ALTER TABLE `Delivering`
  ADD PRIMARY KEY (`deliveryGuyID`,`deliveryID`),
  ADD KEY `deliveryID` (`deliveryID`);

--
-- Indexes for table `Delivery`
--
ALTER TABLE `Delivery`
  ADD PRIMARY KEY (`deliveryID`);

--
-- Indexes for table `DeliveryGuy`
--
ALTER TABLE `DeliveryGuy`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `vehicleID` (`vehicleID`);

--
-- Indexes for table `Donor`
--
ALTER TABLE `Donor`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `Event`
--
ALTER TABLE `Event`
  ADD PRIMARY KEY (`eventID`);

--
-- Indexes for table `Login`
--
ALTER TABLE `Login`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `Meal`
--
ALTER TABLE `Meal`
  ADD PRIMARY KEY (`mealID`);

--
-- Indexes for table `Person`
--
ALTER TABLE `Person`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `Report`
--
ALTER TABLE `Report`
  ADD PRIMARY KEY (`reportID`);

--
-- Indexes for table `Reporting`
--
ALTER TABLE `Reporting`
  ADD PRIMARY KEY (`userID`,`reportID`),
  ADD KEY `reportID` (`reportID`);

--
-- Indexes for table `UserBadge`
--
ALTER TABLE `UserBadge`
  ADD PRIMARY KEY (`userID`,`badgeID`),
  ADD KEY `badgeID` (`badgeID`);

--
-- Indexes for table `Vehicle`
--
ALTER TABLE `Vehicle`
  ADD PRIMARY KEY (`vehicleID`),
  ADD UNIQUE KEY `licensePlateNo` (`licensePlateNo`);

--
-- Indexes for table `Volunteer`
--
ALTER TABLE `Volunteer`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `Volunteering`
--
ALTER TABLE `Volunteering`
  ADD PRIMARY KEY (`eventID`,`userID`),
  ADD KEY `userID` (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Address`
--
ALTER TABLE `Address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Badge`
--
ALTER TABLE `Badge`
  MODIFY `badgeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Delivery`
--
ALTER TABLE `Delivery`
  MODIFY `deliveryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Event`
--
ALTER TABLE `Event`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Meal`
--
ALTER TABLE `Meal`
  MODIFY `mealID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Person`
--
ALTER TABLE `Person`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Vehicle`
--
ALTER TABLE `Vehicle`
  MODIFY `vehicleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Address`
--
ALTER TABLE `Address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `Address` (`id`);

--
-- Constraints for table `Cooking`
--
ALTER TABLE `Cooking`
  ADD CONSTRAINT `cooking_ibfk_1` FOREIGN KEY (`cookID`) REFERENCES `Volunteer` (`userID`),
  ADD CONSTRAINT `cooking_ibfk_2` FOREIGN KEY (`mealID`) REFERENCES `Meal` (`mealID`);

--
-- Constraints for table `Coordinating`
--
ALTER TABLE `Coordinating`
  ADD CONSTRAINT `coordinating_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Person` (`userID`),
  ADD CONSTRAINT `coordinating_ibfk_2` FOREIGN KEY (`eventID`) REFERENCES `Event` (`eventID`);

--
-- Constraints for table `Delivering`
--
ALTER TABLE `Delivering`
  ADD CONSTRAINT `delivering_ibfk_1` FOREIGN KEY (`deliveryGuyID`) REFERENCES `Volunteer` (`userID`),
  ADD CONSTRAINT `delivering_ibfk_2` FOREIGN KEY (`deliveryID`) REFERENCES `Delivery` (`deliveryID`);

--
-- Constraints for table `DeliveryGuy`
--
ALTER TABLE `DeliveryGuy`
  ADD CONSTRAINT `deliveryguy_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Volunteer` (`userID`),
  ADD CONSTRAINT `deliveryguy_ibfk_2` FOREIGN KEY (`vehicleID`) REFERENCES `Vehicle` (`vehicleID`);

--
-- Constraints for table `Donor`
--
ALTER TABLE `Donor`
  ADD CONSTRAINT `donor_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Person` (`userID`);

--
-- Constraints for table `Login`
--
ALTER TABLE `Login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`email`) REFERENCES `Person` (`email`);

--
-- Constraints for table `Reporting`
--
ALTER TABLE `Reporting`
  ADD CONSTRAINT `reporting_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Person` (`userID`),
  ADD CONSTRAINT `reporting_ibfk_2` FOREIGN KEY (`reportID`) REFERENCES `Report` (`reportID`);

--
-- Constraints for table `UserBadge`
--
ALTER TABLE `UserBadge`
  ADD CONSTRAINT `userbadge_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Person` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `userbadge_ibfk_2` FOREIGN KEY (`badgeID`) REFERENCES `Badge` (`badgeID`) ON DELETE CASCADE;

--
-- Constraints for table `Volunteer`
--
ALTER TABLE `Volunteer`
  ADD CONSTRAINT `volunteer_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Person` (`userID`);

--
-- Constraints for table `Volunteering`
--
ALTER TABLE `Volunteering`
  ADD CONSTRAINT `volunteering_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Volunteer` (`userID`),
  ADD CONSTRAINT `volunteering_ibfk_2` FOREIGN KEY (`eventID`) REFERENCES `Event` (`eventID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
