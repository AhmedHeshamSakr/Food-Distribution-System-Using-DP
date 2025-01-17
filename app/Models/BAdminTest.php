<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '#a-Badmin.php';
require_once 'Volunteer.php';
require_once 'Badges.php';
require_once 'Person.php';
require_once __DIR__ . "/../../config/DB.php";

// Create a BadgeAdmin instance
$admin = new BadgeAdmin('Admin', 'User', 'admin@example.com', '1234567890');

// Test createBadge
$badgeLvl = 'TestBadge';
$created = $admin->createBadge($badgeLvl);
echo "Badge created: " . ($created ? 'Success' : 'Failed') . "\n";

// Fetch the badge ID from the database
$conn = Database::getInstance()->getConnection();
$query = "SELECT badgeID FROM Badge WHERE badgeLvl = '$badgeLvl'";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $badgeID = $row['badgeID'];
    echo "Badge ID: $badgeID\n";
} else {
    echo "Failed to retrieve badge ID.\n";
    exit;
}

// Test getBadge
$badge = $admin->getBadge($badgeID);
if ($badge && $badge['badgeLvl'] == $badgeLvl) {
    echo "Badge retrieved correctly.\n";
} else {
    echo "Failed to retrieve badge.\n";
}

// Test updateBadge
$newBadgeLvl = 'UpdatedTestBadge';
$updated = $admin->updateBadge($badgeID, $newBadgeLvl);
echo "Badge updated: " . ($updated ? 'Success' : 'Failed') . "\n";

// Verify the update
$updatedBadge = $admin->getBadge($badgeID);
if ($updatedBadge && $updatedBadge['badgeLvl'] == $newBadgeLvl) {
    echo "Badge update verified.\n";
} else {
    echo "Badge update verification failed.\n";
}

// Test getAllBadges
$allBadges = $admin->getAllBadges();
echo "All Badges:\n";
print_r($allBadges);

// Create a Volunteer instance for testing
$address = new Address('123 Test Street', 12345, 'State'); // Assuming 12345 is the correct integer value for the second argument
$volunteer = new Volunteer(0, 'Volunteer', 'User', 'volunteer@example.com', '0987654321', $address, 'VOL12345', new Badges('Bronze'));

// Test assignBadgeToUser
$assigned = $admin->assignBadgeToUser($volunteer->getUserID(), $badgeID);
echo "Badge assigned: " . ($assigned ? 'Success' : 'Failed') . "\n";

// Verify the assignment
$volunteerBadge = $admin->getUserBadge($volunteer->getUserID());
if ($volunteerBadge && $volunteerBadge->getBadgeID() == $badgeID) {
    echo "Badge assignment verified.\n";
} else {
    echo "Badge assignment verification failed.\n";
}

// Test revokeBadgeFromUser
$revoked = $admin->revokeBadgeFromUser($volunteer->getUserID());
echo "Badge revoked: " . ($revoked ? 'Success' : 'Failed') . "\n";

// Verify revocation
$volunteerBadge = $admin->getUserBadge($volunteer->getUserID());
if (!$volunteerBadge || $volunteerBadge->getBadgeID() != $badgeID) {
    echo "Badge revocation verified.\n";
} else {
    echo "Badge revocation verification failed.\n";
}

// Test deleteBadge
$deleted = $admin->deleteBadge($badgeID);
echo "Badge deleted: " . ($deleted ? 'Success' : 'Failed') . "\n";

// Verify deletion
$badge = $admin->getBadge($badgeID);
if (!$badge) {
    echo "Badge deletion verified.\n";
} else {
    echo "Badge deletion verification failed.\n";
}

?>