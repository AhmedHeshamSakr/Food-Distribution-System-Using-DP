<?php // Create a base User instance (this could be any class that extends User)
require_once 'User.php';
require_once 'Volunteer.php';
require_once 'Login.php';
require_once 'Address.php';
require_once 'Vehicle.php';
require_once 'DeliveryGuy.php';

 class Badge{

}



$emailLogin = new withEmail('hello','hello');
$loginResult = $emailLogin->login(['email' => 'hello', 'password' => 'hello']);
$address = new Address(1, 'hello', 2, 3);
$badge = new Badge();
$vehicle = new Vehicle(350576465454384564586766, '12345');
$user = new Volunteer(0, 'Jumana', 'Yasser', 'juamna2a514652527697596787151a@gmail.com', '01236', $emailLogin, $address, '0158655', $badge);

// Decorate the user with the Cook, DeliveryGuy, Coordinator, Reporter, and Donor roles
           // Calls chooseRole in the constructor
$deliveryGuy = new DeliveryGuy($user, $vehicle);  // Calls chooseRole in the constructor
// $coordinator = new Coordinator($deliveryGuy);  // Calls chooseRole in the constructor
// $reporter = new Reporter($coordinator);  // Calls chooseRole in the constructor
// $donor = new Donor($reporter);  // Calls chooseRole in the constructor

// At this point, chooseRole has already been called in the constructor of each decorator, modifying the userTypeID

// Get the updated userTypeID value
echo "User Type ID after all roles: " . $user->getUserTypeID() . "\n"; // Should show combined flags for all roles

// Verify the roles
$deliveryGuy->chooseRole();
$roles = $deliveryGuy->getAllRoles(); // This should include 'Cook', 'DeliveryGuy', 'Coordinator', 'Reporter', 'Donor'
echo "Roles: " . implode(", ", $roles) . "\n";

// Test if specific roles are set correctly
echo "Is Cook: " . ($deliveryGuy->hasRole(User::COOK_FLAG) ? "Yes" : "No") . "\n";
echo "Is DeliveryGuy: " . ($deliveryGuy->hasRole(User::DELIVERY_FLAG) ? "Yes" : "No") . "\n";
echo "Is Coordinator: " . ($deliveryGuy->hasRole(User::COORDINATOR_FLAG) ? "Yes" : "No") . "\n";
echo "Is Reporter: " . ($deliveryGuy->hasRole(User::REPORTER_FLAG) ? "Yes" : "No") . "\n";
echo "Is Donor: " . ($deliveryGuy->hasRole(User::DONOR_FLAG) ? "Yes" : "No") . "\n";
