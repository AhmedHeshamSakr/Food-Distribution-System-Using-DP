<?php
// Include necessary files (make sure these are correct paths)
require_once __DIR__ . '/../../config/DB.php';
require_once 'Person.php';
require_once 'Login.php';  // Make sure Login.php has the necessary login logic
//require_once 'withEmail.php';  // This contains your withEmail class

// Create a new instance of withEmail to handle registration and login
$email = 'testuser@example.com';
$password = 'securePassword123'; // Password used for registration

// Create the withEmail object
$login = new withEmail($email, $password);

// Step 1: Test Registration
echo "Attempting to register with email: $email\n";

// Register the user
// $registerResult = $login->register($email, $password);
if ($registerResult) {
    echo "Registration successful!\n";
} else {
    echo "Registration failed. The email might already be taken.\n";
}

// Step 2: Test Login with the same credentials
echo "Attempting to login with email: $email\n";

// Prepare login credentials
$loginCredentials = [
    'email' => $email,
    'password' => $password
];

// Try to login
$loginResult = $login->login($loginCredentials);
if ($loginResult && $login->isAuthenticated()) {
    echo "Login successful!\n";
    
    // Fetch and display user data after successful login
    // $userData = $login->getUserData();
    echo "User Data:\n";
    print_r($userData);  // This will print the user's details from the 'person' table
} else {
    echo "Login failed. Incorrect credentials.\n";
}

?>