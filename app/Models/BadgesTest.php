<?php

require_once 'Badges.php'; // Adjust the path if necessary

function testBadgeClass()
{
    echo "Starting tests for Badges class...\n\n";

    // Create a new badge
    echo "Testing badge creation...\n";
    $badge = new Badges("Gold");
    $badgeID = $badge->getBadgeID();
    echo $badgeID > 0 ? "Badge created successfully with ID: $badgeID\n" : "Failed to create badge.\n";

    // Retrieve the created badge by ID
    echo "\nTesting retrieving badge by ID...\n";
    $retrievedBadge = $badge->getBadgeByID($badgeID);
    if ($retrievedBadge) {
        echo "Badge retrieved successfully: Level = {$retrievedBadge['badgeLvl']}\n";
    } else {
        echo "Failed to retrieve badge.\n";
    }

    // Retrieve all badges
    echo "\nTesting retrieving all badges...\n";
    $allBadges = $badge->getAllBadges();
    if (!empty($allBadges)) {
        echo "All badges retrieved successfully: \n";
        foreach ($allBadges as $b) {
            echo "ID: {$b['badgeID']}, Level: {$b['badgeLvl']}\n";
        }
    } else {
        echo "No badges found.\n";
    }

    // Update the badge level
    echo "\nTesting badge update...\n";
    $badge->setBadgeLvl("Platinum");
    if ($badge->updateBadge()) {
        echo "Badge updated successfully to Level: Platinum\n";
    } else {
        echo "Failed to update badge.\n";
    }

    // Delete the badge
    echo "\nTesting badge deletion...\n";
    if ($badge->deleteBadge()) {
        echo "Badge deleted successfully.\n";
    } else {
        echo "Failed to delete badge.\n";
    }

    // Verify deletion
    echo "\nTesting retrieval of deleted badge...\n";
    $deletedBadge = $badge->getBadgeByID($badgeID);
    echo $deletedBadge ? "Badge still exists after deletion.\n" : "Badge successfully deleted.\n";

    echo "\nTests completed.\n";
}

// Run the tests
testBadgeClass();

?>
