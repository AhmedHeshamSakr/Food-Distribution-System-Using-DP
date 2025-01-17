<?php

require_once __DIR__ . "/../../config/DB.php";
require_once "Person.php";
require_once "User.php";
require_once "Address.php";
require_once "Volunteer.php";
require_once "#a-Badmin.php";
require_once "#a-Eadmin.php";
require_once "#a-Vadmin.php";
interface iLogin {
    public function login($credentials): bool;
    public function authenticate(string $username, string $password): bool;
    public function logout(): bool;

    public static function createUser(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, Address $address, string $nationalID): Person;
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

    public static function createUser(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, Address $address, string $nationalID): Person{
        error_log("User Type ID: $userTypeID");
        error_log("Creat User, " . var_export($nationalID, true));

        switch ($userTypeID) {
            case 1 << 5: 
                return new BadgeAdmin($firstName, $lastName, $email, $phoneNo);
            case 1<< 6: 
                return new EventAdmin($firstName, $lastName, $email, $phoneNo);
            case 1<< 7: 
                return new VerificationAdmin($firstName, $lastName, $email, $phoneNo);
            default:
                return new Volunteer($userTypeID,$firstName, $lastName, $email, $phoneNo, $address, $nationalID, new Badges(badgeLvl:'Silver Tier'));
        }
    }

    // The login method will authenticate the user using the credentials
    public function login($credentials): bool {
        $this->email = $credentials['email'];
        $this->password = $credentials['password'];

        // Check if the email and password are valid by authenticating
        return $this->authenticate($this->email, $this->password);
    }


