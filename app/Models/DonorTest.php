<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Required class imports
require_once 'Donor.php'; 
require_once __DIR__ . "/../../config/DB.php"; // Ensure this path is correct
require_once 'Person.php';
require_once 'Login.php';
require_once 'Payment.php'; // Include payment strategies and PaymentContext

try {
    echo "<h1>Testing Donor Class with Payment Strategies</h1>";

    // Sample data for testing
    $userID = 1;
    $firstName = 'John';
    $lastName = 'Doe';
    $email = 'john.doe@example.com';
    $phoneNo = '1234567890';


    // Attempt to log in with the provided credentials
    if (!$login->login(['email' => $email, 'password' => 'password'])) {
        throw new Exception("Login failed for Google authentication.");
    }

    echo "<p>Successfully authenticated with Google for user: {$email}</p>";

    try {
        $db = Database::getInstance()->getConnection();
        $donor = new Donor($userID, $firstName, $lastName, $email, $phoneNo);
        
         $paymentDetails =77777 ;
         //[
        //     'cardNumber' => '1234-5678-9876-5432',
        //     'expiryDate' => '2025-12-31',
        //     'cvv' => '123'
        // ];
    
        $donationResult = $donor->addDonation(100.50, 'Credit Card', $paymentDetails);
        echo $donationResult ? "<p>Donation added successfully.</p>" : "<p>Failed to add donation.</p>";
        
    } catch (Exception $e) {
        echo '<p style="color:red;">Error: ' . $e->getMessage() . '</p>';
    }
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
    echo $updateInfoResult ? "<p>Personal information updated successfully.</p>" : "<p>Failed to update personal information.</p>";

    // Update an existing donation if history exists
    if (!empty($donationHistory)) {
        $firstDonationID = $donationHistory[0]['donationID'];
        $newDonationAmount = 150.75;

        // Example: Dynamic payment details for updating a donation (Fawry payment)
        $paymentMethod = 'Fawry';
        $paymentDetails = [
            'fawryNumber' => 'FAW123456789',
            'referenceID' => '4567'
        ];

        // Update the donation in the database
        $updateDonationResult = $donor->updateDonation($firstDonationID, $newDonationAmount, $paymentMethod);
        echo $updateDonationResult ? "<p>Donation updated successfully.</p>" : "<p>Failed to update donation.</p>";
    }

} catch (InvalidArgumentException $e) {
    echo '<p style="color:red;">Invalid Argument: ' . $e->getMessage() . '</p>';
} catch (Exception $e) {
    echo '<p style="color:red;">An unexpected error occurred: ' . $e->getMessage() . '</p>';
} finally {
    echo "<h1>End of Tests</h1>";
    echo "=== End of Tests ===" . PHP_EOL;
}