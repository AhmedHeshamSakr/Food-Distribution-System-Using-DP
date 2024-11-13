<?php

require_once __DIR__ . "/../../config/DB.php"; 
require_once 'Badges.php';

// CREATE a new badge
$newBadge = new Badges();
$newBadge->setBadgeLvl('Gold Tier');
if ($newBadge->insertBadge()) {
    echo "New badge inserted successfully.<br/>";
} else {
    echo "Failed to insert new badge.<br/>";
}

// READ a badge by ID
$badgeID = 1; // Replace with a valid badgeID from your database
$badge = new Badges();
$badgeData = $badge->getBadgeByID($badgeID);
if ($badgeData) {
    echo "Badge Details:<br/>";
    echo "ID: " . $badgeData['badgeID'] . "<br/>";
    echo "Level: " . $badgeData['badgeLvl'] . "<br/>";
} else {
    echo "Badge not found.<br/>";
}

// READ all badges
$allBadges = new Badges();
$badgesList = $allBadges->getAllBadges();
if (!empty($badgesList)) {
    echo "All Badges:<br/>";
    foreach ($badgesList as $badge) {
        echo "ID: " . $badge['badgeID'] . " - Level: " . $badge['badgeLvl'] . "<br/>";
    }
} else {
    echo "No badges found.<br/>";
}

// UPDATE a badge
$updateBadge = new Badges(1);  // Replace with an existing badgeID
$updateBadge->setBadgeLvl('Platinum Tier');
if ($updateBadge->updateBadge()) {
    echo "Badge updated successfully.<br/>";
} else {
    echo "Failed to update badge.<br/>";
}

// DELETE a badge
// $deleteBadge = new Badges(2);  // Replace with an existing badgeID to delete
// if ($deleteBadge->deleteBadge()) {
//     echo "Badge deleted successfully.<br/>";
// } else {
//     echo "Failed to delete badge.<br/>";
// }

?>