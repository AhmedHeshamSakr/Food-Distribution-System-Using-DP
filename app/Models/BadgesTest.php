<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/Badges.php"; // Adjust the path to your Badges class file

// Helper function to print test results
function printTestResult($testName, $success) {
    echo $testName . ": " . ($success ? "PASSED" : "FAILED") . "\n";
}

// Test 1: Create a new badge
echo "=== Test 1: Create a new badge ===\n";
$badge = new Badges("Gold Tier");
printTestResult("Badge created with ID", $badge->getBadgeID() > 0);

// Test 2: Check for duplicate badge
echo "\n=== Test 2: Check for duplicate badge ===\n";
$duplicateBadge = new Badges("Gold Tier");
printTestResult("Duplicate badge found with same ID", $duplicateBadge->getBadgeID() === $badge->getBadgeID());

// Test 3: Get badge by ID
echo "\n=== Test 3: Get badge by ID ===\n";
$badgeData = $badge->getBadgeByID($badge->getBadgeID());
printTestResult("Badge data retrieved", $badgeData !== null && $badgeData['badgeLvl'] === "Gold Tier");

// Test 4: Get all badges
echo "\n=== Test 4: Get all badges ===\n";
$allBadges = $badge->getAllBadges();
printTestResult("All badges retrieved", is_array($allBadges) && count($allBadges) > 0);

// Test 5: Update badge level
echo "\n=== Test 5: Update badge level ===\n";
$badge->setBadgeLvl("Silver Tier");
$updateResult = $badge->updateBadge();
$updatedBadgeData = $badge->getBadgeByID($badge->getBadgeID());
printTestResult("Badge level updated", $updateResult && $updatedBadgeData['badgeLvl'] === "Silver Tier");

// Test 6: Delete badge
echo "\n=== Test 6: Delete badge ===\n";
$deleteResult = $badge->deleteBadge();
$deletedBadgeData = $badge->getBadgeByID($badge->getBadgeID());
printTestResult("Badge deleted", $deleteResult && $deletedBadgeData === null);

// Test 7: Try to delete a non-existent badge
echo "\n=== Test 7: Try to delete a non-existent badge ===\n";
$nonExistentBadge = new Badges("NonExistent");
try {
    $nonExistentBadge->deleteBadge();
    printTestResult("Deleting non-existent badge", false); // This should not happen
} catch (Exception $e) {
    printTestResult("Deleting non-existent badge throws exception", true);
}

// Test 8: Try to update a non-existent badge
echo "\n=== Test 8: Try to update a non-existent badge ===\n";
$nonExistentBadge = new Badges("NonExistent");
try {
    $nonExistentBadge->updateBadge();
    printTestResult("Updating non-existent badge", false); // This should not happen
} catch (Exception $e) {
    printTestResult("Updating non-existent badge throws exception", true);
}

echo "\n=== All tests completed ===\n";