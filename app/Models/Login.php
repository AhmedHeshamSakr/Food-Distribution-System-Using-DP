<?php

//require_once 'vendor/autoload.php';  // Autoload Google API Client
require_once __DIR__ . '/../../vendor/autoload.php';
use League\OAuth2\Client\Provider\Google;

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

                // Fetch additional user details from the 'person' table
                $userId = $user['email']; // Assuming email is unique and will match with the person table
                $userDetailsQuery = "SELECT * FROM person WHERE email = '$email'";
                $userDetailsResult = mysqli_query($db, $userDetailsQuery);

                if ($userDetailsResult && mysqli_num_rows($userDetailsResult) > 0) {
                    $this->userData = mysqli_fetch_assoc($userDetailsResult); // Store user data
                }

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

    // Getter to retrieve the user's details after authentication
    public function getUserData() {
        return $this->userData;
    }

    // This function should be used to register a user, storing a hashed password in the database
    public function register($email, $password): bool {
        // Establish the database connection
        $db = Database::getInstance()->getConnection();
    
        // Sanitize inputs
        $email = mysqli_real_escape_string($db, $email);
        $password = mysqli_real_escape_string($db, $password);
    
        // Check if the email already exists in the database
        $queryCheck = "SELECT * FROM login WHERE email = '$email'";
        $resultCheck = mysqli_query($db, $queryCheck);
        
        // If email already exists, return false (email is already taken)
        if (mysqli_num_rows($resultCheck) > 0) {
            return false; // Email already exists
        }
    
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
        // Insert the new user into the database with the hashed password
        $query = "INSERT INTO login (email, password) VALUES ('$email', '$hashedPassword')";
    
        // Execute the query
        if (mysqli_query($db, $query)) {
            return true; // Registration successful
        }
    
        return false; // Registration failed
    }
}


class withGoogle implements iLogin {
    

    private $client;
    private $service;
    private $isAuthenticated = false;
    private $userData;

    public function __construct() {
        $this->client = new Google_Client();
        $this->client->setClientId('713975107412-uamp9g6c9ltmfjblkhvumggrt5r3h659.apps.googleusercontent.com');
        $this->client->setClientSecret('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');
        $this->client->setRedirectUri('http://localhost/Food-Distribution-System-Using-DP/app/Models/Login.php');  
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    // Redirect to Google's OAuth consent screen
    public function login($credentials = null): bool {
        //session_start();
        if (isset($_GET['code'])) {
            $this->client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $this->client->getAccessToken();
            return $this->authenticate();  // Authenticate the user after login
        } else {
            $authUrl = $this->client->createAuthUrl();
            header('Location: ' . $authUrl);  // Redirect to Google OAuth screen
            exit();
        }
    }

    // Authenticate the user by fetching data from Google
    public function authenticate(string $email = null, string $password = null): bool {
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $this->service = new Google_Service_Oauth2($this->client);
            $userInfo = $this->service->userinfo->get();

            if ($userInfo) {
                // Store user data in session or database
                $this->isAuthenticated = true;
                $this->userData = $userInfo;
                return true;  // Authentication success
            }
        }

        return false;  // Authentication failed
    }

    // Logout the user and clear session
    public function logout(): bool {
        $this->isAuthenticated = false;
        unset($_SESSION['access_token']);
        return true;
    }

    // Getter to check if the user is authenticated
    public function isAuthenticated(): bool {
        return $this->isAuthenticated;
    }

    // Getter to retrieve the user's details after authentication
    public function getUserData() {
        return $this->userData;
    }


}




// class withGoogle implements iLogin {
//     private $email;
//     private $password;
//     private $isAuthenticated = false;

//     public function __construct($email, $password) {
//         $this->email = $email;
//         $this->password = $password;
//     }

//     public function login($credentials): bool {
//         // Implement Google authentication logic here
//         $this->isAuthenticated = $this->authenticate($credentials['email'], $credentials['password']);
//         return $this->isAuthenticated;
//     }

//     public function authenticate(string $username, string $password): bool {
//         // Placeholder for Google-specific authentication
//         return true; // Assume success for this example
//     }

//     public function logout(): bool {
//         $this->isAuthenticated = false;
//         return !$this->isAuthenticated;
//     }
// }


// class withFacebook implements iLogin {
//     private $email;
//     private $password;
//     private $isAuthenticated = false;

//     public function __construct($email, $password) {
//         $this->email = $email;
//         $this->password = $password;
//     }

//     public function login($credentials): bool {
//         // Implement Facebook authentication logic here
//         $this->isAuthenticated = $this->authenticate($credentials['email'], $credentials['password']);
//         return $this->isAuthenticated;
//     }

//     public function authenticate(string $username, string $password): bool {
//         // Placeholder for Facebook-specific authentication
//         return true; // Assume success for this example
//     }

//     public function logout(): bool {
//         $this->isAuthenticated = false;
//         return !$this->isAuthenticated;
//     }
// }


?>