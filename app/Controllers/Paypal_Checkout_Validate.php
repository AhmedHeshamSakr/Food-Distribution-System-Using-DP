<?php
// Include the configuration file
require_once 'C:\xampp\htdocs\FDS\config\config.php';

// Include the database connection file
require_once 'C:\xampp\htdocs\FDS\config\DB.php';

// Get the raw POST data
$data = file_get_contents('php://input');
$postData = json_decode($data, true);

// Check if the donation amount is provided
if (isset($postData['donation_amount'])) {
    $donationAmount = $postData['donation_amount'];
    $orderId = $postData['order_id'];

    // Validate the PayPal order (existing code)
    $paypalCheckout = new PaypalCheckout();
    $orderDetails = $paypalCheckout->validate($orderId);

    if ($orderDetails) {
        // Save the transaction to the database with the donation amount
        $sql = "INSERT INTO transactions (payer_id, payer_name, payer_email, payer_country, order_id, transaction_id, paid_amount, paid_amount_currency, payment_source, payment_status, created) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssssdsss", $orderDetails['payer']['payer_id'], $orderDetails['payer']['name']['given_name'], $orderDetails['payer']['email_address'], $orderDetails['payer']['address']['country_code'], $orderDetails['id'], $orderDetails['purchase_units'][0]['payments']['captures'][0]['id'], $donationAmount, $orderDetails['purchase_units'][0]['amount']['currency_code'], 'PayPal', 'Completed');
        $stmt->execute();

        // Return success response
        echo json_encode(['status' => 1, 'ref_id' => base64_encode($orderDetails['id'])]);
    } else {
        // Return error response
        echo json_encode(['status' => 0, 'msg' => 'Transaction validation failed.']);
    }
} else {
    // Return error response if donation amount is missing
    echo json_encode(['status' => 0, 'msg' => 'Donation amount is required.']);
}
?>