    public function getUserDetails(string $email): array {

        
        // Query your database to get user details
        $query = "SELECT userID FROM person WHERE email = '$email'";
        $result= run_select_query($query);
        // Return array with user details
        return [
            'userID' => $result[0]['userID'],
        ];
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
    public function register($email, $password, $firstName, $lastName, $phoneNo, $userTypeID, $nationalID, Address $address): bool {
        // Establish the database connection
        $db = Database::getInstance()->getConnection();

        // Sanitize inputs
        $email = mysqli_real_escape_string($db, $email);
        $password = mysqli_real_escape_string($db, $password);
        $firstName = mysqli_real_escape_string($db, $firstName);
        $lastName = mysqli_real_escape_string($db, $lastName);
        $phoneNo = mysqli_real_escape_string($db, $phoneNo);
        $nationalID = mysqli_real_escape_string($db, $nationalID);

        // Check if the email already exists in the database
        $queryCheck = "SELECT * FROM login WHERE email = '$email'";
        $resultCheck = mysqli_query($db, $queryCheck);

        if (mysqli_num_rows($resultCheck) > 0) {
            return false; // Email already exists
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the login data
        $query = "INSERT INTO login (email, password) VALUES ('$email', '$hashedPassword')";
        if (mysqli_query($db, $query)) {
            // After successful registration, get the userID
            $userID = mysqli_insert_id($db);


            error_log("With Email, " . var_export($nationalID, true));

            // Use the factory to create the user object
            $user = $this->createUser($userTypeID, $firstName, $lastName, $email, $phoneNo,$address ,$nationalID);

            // Insert additional user details into the person table
            return true;
            //return $user->saveToDatabase();
        }

        return false; // Registration failed
    }
}


// class withGoogle implements iLogin {
    

//     private $client;
//     private $service;
//     private $isAuthenticated = false;
//     private $userData;

//     public function __construct() {
//         $this->client = new Google_Client();
//         $this->client->setClientId('713975107412-uamp9g6c9ltmfjblkhvumggrt5r3h659.apps.googleusercontent.com');
//         $this->client->setClientSecret('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');
//         $this->client->setRedirectUri('http://localhost/Food-Distribution-System-Using-DP/app/Models/Login.php');  
//         $this->client->addScope('email');
//         $this->client->addScope('profile');
//     }

//     // Redirect to Google's OAuth consent screen
//     public function login($credentials = null): bool {
//         //session_start();
//         if (isset($_GET['code'])) {
//             $this->client->authenticate($_GET['code']);
//             $_SESSION['access_token'] = $this->client->getAccessToken();
//             return $this->authenticate();  // Authenticate the user after login
//         } else {
//             $authUrl = $this->client->createAuthUrl();
//             header('Location: ' . $authUrl);  // Redirect to Google OAuth screen
//             exit();
//         }
//     }

//     // Authenticate the user by fetching data from Google
//     public function authenticate(string $email = null, string $password = null): bool {
//         if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
//             $this->client->setAccessToken($_SESSION['access_token']);
//             $this->service = new Google_Service_Oauth2($this->client);
//             $userInfo = $this->service->userinfo->get();

//             if ($userInfo) {
//                 // Store user data in session or database
//                 $this->isAuthenticated = true;
//                 $this->userData = $userInfo;
//                 return true;  // Authentication success
//             }
//         }

//         return false;  // Authentication failed
//     }

//     // Logout the user and clear session
//     public function logout(): bool {
//         $this->isAuthenticated = false;
//         unset($_SESSION['access_token']);
//         return true;
//     }

//     // Getter to check if the user is authenticated
//     public function isAuthenticated(): bool {
//         return $this->isAuthenticated;
//     }

//     // Getter to retrieve the user's details after authentication
//     public function getUserData() {
//         return $this->userData;
//     }


// }




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

// <?php

// require_once __DIR__ . "/../../config/DB.php";
// require_once "Person.php";
// require_once "Volunteer.php";

// class withEmail {
//     private $email;
//     private $password;

//     public function __construct($email, $password) {
//         $this->email = $email;
//         $this->password = $password;
//     }

//     // Login method
//     public function login(): bool {
//         // Establish database connection
//         $db = Database::getInstance()->getConnection();

//         // Sanitize inputs
//         $email = mysqli_real_escape_string($db, $this->email);
//         $password = mysqli_real_escape_string($db, $this->password);

//         // Query to fetch user by email
//         $query = "SELECT * FROM login WHERE email = '$email'";
//         $result = mysqli_query($db, $query);

//         // Check if user exists
//         if ($result && mysqli_num_rows($result) > 0) {
//             $user = mysqli_fetch_assoc($result);

//             // Verify password
//             if (password_verify($password, $user['password'])) {
//                 // Password is correct
//                 return true;
//             }
//         }

//         // Login failed
//         return false;
//     }

//     // Register method
//     public function register(
//         $email, 
//         $password, 
//         $firstName, 
//         $lastName, 
//         $phoneNo, 
//         $addressID, 
//         $nationalID, 
//         $badgeID, 
//         $userTypeID = 0
//     ): bool {
//         // Establish the database connection
//         $db = Database::getInstance()->getConnection();

//         // Sanitize inputs
//         $email = mysqli_real_escape_string($db, $email);
//         $password = mysqli_real_escape_string($db, $password);
//         $firstName = mysqli_real_escape_string($db, $firstName);
//         $lastName = mysqli_real_escape_string($db, $lastName);
//         $phoneNo = mysqli_real_escape_string($db, $phoneNo);
//         $addressID = mysqli_real_escape_string($db, $addressID);
//         $nationalID = mysqli_real_escape_string($db, $nationalID);
//         $badgeID = mysqli_real_escape_string($db, $badgeID);

//         // Check if the email already exists in the database
//         $queryCheck = "SELECT * FROM login WHERE email = '$email'";
//         $resultCheck = mysqli_query($db, $queryCheck);

//         // If email already exists, return false (email is already taken)
//         if (mysqli_num_rows($resultCheck) > 0) {
//             return false; // Email already exists
//         }

//         // Hash the password before storing it
//         $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

//         // Insert the new user into the login table with the hashed password
//         $query = "INSERT INTO login (email, password) VALUES ('$email', '$hashedPassword')";

//         // Execute the query to insert login details
//         if (mysqli_query($db, $query)) {
//             // After successful registration, get the userID
//             $userID = mysqli_insert_id($db); // Get the last inserted ID (userID)

//             // Insert the user's general information into the `person` table
//             $queryPerson = "INSERT INTO person (userID, firstName, lastName, email, phoneNo, userTypeID) 
//                             VALUES ('$userID', '$firstName', '$lastName', '$email', '$phoneNo', '$userTypeID')";
            
//             if (mysqli_query($db, $queryPerson)) {
//                 // Insert the user's volunteer-specific information into the `volunteer` table
//                 $queryVolunteer = "INSERT INTO volunteer (userID, nationalID, address, badge) 
//                                    VALUES ('$userID', '$nationalID', '$addressID', '$badgeID')";

//                 // If volunteer information is successfully inserted, return true
//                 if (mysqli_query($db, $queryVolunteer)) {
//                     return true;
//                 }
//             }
//         }

//         return false; // Registration failed
//     }

//     // Logout method (optional, placeholder for session management)
//     public function logout() {
//         // Example implementation: clear session or token
//         session_start();
//         session_unset();
//         session_destroy();
//     }
// }


?>