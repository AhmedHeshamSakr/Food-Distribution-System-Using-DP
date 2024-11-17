<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'Event.php';
require_once '#a-Eadmin.php';
require_once 'Address.php';
require_once 'Login.php';
require_once __DIR__ . "/../../config/DB.php"; // Include this if you have any DB connections


// Helper function to print test results
function assertEquals($expected, $actual, $message)
{
    if ($expected === $actual) {
        echo "[PASS] $message\n";
    } else {
        echo "[FAIL] $message\nExpected: $expected, but got: $actual\n";
    }
}

// $address = new Address(3, '123 Test St', 1, 'TestCity');
   // Create a new Address object with valid ENUM level value ('City')
// $address = new Address(123,'Cairo', 1, 'City');

// // Test creating a new address in the database
// echo "Testing Address Creation:<br>";
// $addressCreated = $address->create();
// echo $addressCreated ? "Address created successfully.<br>" : "Failed to create address.<br>";



// Test Event Creation and CRUD Operations
function testEventCRUD()
{

    $address = new Address(77,'Cairo', 3, 'City');
    $address->create();

    $event = new Event(301, '2024-12-25', $address, 'Christmas Party', 'Celebrate Christmas together', 3, 2, 1);

    // Test creating an event
    $eventCreated = $event->create();
    assertEquals(true, $eventCreated, "Event should be created successfully");

    // Test reading an event
    $fetchedEvent = $event->read();
    assertEquals($event->getEventName(), $fetchedEvent->getEventName(), "Fetched Event name should match");
    
    // Test updating an event
    $event->setEventName('Updated Christmas Party');
    $eventUpdated = $event->update();
    assertEquals(true, $eventUpdated, "Event should be updated successfully");
    
    // Test deleting an event
    $eventDeleted = $event->delete();
    assertEquals(true, $eventDeleted, "Event should be deleted successfully");
}

// Test Observer Pattern with EventAdmin
function testObserverPattern()
{
    $email = "mohamed16@gmail.com";
 // Instantiate the login object (replace with actual strategy: withGoogle, withFacebook, or withEmail)
 $login = new withEmail($email, 'password'); // Replace 'password' with the actual password for testing

 // Attempt to log in with the provided credentials
 if (!$login->login(['email' => $email, 'password' => 'password'])) {
     throw new Exception("Login failed for Google authentication.");
 }

 echo "<p>Successfully authenticated with Google for user: {$email}</p>";

    $address = new Address(103, '789 Oak St', 67890, 'TestVille');
    $admin = new EventAdmin(1, 'John', 'Doe', 'john.doe@example.com', '1234567890');

    // Creating an event with specific requirements
    $event = new Event(401, '2024-11-20', $address, 'Thanksgiving Event', 'Family and friends gathering', 2, 1, 1);
    $event->addObserver($admin);

    ob_start(); // Capture output for testing

    // Fulfilling all requirements
    $event->assignCook();
    $event->assignCook();
    $event->assignDelivery();
    $event->assignCoordinator();

    $output = ob_get_clean(); // Capture the printed output

    // Check if admin received a notification
    $expectedMessage = "Admin Notification: All requirements for event 'Thanksgiving Event' are fulfilled.";
    assertEquals(true, strpos($output, $expectedMessage) !== false, "Admin should be notified when event requirements are fulfilled");
}

// Execute all tests
echo "Running Tests...\n";

// testAddressCreation();
testEventCRUD();
testObserverPattern();

echo "All tests completed.\n";

?>