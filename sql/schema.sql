-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 12, 2024 at 10:54 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `FDS`

-- --------------------------------------------------------

-- Table structure for table `address`
CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` enum('Country','State','City','Neighborhood') NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table `badge`
CREATE TABLE `badge` (
  `badgeID` int(11) NOT NULL,
  `badgeName` varchar(255) DEFAULT NULL,
  `badgeLvl` int(11) DEFAULT NULL,
  `expiryDate` date DEFAULT NULL
);

-- --------------------------------------------------------

-- Table structure for table `Cooking`
CREATE TABLE `Cooking` (
  `cookID` int(11) NOT NULL,
  `mealID` int(11) NOT NULL,
  `mealsTaken` int(11) DEFAULT NULL,
  `mealsCompleted` int(11) DEFAULT NULL
);

-- --------------------------------------------------------

-- Table structure for table `coordinating`
CREATE TABLE `coordinating` (
  `userID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table `delivering`
CREATE TABLE `delivering` (
  `deliveryGuyID` int(11) NOT NULL,
  `deliveryID` int(11) NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table `delivery`
CREATE TABLE `delivery` (
  `deliveryID` int(11) NOT NULL,
  `deliveryDate` date DEFAULT NULL,
  `startLocation` int(11) DEFAULT NULL,
  `endLocation` int(11) DEFAULT NULL,
  `deliveryGuy` int(11) DEFAULT NULL,
  `status` enum('pending','delivering','delivered') DEFAULT 'pending',
  `deliveryDetails` text DEFAULT NULL
);

-- --------------------------------------------------------

-- Table structure for table `deliveryguy`
CREATE TABLE `deliveryguy` (
  `userID` int(11) NOT NULL,
  `vehicleID` int(11) DEFAULT NULL
);

-- --------------------------------------------------------

-- Table structure for table `Donating`
CREATE TABLE `Donating` (
  `userID` int(11) NOT NULL,
  `donationID` int(11) NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table `Donation`
CREATE TABLE `Donation` (
  `donationID` int(11) NOT NULL,
  `donationDate` date NOT NULL,
  `donationAmount` decimal(10,2) NOT NULL,
  `paymentMethod` enum('Cash','Credit Card','Bank Transfer','Online Payment') NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table `event`
CREATE TABLE `event` (
  `eventID` int(11) NOT NULL,
  `eventDate` date DEFAULT NULL,
  `eventLocation` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `eventDescription` text DEFAULT NULL
);

-- --------------------------------------------------------

-- Table structure for table `login`
CREATE TABLE `login` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table `Meal`
CREATE TABLE `Meal` (
  `mealID` int(11) NOT NULL,
  `needOfDelivery` tinyint(1) DEFAULT NULL,
  `nOFMeals` int(11) DEFAULT NULL,
  `remainingMeals` int(11) DEFAULT NULL,
  `mealDescription` text DEFAULT NULL
);

-- --------------------------------------------------------

-- Table structure for table `person`
CREATE TABLE `person` (
  `userID` int(11) NOT NULL,
  `userTypeID` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `report`
CREATE TABLE `report` (
  `reportID` int(11) NOT NULL,
  `personINname` varchar(255) DEFAULT NULL,
  `personINaddress` varchar(255) DEFAULT NULL,
  `phoneINno` varchar(20) DEFAULT NULL,
  `status` enum('Pending','Acknowledged','In Progress','Completed') DEFAULT 'Pending',
  `description` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `recognized` tinyint(1) DEFAULT 0
);

-- --------------------------------------------------------

-- Table structure for table `reporting`
CREATE TABLE `reporting` (
  `userID` int(11) NOT NULL,
  `reportID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
);

-- --------------------------------------------------------

-- Table structure for table `userbadge`
CREATE TABLE `userbadge` (
  `userID` int(11) NOT NULL,
  `badgeID` int(11) NOT NULL,
  `dateAwarded` date DEFAULT NULL
);

-- --------------------------------------------------------

-- Table structure for table `vehicle`
CREATE TABLE `vehicle` (
  `vehicleID` int(11) NOT NULL,
  `licensePlateNo` varchar(20) DEFAULT NULL
);

-- --------------------------------------------------------

-- Table structure for table `volunteer`
CREATE TABLE `volunteer` (
  `userID` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `badge` int(11) DEFAULT NULL,
  `nationalID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `volunteering`
CREATE TABLE `volunteering` (
  `userID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL
);

-- --------------------------------------------------------

-- Indexes for dumped tables
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

ALTER TABLE `badge`
  ADD PRIMARY KEY (`badgeID`);

ALTER TABLE `Cooking`
  ADD PRIMARY KEY (`cookID`,`mealID`),
  ADD KEY `mealID` (`mealID`);

ALTER TABLE `coordinating`
  ADD PRIMARY KEY (`eventID`,`userID`),
  ADD KEY `userID` (`userID`);

ALTER TABLE `delivering`
  ADD PRIMARY KEY (`deliveryGuyID`,`deliveryID`),
  ADD KEY `deliveryID` (`deliveryID`);

ALTER TABLE `delivery`
  ADD PRIMARY KEY (`deliveryID`);

ALTER TABLE `deliveryguy`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `vehicleID` (`vehicleID`);

ALTER TABLE `Donating`
  ADD PRIMARY KEY (`userID`,`donationID`),
  ADD KEY `donationID` (`donationID`);

ALTER TABLE `Donation`
  ADD PRIMARY KEY (`donationID`);

ALTER TABLE `event`
  ADD PRIMARY KEY (`eventID`);

ALTER TABLE `login`
  ADD PRIMARY KEY (`email`);

ALTER TABLE `Meal`
  ADD PRIMARY KEY (`mealID`);

ALTER TABLE `report`
  ADD PRIMARY KEY (`reportID`);

ALTER TABLE `reporting`
  ADD PRIMARY KEY (`userID`,`reportID`),
  ADD KEY `reportID` (`reportID`);

ALTER TABLE `userbadge`
  ADD PRIMARY KEY (`userID`,`badgeID`),
  ADD KEY `badgeID` (`badgeID`);

ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicleID`),
  ADD UNIQUE KEY `licensePlateNo` (`licensePlateNo`);

ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`userID`);

ALTER TABLE `volunteering`
  ADD PRIMARY KEY (`eventID`,`userID`),
  ADD KEY `userID` (`userID`);

-- AUTO_INCREMENT for dumped tables
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `badge`
  MODIFY `badgeID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `delivery`
  MODIFY `deliveryID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Donation`
  MODIFY `donationID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `event`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Meal`
  MODIFY `mealID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `person`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`userID`);


ALTER TABLE `report`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `vehicle`
  MODIFY `vehicleID` int(11) NOT NULL AUTO_INCREMENT;

-- Constraints for dumped tables
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `address` (`id`);

ALTER TABLE `Cooking`
  ADD CONSTRAINT `cooking_ibfk_1` FOREIGN KEY (`cookID`) REFERENCES `Volunteer` (`userID`),
  ADD CONSTRAINT `cooking_ibfk_2` FOREIGN KEY (`mealID`) REFERENCES `Meal` (`mealID`);

ALTER TABLE `coordinating`
  ADD CONSTRAINT `coordinating_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`),
  ADD CONSTRAINT `coordinating_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `volunteer` (`userID`);

ALTER TABLE `delivering`
  ADD CONSTRAINT `delivering_ibfk_1` FOREIGN KEY (`deliveryID`) REFERENCES `delivery` (`deliveryID`),
  ADD CONSTRAINT `delivering_ibfk_2` FOREIGN KEY (`deliveryGuyID`) REFERENCES `deliveryguy` (`userID`);

ALTER TABLE `Donating`
  ADD CONSTRAINT `donating_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `person` (`userID`),
  ADD CONSTRAINT `donating_ibfk_2` FOREIGN KEY (`donationID`) REFERENCES `Donation` (`donationID`);

ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`eventLocation`) REFERENCES `address` (`id`);

ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`reportID`) REFERENCES `reporting` (`reportID`);

ALTER TABLE `volunteer`
  ADD CONSTRAINT `volunteer_ibfk_1` FOREIGN KEY (`badge`) REFERENCES `badge` (`badgeID`);

ALTER TABLE `volunteering`
  ADD CONSTRAINT `volunteering_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`),
  ADD CONSTRAINT `volunteering_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `volunteer` (`userID`);

COMMIT;