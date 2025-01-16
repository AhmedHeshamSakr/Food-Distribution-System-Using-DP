<?php 

//Include the autoload file from the Firebase PHP SDK 

require_once 'D:/xampp/htdocs/sdp project/Food-Distribution-System-Using-DP/vendor/autoload.php';



use Kreait\Firebase\Factory; 

$firebase = (new Factory)->withServiceAccount(__DIR__ . '/firebase_credentials.json');


$auth = $firebase->createAuth();

//Your Firebase authentication logic goes here 

?>  