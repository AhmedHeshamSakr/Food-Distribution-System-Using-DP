<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the required class files
require_once __DIR__ . "/../../config/DB.php";
require_once '#c-Meals.php';
require_once '#c-Cooking.php';
require_once '#c-Cook.php';
require_once 'Volunteer.php';
require_once 'User.php';
require_once 'Login.php'; // Include the login implementations

// Setup Database Connection (Assuming it's already configured)
$db = Database::getInstance();

// Helper function to execute queries directly for setup and teardown
function run_raw_query($query) {
    $conn = Database::getInstance()->getConnection();
    return mysqli_query($conn, $query);
}

// Clean up test data (if necessary)
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
        $login = new withEmail($email, 'password'); // Example: Use withEmail as the login method
        
        // Pass the login instance to the parent constructor
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }

    public function getUserID(): int {
        return 101; // Static ID for testing purposes
    }
}
// ** Test 1: Creating a Meal **
echo "Test 1: Creating a Meal\n";
$meal = new Meal(true, 20, "Healthy Veggie Meals");
$createResult = $meal->CreateMeal();

assert($createResult === true, "Failed to create meal");
// Use the getter method to get the mealID
$mealID = $meal->getMealID(); // Use the getter method instead of accessing directly
assert(!empty($mealID), "Meal ID should not be empty after creation");

// ** Test 2: Reading a Meal **
echo "Test 2: Reading a Meal\n";
$readMeal = Meal::ReadMeal($mealID);
assert($readMeal !== null, "Failed to read the created meal");
assert($readMeal->getRemainingMeals() === 20, "Meal count should be 20");
assert($readMeal->mealDescription === "Healthy Veggie Meals", "Meal description mismatch");

// ** Test 3: Cook Taking Meals **
echo "Test 3: Cook Taking Meals\n";

// Use withEmail login mechanism for this test (you can change to withGoogle or withFacebook for different tests)
$credentials = ['email' => 'john.doe@example.com', 'password' => 'password123'];

// Use the login mechanism for Cook
$emailLogin = new withEmail($credentials['email'], $credentials['password']);
$loginResult = $emailLogin->login($credentials);

if ($loginResult) {
    // Assuming successful login, we can now create a cook object
    $cookUser = new TestUser(101, 'John', 'Doe', 'john@example.com', '1234567890', $emailLogin);
    $cook = new Cook($cookUser);

    $takeResult = $cook->takeMeals($mealID, 5);
    assert($takeResult === true, "Cook failed to take meals");

    $remainingMealsAfterTake = Meal::ReadMeal($mealID)->getRemainingMeals();
    assert($remainingMealsAfterTake === 15, "Remaining meals should be 15 after cook took 5 meals");
} else {
    echo "Login failed, unable to proceed with test.\n";
    exit;
}

// ** Test 4: Cook Completing Meals **
echo "Test 4: Cook Completing Meals\n";
$completeResult = $cook->completeMeals($mealID, 3);
assert($completeResult === true, "Cook failed to complete meals");

$cookAssignedMeals = Cooking::getMealsByCook($cookUser->getUserID());
assert(count($cookAssignedMeals) > 0, "Cook should have assigned meals");
assert($cookAssignedMeals[0]['mealsCompleted'] === 3, "Cook should have completed 3 meals");

// ** Test 5: Cook Over-Completion Edge Case **
echo "Test 5: Cook Over-Completion Edge Case\n";
$overCompleteResult = $cook->completeMeals($mealID, 10);
assert($overCompleteResult === false, "Cook should not complete more than taken meals");

$currentCompleted = Cooking::getMealsByCook($cookUser->getUserID())[0]['mealsCompleted'];
assert($currentCompleted === 3, "Cook should still have completed only 3 meals");

// ** Test 6: Cook Taking Additional Meals **
echo "Test 6: Cook Taking Additional Meals\n";
$takeMoreMeals = $cook->takeMeals($mealID, 10);
assert($takeMoreMeals === true, "Cook failed to take additional meals");

$updatedRemainingMeals = Meal::ReadMeal($mealID)->getRemainingMeals();
assert($updatedRemainingMeals === 5, "Remaining meals should be 5 after taking additional meals");

// ** Test 7: Meal Availability Check **
echo "Test 7: Meal Availability Check\n";
$mealsNeeded = Cooking::getMealsNeeded($mealID);
assert($mealsNeeded === 5, "5 meals should be left to prepare");

// Cleanup after tests
cleanup();

echo "All tests completed successfully.\n";
?>