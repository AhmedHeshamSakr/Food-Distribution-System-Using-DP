<?php
require_once __DIR__ . "/../../config/DB.php";
require_once '#a-Badmin.php';
require_once '#a-Eadmin.php';
require_once 'Address.php';
require_once 'Login.php';
require_once 'Event.php';

$email = "mohamed16@gmail.com";

 // Instantiate the login object (replace with actual strategy: withGoogle, withFacebook, or withEmail)
 $login = new withGoogle($email, 'password'); // Replace 'password' with the actual password for testing

 // Attempt to log in with the provided credentials
 if (!$login->login(['email' => $email, 'password' => 'password'])) {
     throw new Exception("Login failed for Google authentication.");
 }

 echo "<p>Successfully authenticated with Google for user: {$email}</p>";

$eventLocation = new Address(1, '123 Test Street', 5, '12345');

// ---------------------- Testing EventAdmin ----------------------

// Initialize EventAdmin
$eventAdmin = new EventAdmin(1, 'John', 'Doe', 'johndoe@example.com', '1234567890', $login);

// 1. Test creating an event
echo "Testing Event Creation:\n";
$createEventResult = $eventAdmin->createEvent(1, '2024-12-31', $eventLocation, 'New Year Event', 'Celebrating New Year', 5, 3, 2);
echo $createEventResult ? "Event created successfully.\n" : "Event creation failed.\n";

// 2. Test reading the event
echo "Testing Event Retrieval:\n";
$event = $eventAdmin->readEvent(1);
if ($event) {
    echo "Event Retrieved: " . $event->getEventName() . "\n";
} else {
    echo "Event retrieval failed.\n";
}

// 3. Test updating the event
echo "Testing Event Update:\n";
$updateEventResult = $eventAdmin->updateEvent(1, '2024-12-31', $eventLocation, 'Updated New Year Event', 'Updated Description', 6, 4, 3);
echo $updateEventResult ? "Event updated successfully.\n" : "Event update failed.\n";

// // 4. Test deleting the event
// echo "Testing Event Deletion:\n";
// $deleteEventResult = $eventAdmin->deleteEvent(1);
// echo $deleteEventResult ? "Event deleted successfully.\n" : "Event deletion failed.\n";


// ---------------------- Testing BadgeAdmin ----------------------

// Initialize BadgeAdmin
$badgeAdmin = new BadgeAdmin(1, 'Jane', 'Smith', 'janesmith@example.com', '0987654321', $login);

// 1. Test creating a badge
echo "\nTesting Badge Creation:\n";
$createBadgeResult = $badgeAdmin->createBadge('Gold');
echo $createBadgeResult ? "Badge created successfully.\n" : "Badge creation failed.\n";

// 2. Test retrieving all badges
echo "Testing Badge Retrieval:\n";
$allBadges = $badgeAdmin->getAllBadges();
if ($allBadges) {
    echo "Badges Retrieved:\n";
    foreach ($allBadges as $badge) {
        echo "- Badge Level: " . $badge['badgeLvl'] . "\n";
    }
} else {
    echo "No badges retrieved.\n";
}

// 3. Test updating the badge
echo "Testing Badge Update:\n";
$updateBadgeResult = $badgeAdmin->updateBadge(1, 'Platinum');
echo $updateBadgeResult ? "Badge updated successfully.\n" : "Badge update failed.\n";

// 4. Test deleting the badge
// echo "Testing Badge Deletion:\n";
// $deleteBadgeResult = $badgeAdmin->deleteBadge(1);
// echo $deleteBadgeResult ? "Badge deleted successfully.\n" : "Badge deletion failed.\n";

// 5. Test assigning a badge to a user
echo "Testing Badge Assignment to User:\n";
$assignBadgeResult = $badgeAdmin->assignBadgeToUser(1, 1, '2024-01-01', '2024-12-31');
echo $assignBadgeResult ? "Badge assigned to user successfully.\n" : "Badge assignment failed.\n";

// 6. Test revoking a badge from a user
// echo "Testing Badge Revocation from User:\n";
// $revokeBadgeResult = $badgeAdmin->revokeBadgeFromUser(1, 1);
// echo $revokeBadgeResult ? "Badge revoked from user successfully.\n" : "Badge revocation failed.\n";

// Cleanup: Optional logic to clean up test data from the database

echo "\nTesting complete.\n";