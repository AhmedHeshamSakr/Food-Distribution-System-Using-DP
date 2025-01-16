<?php

// Product Details 
$itemNumber = "DP12345"; 
$itemName = "Donation"; 
$currency = "USD"; 
 
/* PayPal REST API configuration 
 * You can generate API credentials from the PayPal developer panel. 
 * See your keys here: https://developer.paypal.com/dashboard/ 
 */ 
define('PAYPAL_SANDBOX', TRUE); //TRUE=Sandbox | FALSE=Production 
define('PAYPAL_SANDBOX_CLIENT_ID', 'Ad4cV5mkDEL8PIoEYL9Sho5eqqUvzZa6Wdf9ESPBYPb4CcKuy0VlCpdpho_NTxBX2SlHuHbkRkeDsCrg'); 
define('PAYPAL_SANDBOX_CLIENT_SECRET', 'EAsOK_EyINsOx3RTbyQdn05RT37KjvMwgxuVgTfFFnKeZREpcnwXTiOw2nrAY160hARsc3Kb0tKzXzWH'); 
//define('PAYPAL_PROD_CLIENT_ID', 'Insert_Live_PayPal_Client_ID_Here'); YU&

return(object)array(

        // Database configuration
        'DB_SERVER'=>'localhost',
        'DB_PORT' => 3307, //!!!!!!!!!!!!!!COMMENT THIS OUT IF YOU ARE NOT JUMANA!!!!!!!!!!!!!!!!!!!!!!
        'DB_USERNAME'=>'root', 
        'DB_PASSWORD'=>'',     
        'DB_DATABASE'=> 'fds',  

);