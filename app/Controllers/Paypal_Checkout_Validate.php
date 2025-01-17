<?php 
// Include the configuration file 
require_once 'C:\xampp\htdocs\FDS\config\config.php'; 
 
// Include the database connection file 
include_once 'C:\xampp\htdocs\FDS\config\DB.php'; 
 
// Include the PayPal API library 
require_once 'C:\xampp\htdocs\FDS\app\Models\PayPalCheckout.php'; 
require_once '../Models/PaymentModel.php';
$paypal = new PaypalCheckout; 
 
$response = array('status' => 0, 'msg' => 'Transaction Failed!'); 
if(!empty($_POST['paypal_order_check']) && !empty($_POST['order_id'])){ 
    // Validate and get order details with PayPal API 
    try {  
        $order = $paypal->validate($_POST['order_id']); 
    } catch(Exception $e) {  
        $api_error = $e->getMessage();  
    } 
     
    if(!empty($order)){ 
        $order_id = $order['id']; 
        $intent = $order['intent']; 
        $order_status = $order['status']; 
        $order_time = date("Y-m-d H:i:s", strtotime($order['create_time'])); 
 
        if(!empty($order['purchase_units'][0])){ 
            $purchase_unit = $order['purchase_units'][0]; 
 
            $item_number = $purchase_unit['custom_id']; 
            $item_name = $purchase_unit['description']; 
             
            if(!empty($purchase_unit['amount'])){ 
                $currency_code = $purchase_unit['amount']['currency_code']; 
                $amount_value = $purchase_unit['amount']['value']; 
            } 
 
            if(!empty($purchase_unit['payments']['captures'][0])){ 
                $payment_capture = $purchase_unit['payments']['captures'][0]; 
                $transaction_id = $payment_capture['id']; 
                $payment_status = $payment_capture['status']; 
            } 
 
            if(!empty($purchase_unit['payee'])){ 
                $payee = $purchase_unit['payee']; 
                $payee_email_address = $payee['email_address']; 
                $merchant_id = $payee['merchant_id']; 
            } 
        } 
 
        $payment_source = ''; 
        if(!empty($order['payment_source'])){ 
            foreach($order['payment_source'] as $key=>$value){ 
                $payment_source = $key; 
            } 
        } 
 
        if(!empty($order['payer'])){ 
            $payer = $order['payer']; 
            $payer_id = $payer['payer_id']; 
            $payer_name = $payer['name']; 
            $payer_given_name = !empty($payer_name['given_name'])?$payer_name['given_name']:''; 
            $payer_surname = !empty($payer_name['surname'])?$payer_name['surname']:''; 
            $payer_full_name = trim($payer_given_name.' '.$payer_surname); 
            $payer_full_name = filter_var($payer_full_name, FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH); 
 
            $payer_email_address = $payer['email_address']; 
            $payer_address = $payer['address']; 
            $payer_country_code = !empty($payer_address['country_code'])?$payer_address['country_code']:''; 
        } 
 
        if(!empty($order_id) && $order_status == 'COMPLETED'){ 
            // Insert transaction data into the database
            $db = database::getInstance()->getConnection();
            $transaction = new Transaction($amount, $userID, $date);
 
            if(!empty($payment_id)){ 
                $ref_id_enc = base64_encode($transaction_id); 
                $response = array('status' => 1, 'msg' => 'Transaction completed!', 'ref_id' => $ref_id_enc); 
            } 
        } 
    }else{ 
        $response['msg'] = $api_error; 
    } 
} 
echo json_encode($response); 
?>