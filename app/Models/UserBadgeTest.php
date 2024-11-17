<?php

require_once __DIR__ . "/../../config/DB.php"; // Assuming your DB connection file
require_once 'UserBadge.php';
require_once 'Login.php'; // Assuming Login class is defined in this file
require_once 'User.php';
require_once 'Badges.php';

class TestUser extends Person {
    public function __construct(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo) {
        // Create a mock login instance for testing
        $login = new withEmail($email, 'password');
        
        // Pass the login instance to the parent constructor
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo);

        // Add this user to the `volunteer` table
        $this->addToVolunteer();
    }

    public function getUserID(): int {
        return 101; // Static ID for testing purposes
    }

    private function addToVolunteer() {
        $userID = $this->getUserID();
        $db = Database::getInstance()->getConnection();

        // Check if user already exists in the volunteer table
        $query = "SELECT userID FROM Volunteer WHERE userID = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 0) {
            // Insert into volunteer table
            $insertQuery = "INSERT INTO Volunteer (userID, address, badge, nationalID) VALUES (?, 'Test Address', 1, '123456789')";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bind_param('i', $userID);
            $insertStmt->execute();
        }

        $stmt->close();
    }
}




class UserBadgeTest
{
    public function runTests()
    {
        // Test UserBadge Creation
        echo "<h3>Testing UserBadge Creation</h3>";
        $userBadge = new UserBadge(4, 1, '2024-11-13', '2025-11-13');
        $createResult = $userBadge->create(); // Create method in UserBadge
        echo $createResult ? "UserBadge created successfully.<br>" : "Failed to create UserBadge.<br>";

        // Test Read (Fetching a badge)
        echo "<h3>Testing Read</h3>";
        $fetchedBadge = UserBadge::read(4, 1); // Read method in UserBadge
        if ($fetchedBadge) {
            echo "User Badge Details:<br>";
            echo "UserID: " . $fetchedBadge->getUserID() . "<br>";
            echo "BadgeID: " . $fetchedBadge->getBadgeID() . "<br>";
            echo "Date Awarded: " . $fetchedBadge->getDateAwarded() . "<br>";
            echo "Expiry Date: " . $fetchedBadge->getExpiryDate() . "<br>";
        } else {
            echo "User Badge not found.<br>";
        }

        // Test Update (Updating the award date)
        echo "<h3>Testing Update</h3>";
        $userBadge->setDateAwarded('2024-12-01'); // Changing the award date for the test
        $updateResult = $userBadge->update(); // Update method in UserBadge
        echo $updateResult ? "UserBadge updated successfully.<br>" : "Failed to update UserBadge.<br>";

        // Test Read again after update
        echo "<h3>Testing Read After Update</h3>";
        $updatedBadge = UserBadge::read(4, 1); // Read again after update
        if ($updatedBadge) {
            echo "Updated User Badge Details:<br>";
            echo "UserID: " . $updatedBadge->getUserID() . "<br>";
            echo "BadgeID: " . $updatedBadge->getBadgeID() . "<br>";
            echo "Date Awarded: " . $updatedBadge->getDateAwarded() . "<br>";
            echo "Expiry Date: " . $updatedBadge->getExpiryDate() . "<br>";
        } else {
            echo "User Badge not found after update.<br>";
        }

        // Test Delete (Deleting the badge)
        echo "<h3>Testing Delete</h3>";
        $deleteResult = $userBadge->delete(); // Delete method in UserBadge
        echo $deleteResult ? "UserBadge deleted successfully.<br>" : "Failed to delete UserBadge.<br>";

        // Test Read after deletion
        echo "<h3>Testing Read After Deletion</h3>";
        $deletedBadge = UserBadge::read(4, 1); // Read again after delete
        if ($deletedBadge) {
            echo "User Badge still exists after deletion!<br>";
        } else {
            echo "User Badge successfully deleted.<br>";
        }
    }
}

// Instantiate the test class and run the tests
$test = new UserBadgeTest();
$test->runTests();

?>
