<?php // Create a base User instance (this could be any class that extends User)
require_once 'Person.php';
require_once 'Volunteer.php';
require_once 'Login.php';
require_once 'Address.php';
require_once 'Vehicle.php';
require_once 'Badges.php';
require_once 'DeliveryGuy.php';
require_once '#c-Cook.php';
require_once 'Coordinator.php';
require_once 'Donor.php';
require_once 'Reporter.php';
require_once '#a-Eadmin.php';
require_once '#a-Badmin.php';



$emailLogin = new withEmail('hello','hello');
$loginResult = $emailLogin->login(['email' => 'hello', 'password' => 'hello']);
// $address = new Address(1, 'hello', 2, 3);
$badge = new Badges(2, 'Gold Tier');
$vehicle = new Vehicle(35057646546664, '12345');
$user = new Volunteer(0, 'Jumana', 'Yasser', 'juamna2a65144444555464564525556445664456565255744552654476465644355555474446D584245949446654752465955564465679687151a@gmail.com', '01236', $address, '0158655', $badge);
//$user = new Donor(50, 'Leon', 'Kennedy','Ada@gmail.com', '01236', $emailLogin);
//$user = new Reporter(2,'Ada', 'Wong', 'Leon@gmail.com', '01236', $emailLogin);
//$user = new EventAdmin(4,'Jill', 'Valentine', 'jill@gmail.com', '01236', $emailLogin);
//$user = new BadgeAdmin(4,'Jake', 'Valentine', 'jill@gmail.com', '01236', $emailLogin);

// Decorate the user with the Cook, DeliveryGuy, Coordinator, Reporter, and Donor roles
           // Calls chooseRole in the constructor
echo "#################################### TESTING COOK ####################################</br>\n";

$cook = new Cook($user);
// $coordinator = new Coordinator($deliveryGuy);  // Calls chooseRole in the constructor
// $reporter = new Reporter($coordinator);  // Calls chooseRole in the constructor
// $donor = new Donor($reporter);  // Calls chooseRole in the constructor

// At this point, chooseRole has already been called in the constructor of each decorator, modifying the userTypeID

// Get the updated userTypeID value
echo "User Type ID after all roles: " . $cook->getUserTypeID() . "</br>\n"; // Should show combined flags for all roles

// Verify the roles

$cook->chooseRole();
//$roles = $deliveryGuy->getAllRoles(); // This should include 'Cook', 'DeliveryGuy', 'Coordinator', 'Reporter', 'Donor'
//echo "Roles: " . implode(", ", $roles) . "\n";

// Test if specific roles are set correctly

echo "Is Cook: " . ($cook->hasRole(Person::COOK_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is DeliveryGuy: " . ($cook->hasRole(Person::DELIVERY_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is Coordinator: " . ($cook->hasRole(Person::COORDINATOR_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is Reporter: " . ($cook->hasRole(Person::REPORTER_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is Donor: " . ($cook->hasRole(Person::DONOR_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is E_Admin: " . ($cook->hasRole(Person::E_ADMIN_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is B_Admin: " . ($cook->hasRole(Person::B_ADMIN_FLAG) ? "Yes" : "No") . "</br>\n";


echo "#################################### TESTING COOK + DELIVERY GUY ####################################</br>\n";

$deliveryGuy = new DeliveryGuy($cook, $vehicle);  // Calls chooseRole in the constructor
// $coordinator = new Coordinator($deliveryGuy);  // Calls chooseRole in the constructor
// $reporter = new Reporter($coordinator);  // Calls chooseRole in the constructor
// $donor = new Donor($reporter);  // Calls chooseRole in the constructor

// At this point, chooseRole has already been called in the constructor of each decorator, modifying the userTypeID

// Get the updated userTypeID value


echo "User Type ID after all roles: " . $deliveryGuy->getUserTypeID() . "</br>\n"; // Should show combined flags for all roles

// Verify the roles

$deliveryGuy->chooseRole();
//$roles = $deliveryGuy->getAllRoles(); // This should include 'Cook', 'DeliveryGuy', 'Coordinator', 'Reporter', 'Donor'
//echo "Roles: " . implode(", ", $roles) . "\n";

// Test if specific roles are set correctly

echo "Is Cook: " . ($deliveryGuy->hasRole(Person::COOK_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is DeliveryGuy: " . ($deliveryGuy->hasRole(Person::DELIVERY_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is Coordinator: " . ($deliveryGuy->hasRole(Person::COORDINATOR_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is Reporter: " . ($deliveryGuy->hasRole(Person::REPORTER_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is Donor: " . ($deliveryGuy->hasRole(Person::DONOR_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is E_Admin: " . ($cook->hasRole(Person::E_ADMIN_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is B_Admin: " . ($cook->hasRole(Person::B_ADMIN_FLAG) ? "Yes" : "No") . "</br>\n";


echo "#################################### TESTING COOK + DELIVERY GUY + COORDINATOR ####################################</br>\n";

$coordinator = new Coordinator($deliveryGuy);
// $coordinator = new Coordinator($deliveryGuy);  // Calls chooseRole in the constructor
// $reporter = new Reporter($coordinator);  // Calls chooseRole in the constructor
// $donor = new Donor($reporter);  // Calls chooseRole in the constructor

// At this point, chooseRole has already been called in the constructor of each decorator, modifying the userTypeID

// Get the updated userTypeID value
echo "User Type ID after all roles: " . $coordinator->getUserTypeID() . "</br>\n"; // Should show combined flags for all roles

// Verify the roles

$coordinator->chooseRole();
//$roles = $deliveryGuy->getAllRoles(); // This should include 'Cook', 'DeliveryGuy', 'Coordinator', 'Reporter', 'Donor'
//echo "Roles: " . implode(", ", $roles) . "\n";

// Test if specific roles are set correctly

echo "Is Cook: " . ($coordinator->hasRole(Person::COOK_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is DeliveryGuy: " . ($coordinator->hasRole(Person::DELIVERY_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is Coordinator: " . ($coordinator->hasRole(Person::COORDINATOR_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is Reporter: " . ($coordinator->hasRole(Person::REPORTER_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is Donor: " . ($coordinator->hasRole(Person::DONOR_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is E_Admin: " . ($cook->hasRole(Person::E_ADMIN_FLAG) ? "Yes" : "No") . "</br>\n";
echo "Is B_Admin: " . ($cook->hasRole(Person::B_ADMIN_FLAG) ? "Yes" : "No") . "</br>\n";
