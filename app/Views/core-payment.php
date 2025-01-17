<?php



require_once '../../config/config.php';
require_once '../../app/Controllers/PaymentController.php';
require_once '../../app/Models/PaymentModel.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();

}
// Initialize database connection (implement as needed)
$db = database::getInstance()->getConnection();

// Initialize controller
$controller = new PaymentController($db);


// Get payment data
$paymentData = $controller->getPaymentData();


// Render the view
require_once 'PaymentView.php';
renderPaymentView($paymentData['currency'], $paymentData['itemNumber']);
$controller->validatePayment();

