<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required class files
require_once __DIR__ . "/../../config/DB.php";
require_once '#c-Meals.php';
require_once '#c-Cooking.php';
require_once '#c-Cook.php';
require_once 'Volunteer.php';
require_once 'User.php';
require_once 'Login.php'; // Login implementations

// Setup Database Connection (Assuming it's already configured)
$db = Database::getInstance();

// Helper function to execute raw queries for setup and teardown
function run_raw_query($query) {
    $conn = Database::getInstance()->getConnection();
    return mysqli_query($conn, $query);
}

// Clean up test data
function cleanup() {
    run_raw_query("DELETE FROM Cooking");
    run_raw_query("DELETE FROM Meal");
}

// Start Tests
echo "Running Tests...\n";

// Cleanup before starting tests
cleanup();

class TestUser extends User {
    public function __construct(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo) {
        // Create a mock login instance for testing
        $login = new withEmail($email, 'password');
        
        // Pass the login instance to the parent constructor
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);

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

// ** Test 1: Creating a Meal **
echo "Test 1: Creating a Meal\n";
$meal = new Meal(needOfDelivery: true, nOFMeals: 20, mealDescription: "Healthy Veggie Meals");
try {
    $createResult = $meal->CreateMeal();
    assert($createResult === true, "Failed to create meal");
    $mealID = $meal->getMealID();
    assert(!empty($mealID), "Meal ID should not be empty after creation");
} catch (Exception $e) {
    echo "Error in Test 1: " . $e->getMessage() . "\n";
}

// ** Test 2: Reading a Meal **
echo "Test 2: Reading a Meal\n";
try {
    $readMeal = Meal::ReadMeal($mealID);
    assert($readMeal !== null, "Failed to read the created meal");
    assert($readMeal->getRemainingMeals() === 20, "Meal count should be 20");
    assert($readMeal->getMealDescription() === "Healthy Veggie Meals", "Meal description mismatch");
} catch (Exception $e) {
    echo "Error in Test 2: " . $e->getMessage() . "\n";
}


// ** Test 3: Cook Taking Meals **
echo "Test 3: Cook Taking Meals\n";
$user = new TestUser(userTypeID: 0, firstName: "John", lastName: "Doe", email: "john.doe@example.com", phoneNo: "1234567890");
$cook = new Cook($user);
try {
    $takeMealsResult = $cook->takeMeals(mealID: $mealID, count: 5);
    assert($takeMealsResult === true, "Failed to take meals");
    $updatedMeal = Meal::ReadMeal($mealID);
    assert($updatedMeal->getRemainingMeals() === 15, "Remaining meals should be 15 after taking meals");
} catch (Exception $e) {
    echo "Error in Test 3: " . $e->getMessage() . "\n";
}

// ** Test 4: Cook Completing Meals **
echo "Test 4: Cook Completing Meals\n";
try {
    $completeMealsResult = $cook->completeMeals(mealID: $mealID, count: 5);
    assert($completeMealsResult === true, "Failed to complete meals");

    $cookingRecord = Cooking::getMealsByCook($user->getUserID())[0];
    assert($cookingRecord['mealsCompleted'] === 5, "Meals completed should be 5");
} catch (Exception $e) {
    echo "Error in Test 4: " . $e->getMessage() . "\n";
}

// ** Test 5: Getting Meals Assigned to Cook **
echo "Test 5: Getting Meals Assigned to Cook\n";
try {
    $mealsAssigned = $cook->getMealsAssigned();
    assert(!empty($mealsAssigned), "Meals assigned should not be empty");
    assert($mealsAssigned[0]['mealID'] === $mealID, "Assigned meal ID should match");
} catch (Exception $e) {
    echo "Error in Test 5: " . $e->getMessage() . "\n";
}

// ** Test 6: Cleanup After Tests **
echo "Test 6: Cleanup\n";
cleanup();

echo "All tests completed successfully.\n";
?>
