<?php
require_once '../../config/DB.php';
require_once 'PaymentModel.php'; // Adjust the path as needed

// Create a database connection

// Function to test storeTransaction
function testStoreTransaction() {
    echo "Starting storeTransaction test...\n";

    // Test data
    $amount = 100.50;
    $userID = 41; // Replace with a valid user ID in your database
    $date = date("Y-m-d H:i:s");

    // Create a transaction instance
    $transaction = new Transaction($amount, $userID, $date);

    // Call the storeTransaction method and print the result
    if ($transaction->storeTransaction()) {
        
        print('<br> TransactionID: ' . $transaction->getTransactionID());
        echo "<br>Transaction stored successfully!\n";
    } else {
        echo "<br>Failed to store the transaction.\n";
    }

    print('<br> Updating the transaction amount to 200.75...');
    $transaction->setAmount(200.75);
    print('<br> New transaction amount: ' . $transaction->getAmount() . '<br>');
}

try {
    // Run the test
    testStoreTransaction();
} catch (Exception $e) {
    echo "An error occurred during testing: " . $e->getMessage() . "\n";
}
