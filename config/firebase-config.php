<?php 

//Include the autoload file from the Firebase PHP SDK 

require_once "../vendor/autoload.php";


use Kreait\Firebase\Factory; 

$firebase = (new Factory)->withServiceAccount('../config/firebase_credentials.json'); 

$auth = $firebase->createAuth();

//Your Firebase authentication logic goes here 

?>  