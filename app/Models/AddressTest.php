
<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../config/DB.php";
require_once 'Address.php';

// Create a new Address object with valid ENUM level value ('City')
$address = new Address('Cairo', Address::getIdByName('Egypt'), 'City');

// Test creating a new address in the database
echo "Testing Address Creation:<br>";
$addressCreated = $address->create();
echo $addressCreated ? "Address created successfully.<br>" : "Failed to create address.<br>";

// Test reading the address from the database
echo "<br>Testing Address Read:<br>";
$addressDetails = Address::read($address->getId());
if ($addressDetails) {
    echo "Address ID: " . $addressDetails->getId() . "<br>";
    echo "Address Name: " . $addressDetails->getName() . "<br>";
    echo "Parent ID: " . $addressDetails->getParentId() . "<br>";
    echo "Level: " . $addressDetails->getLevel() . "<br>";
} else {
    echo "Address not found.<br>";
}

// Test updating the address details with another valid ENUM level value ('State')
echo "<br>Testing Address Update:<br>";
$address->setName('Updated Cairo');
// $address->setParentId(0);
// $address->setLevel('Country');
$addressUpdated = $address->update();
echo $addressUpdated ? "Address updated successfully.<br>" : "Failed to update address.<br>";

// Test reading the updated address from the database
echo "<br>Testing Updated Address Read:<br>";
$updatedAddressDetails = Address::read($addressDetails->getId());
if ($updatedAddressDetails) {
    echo "Updated Address ID: " . $updatedAddressDetails->getId() . "<br>";
    echo "Updated Address Name: " . $updatedAddressDetails->getName() . "<br>";
    echo "Updated Parent ID: " . $updatedAddressDetails->getParentId() . "<br>";
    echo "Updated Level: " . $updatedAddressDetails->getLevel() . "<br>";
} else {
    echo "Address not found.<br>";
}

// Test deleting the address from the database
// echo "<br>Testing Address Deletion:<br>";
// $addressDeleted = $address->delete();
// echo $addressDeleted ? "Address deleted successfully.<br>" : "Failed to delete address.<br>";


// Attempt to read the address again to confirm deletion
echo "<br>Testing Address Read After Deletion:<br>";
$deletedAddressDetails = Address::read($addressDetails->getId());
if ($deletedAddressDetails) {
    echo "Address still exists after deletion!<br>";
} else {
    echo "Address successfully deleted.<br>";
}

?>
