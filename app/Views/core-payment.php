<?php



require_once '../../config/config.php';
require_once '../../app/Controllers/PaymentController.php';
require_once '../../app/Models/PaymentModel.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();

}
// Initialize database connection (implement as needed)
$db = null; // Replace with your database connection

// Initialize controller
$controller = new PaymentController($db);

// Get payment data
$paymentData = $controller->getPaymentData();

// Handle payment validation if POST request
$controller->validatePayment();

// Render the view
require_once 'PaymentView.php';
renderPaymentView($paymentData['currency'], $paymentData['itemNumber']);
$controller->validatePayment();

