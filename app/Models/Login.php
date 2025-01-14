<?php
require_once __DIR__ . "/../../config/firebase-config.php";


use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

require_once __DIR__ . "/../../config/DB.php";
require_once "User.php";
interface iLogin {
    public function login($credentials): bool;
    public function authenticate(string $username, string $password): bool;
    public function logout(): bool;
}


class withEmail implements iLogin {
    private $email;
    private $password;
    private $isAuthenticated = false;
    private $userData; // to Store user data after login from person table

    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }

    // The login method will authenticate the user using the credentials
    // The login method will authenticate the user using the credentials
    public function login($credentials): bool {
        $this->email = $credentials['email'];
        $this->password = $credentials['password'];

        // Check if the email and password are valid by authenticating
        return $this->authenticate($this->email, $this->password);
    }

    // This method authenticates the user by checking email and password in the database
    public function authenticate(string $email, string $password): bool {
        // Establish the database connection
        $db = Database::getInstance()->getConnection();
        // Sanitize inputs (to prevent SQL injection)
        $email = mysqli_real_escape_string($db, $email);
        // Query to fetch the user based on email
        $query = "SELECT * FROM login WHERE email = '$email'";
        // Execute the query
        $result = mysqli_query($db, $query);
        // Check if the user exists
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            // Now check if the password matches using password_verify
            if (password_verify($password, $user['password'])) {
                // If password matches, set authentication to true
                $this->isAuthenticated = true;
                return true; // Authentication success
            }
        }
        // Authentication failed
        $this->isAuthenticated = false;
        return false;
    }
    // The logout method simply sets authentication to false
    public function logout(): bool {
        $this->isAuthenticated = false;
        return true;
    }
    // Getter to check if the user is authenticated
    public function isAuthenticated(): bool {
        return $this->isAuthenticated;
    }

    // Add this method to the withEmail class
    public function getEmail(): ?string
    {
        if ($this->isAuthenticated) {
            return $this->email;
        }
        return null;
    }

    // This function should be used to register a user, storing a hashed password in the database
    public function register($email, $password, $firstName, $lastName, $phoneNo, $userTypeID=0): bool {
        // Establish the database connection
        $db = Database::getInstance()->getConnection();
    
        // Sanitize inputs
        $email = mysqli_real_escape_string($db, $email);
        $password = mysqli_real_escape_string($db, $password);
        $firstName = mysqli_real_escape_string($db, $firstName);
        $lastName = mysqli_real_escape_string($db, $lastName);
        $phoneNo = mysqli_real_escape_string($db, $phoneNo);
    
        // Check if the email already exists in the database
        $queryCheck = "SELECT * FROM login WHERE email = '$email'";
        $resultCheck = mysqli_query($db, $queryCheck);
    
        // If email already exists, return false (email is already taken)
        if (mysqli_num_rows($resultCheck) > 0) {
            return false; // Email already exists
        }
    
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
        // Insert the new user into the login table with the hashed password
        $query = "INSERT INTO login (email, password) VALUES ('$email', '$hashedPassword')";
    
        // Execute the query to insert login details
        if (mysqli_query($db, $query)) {
            // After successful registration, get the userID
            $userID = mysqli_insert_id($db); // Get the last inserted ID (userID)
            $login = new withEmail($email, 'password');
            // Create an instance of the Person class and insert extra information
            $User = new User($userTypeID=0, $firstName, $lastName, $email, $phoneNo);
            // If person is successfully inserted, return true
            return true;
        }
    
        return false; // Registration failed
    }
}


class withGoogle implements iLogin {
    private $auth;

    public function __construct($auth) {
        $this->auth = $auth;
    }

    public function login($credentials): bool {
        try {
            // Verify the ID token received from the frontend
            $idToken = $credentials['idToken']; // Ensure credentials include the ID token
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);

            // Retrieve user details
            $uid = $verifiedIdToken->claims()->get('sub'); // User's unique ID
            $email = $verifiedIdToken->claims()->get('email'); // User's email

            // Log success (can store $uid and $email for later use)
            return true; // Login successful
        } catch (FailedToVerifyToken $e) {
            // Log or handle error
            return false; // Login failed
        }
    }

    public function authenticate(string $username, string $password): bool {
        // Google login does not require username/password
        return false; // Not applicable
    }

    public function logout(): bool {
        // Google login session management can be handled on the client-side
        return true; // Logout successful
    }
}


?>