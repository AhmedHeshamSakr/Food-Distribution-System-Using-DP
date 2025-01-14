<?php 

//Include the autoload file from the Firebase PHP SDK 

require_once __DIR__ . "/../vendor/autoload.php";


use Kreait\Firebase\Factory; 

use Kreait\Firebase\ServiceAccount; 

// Path to your Firebase service account JSON file 

$serviceAccount = ServiceAccount::fromJsonFile("firebase_credentials.json"); 



$firebase = (new Factory) 

    ->withServiceAccount($serviceAccount) 

    ->create(); 

$auth = $firebase->createAuth();

//Your Firebase authentication logic goes here 

?>  