<?php
// Include the autoloader and the Google login class
require_once __DIR__ . '/../../vendor/autoload.php';
require_once 'Login.php';  // Include the Google login class you created

// Start a session at the very beginning of the file, and only once
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Create a new instance of the withGoogle class
$googleLogin = new withGoogle();

// Step 1: Call the login function to redirect the user to Google for authentication
if (isset($_GET['code'])) {
    // If the user is redirected back with 'code', we authenticate them
    if ($googleLogin->login()) {
        echo "You are logged in with Google.<br>";
        $userData = $googleLogin->getUserData();
        echo "User Info: <br>";
        echo "Name: " . $userData['name'] . "<br>";
        echo "Email: " . $userData['email'] . "<br>";
        echo "Profile Picture: <img src='" . $userData['picture'] . "' alt='Profile Picture'><br>";
    } else {
        echo "Failed to authenticate with Google.";
    }
} else {
    // If the user is not authenticated, redirect to Google login
    // Ensure no output before the redirect
    $googleLogin->login();  // This will redirect to Google login page
    exit(); // Stop further execution to prevent the script from continuing after the redirect
}
?>
