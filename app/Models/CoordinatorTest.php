<?php

// Include necessary files
require_once __DIR__ . "/../../config/DB.php";
require_once 'User.php';
require_once 'Volunteer.php';
require_once 'Coordinator.php';




class TestUser extends Person {
    public function __construct(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo) {
        // Create a mock login instance for testing
        $login = new withEmail($email, 'password');
        
        // Pass the login instance to the parent constructor
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo);

        // Add this user to the `volunteer` table
        $this->ensureUserIsVolunteer();
    }

    public function getUserID(): int {
        return 101; // Static ID for testing purposes
    }

    private function ensureUserIsVolunteer() {
        $userID = $this->getUserID();
        $db = Database::getInstance()->getConnection();

        // Check if the user already exists in the volunteer table
        $checkQuery = "SELECT userID FROM Volunteer WHERE userID = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 0) {
            // Insert the user into the volunteer table
            $insertQuery = "INSERT INTO Volunteer (userID, address, badge, nationalID) VALUES (?, 'Test Address', 1, '123456789')";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bind_param('i', $userID);
            $insertStmt->execute();
        }

        $stmt->close();
    }
}

echo "---- TESTING COORDINATOR CLASS WITH TestUser ----\n";

// Step 1: Create a TestUser Object
echo "\n[Step 1] Creating TestUser Object...\n";
$testUser = new TestUser(2, "Jane", "Doe", "jane.doe@example.com", "0987654321");

if ($testUser) {
    echo "TestUser Created: \n";
    echo "ID: " . $testUser->getUserID() . "\n";
    echo "Name: " . $testUser->getFirstName() . " " . $testUser->getLastName() . "\n";
    echo "Email: " . $testUser->getEmail() . "\n";
} else {
    echo "Failed to create TestUser.\n";
}

// Step 2: Create a Coordinator Object using the TestUser
echo "\n[Step 2] Creating Coordinator Object...\n";
$coordinator = new Coordinator($testUser);

if ($coordinator) {
    echo "Coordinator Created with User ID: " . $testUser->getUserID() . "\n";
} else {
    echo "Failed to create Coordinator.\n";
}

// Step 3: Assign the Coordinator to an Event
echo "\n[Step 3] Assigning Coordinator to Event (Event ID: 5)...\n";

try {
    $assignResult = $coordinator->assignCoordinatorToEvent(5);
    if ($assignResult) {
        echo "Coordinator successfully assigned to Event ID 5.\n";
    }
} catch (Exception $e) {
    echo "Error assigning coordinator to event: " . $e->getMessage() . "\n";
}

// Step 4: Retrieve Assigned Events
echo "\n[Step 4] Retrieving Assigned Events...\n";
$assignedEvents = $coordinator->getAssignedEvents();

if (!empty($assignedEvents)) {
    echo "Assigned Events:\n";
    foreach ($assignedEvents as $event) {
        echo "- Event ID: " . $event['eventID'] . ", Name: " . $event['name'] . ", Date: " . $event['eventDate'] . "\n";
    }
} else {
    echo "No events assigned to this coordinator.\n";
}

// Step 5: Fetch Details of a Specific Event
echo "\n[Step 5] Fetching Details for Event ID: 5...\n";
$eventDetails = $coordinator->getEventDetails(5);

if ($eventDetails) {
    echo "Event Details:\n";
    echo "ID: " . $eventDetails['eventID'] . "\n";
    echo "Name: " . $eventDetails['name'] . "\n";
    echo "Date: " . $eventDetails['eventDate'] . "\n";
    echo "Description: " . $eventDetails['eventDescription'] . "\n";
} else {
    echo "No details found for Event ID 5.\n";
}

echo "\n---- TESTING COMPLETE ----\n";

