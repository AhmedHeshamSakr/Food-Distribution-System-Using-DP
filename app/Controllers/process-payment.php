<?php
require_once '../../config/config.php';
require_once '../../app/Controllers/PaymentController.php';
require_once '../../app/Models/PaymentModel.php';

// Initialize database connection
$db = database::getInstance()->getConnection();

// Initialize controller
$controller = new PaymentController($db);

// Process the payment
//$controller->validatePayment();

// PaymentView.php (modified fetch URL)
// In the JavaScript section, update the fetch call:
?>
<script>
fetch('process-payment.php', {
    method: 'POST',
    headers: { 'Accept': 'application/json' },
    body: encodeFormData(postData)
})
</script>
