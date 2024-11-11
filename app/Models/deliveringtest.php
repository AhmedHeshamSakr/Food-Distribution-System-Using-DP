<?php

require_once 'Delivery.php';
require_once 'DeliveryGuy.php';
require_once 'Delivering.php';
require_once '../../config/DB.php';

// Custom function to simulate database query execution (mocked)
function setupVolunteer()
{
    // Generate a unique email each time by appending a random string
    $randomString = substr(md5(mt_rand()), 0, 5);
    $uniqueEmail = "john.doe_{$randomString}@example.com";

    $login = new DummyLogin(); // Placeholder for a login interface implementation
    $address = new Address(1, '123 Main St', null, 'Street'); // Example Address object
    $badge = new Badge(101, 'Trusted'); // Example Badge object

    // Create and return a Volunteer instance
    return new Volunteer(
        userTypeID: 1,
        firstName: 'John',
        lastName: 'Doe',
        email: $uniqueEmail,
        phoneNo: '555-1234',
        login: $login,
        address: $address,
        nationalID: 'ABC12345',
        badge: $badge
    );
}

// Testing the Delivering class
function testDeliveringClass()
{
    // Mock DeliveryGuy and Delivery objects
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('car1221', 2);
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);
    $delivery = new Delivery('2024-11-12', 'Warehouse', 'Customer', $deliveryGuy->getUserID());

    // Create the Delivering object with mock data
    $deliveryTime = '14:30:00';
    $delivering = new Delivering($deliveryGuy, $delivery, $deliveryTime);

    // Test Constructor and Getter Methods
    echo "Testing Constructor and Getter Methods\n";
    assert($delivering->getDeliveryGuy() === $deliveryGuy);
    assert($delivering->getDelivery() === $delivery);
    assert($delivering->getDeliveryTime() === $deliveryTime);
    echo "Constructor and Getter Methods passed.\n\n";

    // Test insertDelivering method
    echo "Testing insertDelivering method\n";
    $insertResult = $delivering->insertDelivering();
    assert($insertResult === true);  // Mocked to return true
    echo "insertDelivering method passed.\n\n";

    // Test updateDelivering method
    echo "Testing updateDelivering method\n";
    $updateResult = $delivering->updateDelivering(12);
    assert($updateResult === true);  // Mocked to return true
    echo "updateDelivering method passed.\n\n";

    // Test deleteDelivering method
    echo "Testing deleteDelivering method\n";
    $deleteResult = $delivering->deleteDelivering();
    assert($deleteResult === true);  // Mocked to return true
    echo "deleteDelivering method passed.\n\n";
}

// Run the tests
testDeliveringClass();

?>
