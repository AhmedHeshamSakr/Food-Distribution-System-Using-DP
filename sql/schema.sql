-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 16, 2024 at 08:30 PM
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
-- Table structure for table `Badge`
--

CREATE TABLE `Badge` (
  `badgeID` int(11) NOT NULL,
  `badgeLvl` enum('Bronze Tier','Silver Tier','Gold Tier','Platinum Tier') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Cooking`
--

CREATE TABLE `Cooking` (
  `cookID` int(11) NOT NULL,
  `mealID` int(11) NOT NULL,
  `mealsTaken` int(11) DEFAULT NULL,
  `mealsCompleted` int(11) DEFAULT NULL
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
  `deliveryGuy` int(11) DEFAULT NULL,
  `status` enum('pending','delivering','delivered') DEFAULT 'pending',
  `deliveryDetails` text DEFAULT NULL
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
-- Table structure for table `Donating`
--

CREATE TABLE `Donating` (
  `userID` int(11) NOT NULL,
  `donationID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Donating`
--

INSERT INTO `Donating` (`userID`, `donationID`) VALUES
(30, 59),
(31, 60),
(33, 61),
(34, 62),
(35, 63);

-- --------------------------------------------------------

--
-- Table structure for table `Donation`
--

CREATE TABLE `Donation` (
  `donationID` int(11) NOT NULL,
  `donationDate` date NOT NULL,
  `donationAmount` decimal(10,2) NOT NULL,
  `paymentMethod` enum('Fawry','Credit Card','Visa') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Donation`
--

INSERT INTO `Donation` (`donationID`, `donationDate`, `donationAmount`, `paymentMethod`) VALUES
(59, '2024-11-13', 150.75, 'Fawry'),
(60, '2024-11-13', 150.75, 'Fawry'),
(61, '2024-11-16', 150.75, 'Fawry'),
(62, '2024-11-16', 150.75, 'Fawry'),
(63, '2024-11-16', 150.75, 'Fawry');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `eventID` int(11) NOT NULL,
  `eventDate` date DEFAULT NULL,
  `eventLocation` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `eventDescription` text DEFAULT NULL
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
-- Table structure for table `Meal`
--

CREATE TABLE `Meal` (
  `mealID` int(11) NOT NULL,
  `needOfDelivery` tinyint(1) DEFAULT NULL,
  `nOFMeals` int(11) DEFAULT NULL,
  `remainingMeals` int(11) DEFAULT NULL,
  `mealDescription` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Meal`
--

INSERT INTO `Meal` (`mealID`, `needOfDelivery`, `nOFMeals`, `remainingMeals`, `mealDescription`) VALUES
(501, 1, 20, 20, 'Healthy Veggie Meals'),
(502, 1, 20, 20, 'Healthy Veggie Meals'),
(503, 1, 20, 20, 'Healthy Veggie Meals');

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
  `phoneNo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`userID`, `userTypeID`, `firstName`, `lastName`, `email`, `phoneNo`) VALUES
(30, 5, 'Johnny', 'DoeUpdated', 'johnny.doe@example.com', '0987654321'),
(31, 5, 'Johnny', 'DoeUpdated', 'johnny.doe@example.com', '0987654321'),
(32, 1, 'John', 'Doe', 'johndoe@example.com', '1234567890'),
(33, 0, 'Johnny', 'DoeUpdated', 'johnny.doe@example.com', '0987654321'),
(34, 5, 'Johnny', 'DoeUpdated', 'johnny.doe@example.com', '0987654321'),
(35, 5, 'Johnny', 'DoeUpdated', 'johnny.doe@example.com', '0987654321'),
(36, 1, 'John', 'Doe', 'john.doe@example.com', '1234567890');

-- --------------------------------------------------------

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
(24, 'Jane Doe', '456 Another St', '5559876543', 'Acknowledged', 'Description for a test report', 1, 1),
(25, 'Jane Doe', '456 Another St', '5559876543', 'Pending', 'Description for a test report', 0, 0);

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`reportID`, `personINname`, `personINaddress`, `phoneINno`, `status`, `description`, `is_deleted`, `recognized`) VALUES
(24, 'Jane Doe', '456 Another St', '5559876543', 'Acknowledged', 'Description for a test report', 1, 1),
(25, 'Jane Doe', '456 Another St', '5559876543', 'Pending', 'Description for a test report', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reporting`
--

CREATE TABLE `reporting` (
  `userID` int(11) NOT NULL,
  `reportID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reporting`
--

INSERT INTO `reporting` (`userID`, `reportID`, `created_at`, `updated_at`, `is_deleted`) VALUES
(5, 24, '2024-11-17 12:43:06', '2024-11-17 12:43:06', 0),
(6, 25, '2024-11-17 12:51:12', '2024-11-17 12:51:12', 0);
-- --------------------------------------------------------

--
-- Table structure for table `userbadge`
--

CREATE TABLE `userbadge` (
  `userID` int(11) NOT NULL,
  `badgeID` int(11) NOT NULL,
  `dateAwarded` date DEFAULT NULL,
  `expiryDate` date DEFAULT NULL
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
  `badge` int(11) DEFAULT NULL,
  `nationalID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteering`
--

CREATE TABLE `volunteering` (
  `userID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_number` varchar(50) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_price` float(10,2) DEFAULT NULL,
  `item_price_currency` varchar(10) DEFAULT NULL,
  `payer_id` varchar(50) DEFAULT NULL,
  `payer_name` varchar(50) DEFAULT NULL,
  `payer_email` varchar(50) DEFAULT NULL,
  `payer_country` varchar(20) DEFAULT NULL,
  `merchant_id` varchar(255) DEFAULT NULL,
  `merchant_email` varchar(50) DEFAULT NULL,
  `order_id` varchar(50) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `paid_amount` float(10,2) NOT NULL,
  `paid_amount_currency` varchar(10) NOT NULL,
  `payment_source` varchar(50) DEFAULT NULL,
  `payment_status` varchar(25) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
-- Indexes for table `Donating`
--
ALTER TABLE `Donating`
  ADD PRIMARY KEY (`userID`,`donationID`),
  ADD KEY `donationID` (`donationID`);

--
-- Indexes for table `Donation`
--
ALTER TABLE `Donation`
  ADD PRIMARY KEY (`donationID`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `event_ibfk_1` (`eventLocation`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `Meal`
--
ALTER TABLE `Meal`
  ADD PRIMARY KEY (`mealID`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`userID`);

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
  ADD PRIMARY KEY (`userID`),
  ADD KEY `volunteer_ibfk_1` (`badge`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `Badge`
--
ALTER TABLE `Badge`
  MODIFY `badgeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `deliveryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Donation`
--
ALTER TABLE `Donation`
  MODIFY `donationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=302;

--
-- AUTO_INCREMENT for table `Meal`
--
ALTER TABLE `Meal`
  MODIFY `mealID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=504;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicleID` int(11) NOT NULL AUTO_INCREMENT;


--
-- Constraints for table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `address` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Cooking`
--
ALTER TABLE `Cooking`
  ADD CONSTRAINT `cooking_ibfk_1` FOREIGN KEY (`cookID`) REFERENCES `Volunteer` (`userID`),
  ADD CONSTRAINT `cooking_ibfk_2` FOREIGN KEY (`mealID`) REFERENCES `Meal` (`mealID`);

--
-- Constraints for table `coordinating`
--
ALTER TABLE `coordinating`
  ADD CONSTRAINT `coordinating_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`),
  ADD CONSTRAINT `coordinating_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `volunteer` (`userID`);

--
-- Constraints for table `delivering`
--
ALTER TABLE `delivering`
  ADD CONSTRAINT `delivering_ibfk_1` FOREIGN KEY (`deliveryID`) REFERENCES `delivery` (`deliveryID`),
  ADD CONSTRAINT `delivering_ibfk_2` FOREIGN KEY (`deliveryGuyID`) REFERENCES `deliveryguy` (`userID`);

--
-- Constraints for table `Donating`
--
ALTER TABLE `Donating`
  ADD CONSTRAINT `donating_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `person` (`userID`),
  ADD CONSTRAINT `donating_ibfk_2` FOREIGN KEY (`donationID`) REFERENCES `Donation` (`donationID`);

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`eventLocation`) REFERENCES `address` (`id`);


--
-- Constraints for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD CONSTRAINT `volunteer_ibfk_1` FOREIGN KEY (`badge`) REFERENCES `badge` (`badgeID`);

--
-- Constraints for table `volunteering`
--
ALTER TABLE `volunteering`
  ADD CONSTRAINT `volunteering_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`),
  ADD CONSTRAINT `volunteering_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `volunteer` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
