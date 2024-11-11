<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'Donor.php'; // Make sure the path is correct
require_once __DIR__ . "/../../config/DB.php"; // Assuming this file has the database connection and utility functions
require_once 'Person.php';
// Create a mock implementation of iLogin for testing

class MockLogin implements iLogin {
    // Implement the authenticate method
    public function authenticate(string $username, string $password): bool {
        // Mock implementation always returns true
        return true;
    }

    // Implement the login method
    public function login(): bool {
        // Mock login method always returns true
        return true;
    }

    // Implement the logout method with correct return type
    public function logout(): bool {
        // Mock logout method always returns true
        return true;
    }

    // Implement the isLoggedIn method
    public function isLoggedIn(): bool {
        // Mock method for checking if logged in
        return true;
    }
}
function printResult($message, $result) {
    echo "<p>{$message}: " . ($result ? '<strong>Success</strong>' : '<strong>Failed</strong>') . "</p>";
}

try {
    echo "<h1>Testing Donor Class</h1>";

    // Sample data for testing
    $userID = 1;
    $firstName = 'John';
    $lastName = 'Doe';
    $email = 'john.doe@example.com';
    $phoneNo = '1234567890';

    // Instantiate the MockLogin object
    $login = new MockLogin();

    // Instantiate the Donor object with the MockLogin instance
    $donor = new Donor($userID, $firstName, $lastName, $email, $phoneNo, $login);

    echo "<p>Donor Created Successfully with User ID: {$donor->getUserID()}</p>";

    // Add a donation
    $donationAmount = 100.50;
    $paymentMethod = 'Credit Card';
    $donationResult = $donor->addDonation($donationAmount, $paymentMethod);
    printResult('Adding Donation', $donationResult);

    // Fetch donation history
    $donationHistory = $donor->fetchDonationHistory();
    echo "<h2>Donation History:</h2>";
    if (!empty($donationHistory)) {
        echo "<ul>";
        foreach ($donationHistory as $donation) {
            echo "<li>Donation ID: {$donation['donationID']}, Date: {$donation['donationDate']}, Amount: {$donation['donationAmount']}, Method: {$donation['paymentMethod']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No donation history found.</p>";
    }

    // Update donor's personal information
    $newFirstName = 'Johnny';
    $newLastName = 'DoeUpdated';
    $newEmail = 'johnny.doe@example.com';
    $newPhoneNo = '0987654321';
    $updateInfoResult = $donor->updatePersonalInfo($newFirstName, $newLastName, $newEmail, $newPhoneNo);
    printResult('Updating Personal Information', $updateInfoResult);

    // Update an existing donation
    if (!empty($donationHistory)) {
        $firstDonationID = $donationHistory[0]['donationID'];
        $newDonationAmount = 150.75;
        $newPaymentMethod = 'Online Payment';
        $updateDonationResult = $donor->updateDonation($firstDonationID, $newDonationAmount, $newPaymentMethod);
        printResult('Updating Donation', $updateDonationResult);
    }

} catch (Exception $e) {
    echo '<p style="color:red;">An error occurred: ' . $e->getMessage() . '</p>';
}

echo "<h1>End of Tests</h1>";

echo "=== End of Tests ===" . PHP_EOL;