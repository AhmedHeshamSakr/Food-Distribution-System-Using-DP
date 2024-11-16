
--TRY only if schema.sql didnt work becouse propably it will have some 
--erros and u will have to fix them :)



CREATE TABLE `person` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,  -- Ensure auto-increment
  `userTypeID` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNo` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `email` (`email`)  -- Unique email for each user
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--  Create the Login table
CREATE TABLE Login (
    email VARCHAR(255) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY (email) REFERENCES Person(email)
);


-- Create the Reporting table
CREATE TABLE Reporting (
    userID INT,
    reportID INT,
    is_deleted TINYINT(1) DEFAULT 0,  -- Change BOOLEAN to TINYINT(1)
    PRIMARY KEY (userID, reportID),
    FOREIGN KEY (userID) REFERENCES Person(userID),
    FOREIGN KEY (reportID) REFERENCES Report(reportID),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create the ReportingData table
CREATE TABLE Report (
    reportID INT PRIMARY KEY AUTO_INCREMENT,  -- Add AUTO_INCREMENT to reportID
    personInName VARCHAR(255),
    personInAddress VARCHAR(255),
    personInPhone VARCHAR(20),
    status ENUM('Pending', 'Acknowledged', 'In Progress', 'Completed') DEFAULT 'Pending',
    recognized TINYINT(1) DEFAULT 0,  -- Change BOOLEAN to TINYINT(1)
    description TEXT,
    is_deleted TINYINT(1) DEFAULT 0  -- Change BOOLEAN to TINYINT(1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `volunteer` (
  `userID` int(11) NOT NULL,  -- References person table
  `badge` int(11) DEFAULT NULL,
  PRIMARY KEY (`userID`),  -- Makes userID the primary key here as well
  CONSTRAINT `volunteer_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `person` (`userID`)  -- Foreign Key constraint
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Create the Badge table
CREATE TABLE Badge (
    badgeID INT PRIMARY KEY AUTO_INCREMENT,
    expiryDate DATE,
    badgeLvl ENUM('Bronze Tier', 'Silver Tier', 'Gold Tier', 'Platinum Tier') NOT NULL;
);

-- Create the Vehicle table
CREATE TABLE Vehicle (
    vehicleID INT PRIMARY KEY AUTO_INCREMENT,
    licensePlateNo VARCHAR(20) UNIQUE
);



-- Create the Address table
CREATE TABLE Address (
    addressID INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    city VARCHAR(100),
    postalCode INT,
    state VARCHAR(100),
    street VARCHAR(255)
);

CREATE TABLE Meal (
    mealID INT PRIMARY KEY AUTO_INCREMENT,
    needOfDelivery BOOLEAN,
    nOFMeals INT, -- Total meals needed
    remainingMeals INT, -- Remaining meals to be completed
    mealDescription TEXT
);

CREATE TABLE Cooking (
    cookID INT,
    mealID INT,
    mealsTaken INT,
    mealsCompleted INT,
    PRIMARY KEY (cookID, mealID),
    FOREIGN KEY (cookID) REFERENCES Volunteer(userID),
    FOREIGN KEY (mealID) REFERENCES Meal(mealID)
);



-- Create the Donation table with paymentMethod as ENUM
CREATE TABLE Donation (
    donationID INT PRIMARY KEY AUTO_INCREMENT,
    donationDate DATE,
    donationAmount DECIMAL(10, 2),
    paymentMethod ENUM('Cash', 'Credit Card', 'Bank Transfer', 'Online Payment'),
);

-- Create the Donating table (Many-to-Many relationship)
CREATE TABLE Donating (
    donorID INT,
    donationID INT,
    PRIMARY KEY (donorID, donationID),
    FOREIGN KEY (donationID) REFERENCES Donation(donationID)
);

-- Create the DeliveryGuy table
CREATE TABLE DeliveryGuy (
    userID INT PRIMARY KEY,
    vehicleID INT,
    FOREIGN KEY (userID) REFERENCES Volunteer (userID),
    FOREIGN KEY (vehicleID) REFERENCES Vehicle(vehicleID)
);



    CREATE TABLE Meal (
    mealID INT PRIMARY KEY AUTO_INCREMENT,
    needOfDelevery BOOLEAN,
    nOFMeals INT,
    mealDescription TEXT
);

    CREATE TABLE Cooking (
    cookID INT PRIMARY KEY,
    mealID INT,
    PRIMARY KEY (cookID ,mealID),
    FOREIGN KEY (cookID) REFERENCES Volunteer(userID),
    FOREIGN KEY (mealID) REFERENCES Meal(mealID)
);



-- Create the Donation table with paymentMethod as ENUM
CREATE TABLE Donation (
    donationID INT PRIMARY KEY AUTO_INCREMENT,
    donationDate DATE,
    donationAmount DECIMAL(10, 2),
    paymentMethod ENUM('Cash', 'Credit Card', 'Bank Transfer', 'Online Payment'),
);

-- Create the Donating table (Many-to-Many relationship)
CREATE TABLE Donating (
    donorID INT,
    donationID INT,
    PRIMARY KEY (donorID, donationID),
    FOREIGN KEY (donationID) REFERENCES Donation(donationID)
);

-- Create the DeliveryGuy table
CREATE TABLE DeliveryGuy (
    userID INT PRIMARY KEY,
    vehicleID INT,
    FOREIGN KEY (userID) REFERENCES Volunteer (userID),
    FOREIGN KEY (vehicleID) REFERENCES Vehicle(vehicleID)
);


-- Create the Delivery table
CREATE TABLE Delivery (
    deliveryID INT PRIMARY KEY AUTO_INCREMENT,
    deliveryDate DATE,
    startLocation INT,
    endLocation INT,
    deliveryGuy INT,
);
-- Create the Delivering table
CREATE TABLE Delivering (
    deliveryGuyID INT PRIMARY KEY,
    deliveryID INT,
    PRIMARY KEY (deliveryGuyID ,deliveryID),
    FOREIGN KEY (deliveryGuyID) REFERENCES Volunteer(userID),
    FOREIGN KEY (deliveryID) REFERENCES Delivery(deliveryID)
);



CREATE TABLE Coordinating (
    userID INT ,
    eventID INT,
    PRIMARY KEY (eventID ,userID ),
    FOREIGN KEY (userID) REFERENCES Person(userID),
    FOREIGN KEY (eventID) REFERENCES Events(eventID)

);

    CREATE TABLE Event (
    eventID  INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    eventDate dATE,
    eventLocation INt,
    eventDescription TEXT
);


CREATE TABLE Volunteering (
    userID INT,
    eventID INT,
    PRIMARY KEY (eventID ,userID ),
    FOREIGN KEY (userID) REFERENCES Person(userID),
    FOREIGN KEY (eventID) REFERENCES Events(eventID)

);



