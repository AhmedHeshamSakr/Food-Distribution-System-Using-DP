<?php

require_once '../../config/config.php';

// Assuming this script is called after payment approval

$donation_amount = $_POST['donation_amount'];
$userID = 41; // This should be dynamically set based on the user
$transaction_date = date("Y-m-d H:i:s");

$transaction = new Transaction($donation_amount, $userID, $transaction_date);
error_log('Amount in Transaction object: ' . $transaction->getAmount());

$transaction->storeTransaction();

// Additional processing can be done here

?>