<?php
// Include the configuration file
require_once '../../config/config.php' ;
// Include the database connection file 
include_once '../../config/DB.php'; 

$payment_ref_id = $statusMsg = '';
$status = 'error';

// Check whether the payment ID is not empty
if (!empty($_GET['checkout_ref_id'])) {
    $payment_txn_id = base64_decode($_GET['checkout_ref_id']);

    // Fetch transaction data from the database
    $sqlQ = "SELECT id, payer_id, payer_name, payer_email, payer_country, order_id, transaction_id, paid_amount, paid_amount_currency, payment_source, payment_status, created FROM transactions WHERE transaction_id = ?";
    $stmt = $db->prepare($sqlQ);
    $stmt->bind_param("s", $payment_txn_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Get transaction details
        $stmt->bind_result($payment_ref_id, $payer_id, $payer_name, $payer_email, $payer_country, $order_id, $transaction_id, $paid_amount, $paid_amount_currency, $payment_source, $payment_status, $created);
        $stmt->fetch();

        $status = 'success';
        $statusMsg = 'Your Donation has been Successful!';
    } else {
        $statusMsg = "Transaction has been failed!";
    }
} else {
    header("Location: index.php");
    exit;
}
?>

<!-- Display payment status and details -->
<!-- <?php if (!empty($payment_ref_id)) { ?>
    <h1 class="<?php echo $status; ?>"><?php echo $statusMsg; ?></h1>
    
    <h4>Donation Information</h4>
    <p><b>Reference Number:</b> <?php echo $payment_ref_id; ?></p>
    <p><b>Order ID:</b> <?php echo $order_id; ?></p>
    <p><b>Transaction ID:</b> <?php echo $transaction_id; ?></p>
    <p><b>Donation Amount:</b> <?php echo $paid_amount . ' ' . $paid_amount_currency; ?></p>
    <p><b>Payment Status:</b> <?php echo $payment_status; ?></p>
    <p><b>Date:</b> <?php echo $created; ?></p>
    
    <h4>Donor Information</h4>
    <p><b>ID:</b> <?php echo $payer_id; ?></p>
    <p><b>Name:</b> <?php echo $payer_name; ?></p>
    <p><b>Email:</b> <?php echo $payer_email; ?></p>
    <p><b>Country:</b> <?php echo $payer_country; ?></p>
<?php } else { ?>
    <h1 class="error">Your Donation has failed!</h1>
    <p class="error"><?php echo $statusMsg; ?></p>
<?php } ?> -->