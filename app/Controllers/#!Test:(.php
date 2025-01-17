<?php

require_once __DIR__ . "/../Controllers/EventController.php";
require_once __DIR__ . "/../Models/Event.php";
require_once __DIR__ . "/../Models/Volunteer.php";
require_once __DIR__ . "/../Models/Cook.php";
require_once __DIR__ . "/../Models/DeliveryGuy.php";
require_once __DIR__ . "/../Models/Coordinator.php";
require_once __DIR__ . "/../Models/Vehicle.php";

/**
 * Mock the database query function to simulate a database interaction
 */
function run_select_query($query) {
    // Simulate a real database response
    if (strpos($query, "userID FROM person") !== false) {
        return [['userID' => 1]]; // Mock user ID for the given email
    }
    return [];
}

/**
 * Full test for handleVolunteerSignup
 */
function testHandleVolunteerSignup() {
    echo "=== Starting handleVolunteerSignup Test ===\n";

    // Mock session data to simulate a logged-in user
    $_SESSION['email'] = "test@example.com"; // Replace with an actual email in your database

    // Mock POST data for testing different staff types
    $testCases = [
        ['eventID' => 1, 'staffType' => 'cook'],
        ['eventID' => 1, 'staffType' => 'delivery'],
        ['eventID' => 1, 'staffType' => 'coordinator'],
    ];

    foreach ($testCases as $case) {
        echo "\n--- Testing staff type: {$case['staffType']} ---\n";

        // Set POST data for the current test case
        $_POST['eventID'] = $case['eventID']; // Replace with a valid event ID in your database
        $_POST['staffType'] = $case['staffType'];

        // Create a new instance of the EventController in volunteer context
        $controller = new EventController(true);

        // Capture the output and handle exceptions
        ob_start();
        try {
            $controller->handleVolunteerSignup();
            $output = ob_get_clean();
            echo "Test passed for staff type '{$case['staffType']}': Volunteer signup completed successfully.\n";
        } catch (Exception $e) {
            ob_end_clean();
            echo "Test failed for staff type '{$case['staffType']}': " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== handleVolunteerSignup Test Completed ===\n";
}

// Run the test function
testHandleVolunteerSignup();