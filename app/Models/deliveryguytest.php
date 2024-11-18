<?php

// Include the necessary class files
require_once '../../config/DB.php';
require_once 'Volunteer.php';
require_once 'Person.php';
require_once 'DeliveryGuy.php';
require_once 'Vehicle.php';
require_once 'Delivery.php';
require_once 'Delivering.php';

$vehicle = new Vehicle('car12', 22);

// Initialize shared objects
function setupVolunteer()
{
    // Generate a unique email each time by appending a random string
    $randomString = substr(md5(mt_rand()), 0, 5);
    $uniqueEmail = "john.doe_{$randomString}@example.com";

    $login = new DummyLogin(); // Placeholder for a login interface implementation
    $address = new Address('Cairo', null, 1); // Example Address object
    $badge = new Badges(101, 'Trusted'); // Example Badge object

    // Create and return a Volunteer instance
    return new Volunteer(
        userTypeID: 1,
        firstName: 'John',
        lastName: 'Doe',
        email: $uniqueEmail,
        phoneNo: '555-1234',
        address: $address,
        nationalID: 'ABC12345',
        badge: $badge
    );
}

function clearPreviousTestData($email)
{
    $query = "DELETE FROM person WHERE email = '{$email}'";
    run_query($query);
}

function clearPreviousDeliveryGuy($userID)
{
    $query = "DELETE FROM deliveryguy WHERE userID = '{$userID}'";
    run_query($query);
}


// Test initializing DeliveryGuy
function testAddDeliveryRole()
{
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('car1', 2);
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);

    echo "Roles assigned to volunteer:\n";
    foreach ($deliveryGuy->getAllRoles() as $role) {
        echo "- $role\n";
    }
}

// Test insertDeliveryGuy method
function testInsertDeliveryGuy()
{
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('Car', 1);
    
    // Clear any existing records for this volunteer's userID
    clearPreviousDeliveryGuy($volunteer->getUserID());

    // Create a DeliveryGuy instance
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);

    // Check if the insert was successful
    echo "Roles assigned to volunteer:\n";
    foreach ($deliveryGuy->getAllRoles() as $role) {
        echo "- $role\n";
    }
}

// Test updateDeliveryGuy method
function testUpdateDeliveryGuy()
{
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('car3', 4);
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);

    $fieldsToUpdate = ['vehicleID' => 84];
    if ($deliveryGuy->updateDeliveryGuy($fieldsToUpdate)) {
        echo "updateDeliveryGuy: Success\n";
    } else {
        echo "updateDeliveryGuy: Failed\n";
    }
}

// Test deleteDeliveryGuy method
function testDeleteDeliveryGuy()
{
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('car4', 5);
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);

    if ($deliveryGuy->deleteDeliveryGuy()) {
        echo "deleteDeliveryGuy: Success\n";
    } else {
        echo "deleteDeliveryGuy: Failed\n";
    }
}

// Test addToDeliveryList method
function testAddToDeliveryList()
{
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('car5', 5);
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);

    $delivery = new Delivery('2024-11-12', 'Warehouse', 'Customer', $deliveryGuy->getUserID());
    if ($deliveryGuy->addToDeliveryList($delivery)) {
        echo "addToDeliveryList: Success\n";
    } else {
        echo "addToDeliveryList: Failed\n";
    }
}

// Test removeFromDeliveryList method
function testRemoveFromDeliveryList()
{
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('car6', 6);
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);

    $delivery = new Delivery(1, '2024-11-12', 'Warehouse', $deliveryGuy->getUserID(), 'Customer');
    $deliveryGuy->addToDeliveryList($delivery);

    if ($deliveryGuy->removeFromDeliveryList($delivery)) {
        echo "removeFromDeliveryList: Success\n";
    } else {
        echo "removeFromDeliveryList: Failed\n";
    }
}

// Test getDeliveryList method
function testGetDeliveryList()
{
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('car7', 7);
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);

    $delivery = new Delivery(1, '2024-11-12', 'Warehouse', $deliveryGuy->getUserID(), 'Customer');
    $deliveryGuy->addToDeliveryList($delivery);

    $deliveryList = $deliveryGuy->getDeliveryList();
    if (!empty($deliveryList)) {
        echo "getDeliveryList: Retrieved " . count($deliveryList) . " deliveries\n";
    } else {
        echo "getDeliveryList: No deliveries found\n";
    }
}

// Test assignDelivery method
function testAssignDelivery()
{
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('car8', 8);
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);

    $delivery = new Delivery(1, '2024-11-12', 'Warehouse', $deliveryGuy->getUserID(), 'Customer');
    if ($deliveryGuy->assignDelivery($delivery, '12:00')) {
        echo "assignDelivery: Success\n";
    } else {
        echo "assignDelivery: Failed\n";
    }
}

// Test unassignDelivery method
function testUnassignDelivery()
{
    $volunteer = setupVolunteer();
    $vehicle = new Vehicle('car9', 9);
    $deliveryGuy = new DeliveryGuy($volunteer, $vehicle);

    $delivery = new Delivery(1, '2024-11-12', 'Warehouse', $deliveryGuy->getUserID(), 'Customer');
    $deliveryGuy->assignDelivery($delivery, '12:00');

    if ($deliveryGuy->unassignDelivery($delivery)) {
        echo "unassignDelivery: Success\n";
    } else {
        echo "unassignDelivery: Failed\n";
    }
}

// Run all tests

echo "=== Running Tests for DeliveryGuy ===\n";
testAddDeliveryRole();
echo "\n";
testInsertDeliveryGuy();
echo "\n";
testUpdateDeliveryGuy();
echo "\n";
testDeleteDeliveryGuy();
echo "\n";
testAddToDeliveryList();
echo "\n";
testRemoveFromDeliveryList();
echo "\n";
testGetDeliveryList();
echo "\n";
testAssignDelivery();
echo "\n";
testUnassignDelivery();

?>
