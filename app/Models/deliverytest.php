<?php

// Include the necessary class files
require_once '../../config/DB.php';
require_once 'Delivery.php';


// Test the insertion of a new delivery
function testInsertDelivery()
{
    // Database connection setup
    $db = Database::getInstance(); // Assuming this class is defined in Database.php

    // Test data for creating a new delivery
    $deliveryDate = '2024-11-15';
    $startLocation = '123 Main St';
    $endLocation = '456 Elm St';
    $deliveryGuy = 1; // Example delivery guy ID
    $status = 'pending';
    $deliveryDetails = 'Handle with care, fragile items inside.';

    // Create a new delivery object
    $delivery = new Delivery('2024-11-12', 'Warehouse', 'Customer', $deliveryGuy);

    // Insert the delivery into the database
    if ($delivery->insertDelivery($deliveryDate, $startLocation, $endLocation, $deliveryGuy, $status, $deliveryDetails)) {
        echo "New delivery inserted successfully with ID: " . $delivery->getDeliveryID() . "\n";
    } else {
        echo "Failed to insert delivery.\n";
    }
}

// Test updating an existing delivery
function testUpdateDelivery()
{
    // Example: Updating delivery with ID 1 (change this to an existing deliveryID)
    $deliveryGuy = 1; // Example delivery guy ID
    $deliveryID = 1;
    $delivery = new Delivery('2024-11-12', 'Warehouse', 'Customer', $deliveryGuy);

    // Update status and delivery details
    $newStatus = 'delivering';
    $newDetails = 'In transit, estimated delivery time is 2 hours.';
    $delivery->setStatus($newStatus);
    $delivery->setDeliveryDetails($newDetails);

    // Update delivery in the database
    if ($delivery->updateDelivery([
        'status' => $newStatus,
        'deliveryDetails' => $newDetails
    ])) {
        echo "Delivery updated successfully.\n";
    } else {
        echo "Failed to update delivery.\n";
    }
}

// Test deleting a delivery
function testDeleteDelivery()
{
    // Example: Deleting delivery with ID 1 (change this to an existing deliveryID)
    $deliveryID = 1;
    $deliveryGuy = 1;
    $delivery = new Delivery('2024-11-12', 'Warehouse', 'Customer', $deliveryGuy);

    if ($delivery->deleteDelivery()) {
        echo "Delivery with ID $deliveryID deleted successfully.\n";
    } else {
        echo "Failed to delete delivery.\n";
    }
}

// Test retrieving delivery details by ID
function testGetDeliveryDetails()
{
    // Example: Retrieving details of delivery with ID 1 (change this to an existing deliveryID)
    $deliveryID = 1;
    $deliveryGuy = 1;
    $delivery = new Delivery('2024-11-12', 'Warehouse', 'Customer', $deliveryGuy);

    // Assuming you can retrieve the delivery from the database (you can implement a select method)
    $deliveryDetails = $delivery->getDeliveryDetails();
    echo "Delivery Details for ID $deliveryID: " . $deliveryDetails . "\n";
}

// Run the tests
echo "=== Running Test: Insert Delivery ===\n";
testInsertDelivery();

echo "\n=== Running Test: Update Delivery ===\n";
testUpdateDelivery();

echo "\n=== Running Test: Delete Delivery ===\n";
testDeleteDelivery();

echo "\n=== Running Test: Get Delivery Details ===\n";
testGetDeliveryDetails();

?>
