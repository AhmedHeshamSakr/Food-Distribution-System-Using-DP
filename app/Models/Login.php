<?php
require_once __DIR__ . "/../../config/firebase-config.php";

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
require_once __DIR__ . "/../../config/DB.php";
require_once "Person.php";
require_once "User.php";
require_once "Address.php";
require_once "Volunteer.php";
require_once "#a-Badmin.php";
require_once "#a-Eadmin.php";
require_once "a-Vadmin.php";
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
    public static function createUser(int $userTypeID, string $firstName, string $lastName, 
    string $email, string $phoneNo, Address $address, string $nationalID): Person{
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
                return new Volunteer($userTypeID,$firstName, $lastName, $email, $phoneNo, $address, 
                $nationalID, new Badges(badgeLvl:'Silver Tier'));
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
        $db = Database::getInstance()->getConnection();
        $email = mysqli_real_escape_string($db, $email);
        $query = "SELECT * FROM login WHERE email = '$email'";
        $result = mysqli_query($db, $query);
        
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



require_once 'FirebaseFacade.php';

class withGoogle implements iLogin {
    private $firebaseFacade;
    private $isAuthenticated = false;

    public function __construct(FirebaseFacade $firebaseFacade) {
        $this->firebaseFacade = $firebaseFacade;
    }

    public function login($userData): bool {
        $token_id = $userData["token"];
        
        // Log user data via facade
        if ($this->authenticate($token_id)) {
            $this->checkUserExistence($userData);
            return true;
        }
        return false;
    }

    public function logout(): bool {
        $this->isAuthenticated = false;
        return true;
    }
    public function authenticate(string $credentials): bool {
        try {
    
            $verifiedIdToken = $firebaseFacade->auth->verifyIdToken($credentials,false,360000);
             // Extract user details from the token claims
             $uid = $verifiedIdToken->claims()->get('sub');
             $email = $verifiedIdToken->claims()->get('email');
             $firstName = $verifiedIdToken->claims()->get('given_name') ?? '';
             $lastName = $verifiedIdToken->claims()->get('family_name') ?? '';
    
            $uid = $verifiedIdToken->claims()->get('sub');
        
            $user = $auth->getUser($uid);
            
            $this->isAuthenticated = true;
            return true;
        } catch (FailedToVerifyToken $e) {
            $this->isAuthenticated = false;
            return false;
        }
    }
    public function isAuthenticated(): bool {
        return $this->isAuthenticated;
    }
    private function checkUserExistence($userData) {
        $email = $userData['email'];
       
        $db = Database::getInstance()->getConnection();
        $queryCheck = "SELECT * FROM login WHERE email = '$email'";
        $resultCheck = mysqli_query($db, $queryCheck);
        if (mysqli_num_rows($resultCheck) > 0) {
            // User exists
            error_log("User exists in the database.");
        } else {
            // User doesn't exist, create new user
            error_log("User does not exist, creating new user.");
            $this->createUser($userData);
        }
    }

    // Create a new user in the database
    private function createUser($userData) {
        $db = Database::getInstance()->getConnection();
        
        $email = mysqli_real_escape_string($db, $userData['email']);
        $firstName = mysqli_real_escape_string($db, $userData['name']);
        
        // Insert the login data into the database
        $query = "INSERT INTO login (email, first_name) VALUES ('$email', '$firstName')";
        
        if (mysqli_query($db, $query)) {
            error_log("User successfully created in the database.");
        } else {
            error_log("Error inserting user into database: " . mysqli_error($db));
        }
    }
}

// class withGoogle implements iLogin {
//     private $firebaseFacade;
//     private $isAuthenticated = false;
//     private $userName;
//     private $userEmail;

//     public function __construct() {
//         // Initialize the FirebaseFacade
//         $this->firebaseFacade = new FirebaseFacade();
//     }

//     // Login method, handles Firebase integration and application logic
//     public function login($userData): bool {
//         try {
//             $this->userName = $userData['name'] ?? 'Unknown';
//             $this->userEmail = $userData['email'] ?? 'Unknown';

//             // Log the user data using the facade
//             $this->firebaseFacade->logUserData($this->userName, $this->userEmail);

//             // Check if the user already exists in the database
//             if ($this->checkIfUserExists($this->userEmail)) {
//                 $this->isAuthenticated = true;
//                 return true;
//             }

//             // If the user doesn't exist, create a new user
//             $addressObject = new Address("Google Address", null, "City");
//             if (!$addressObject->create()) {
//                 throw new Exception("Failed to create address.");
//             }

//             // Create and store the user
//             $user = $this->createUser(0, $this->userName, "", $this->userEmail, "", $addressObject, "");
//             $db = Database::getInstance()->getConnection();
//             $query = "INSERT INTO login (email) VALUES ('" . mysqli_real_escape_string($db, $this->userEmail) . "')";
//             if (!mysqli_query($db, $query)) {
//                 throw new Exception("Failed to insert user into login table: " . mysqli_error($db));
//             }

//             $this->isAuthenticated = true;
//             return true;
//         } catch (Exception $e) {
//             error_log("Error during login: " . $e->getMessage());
//             return false;
//         }
//     }

//     public function logout(): bool {
//         $this->isAuthenticated = false;
//         return true;
//     }

//     public function isAuthenticated(): bool {
//         return $this->isAuthenticated;
//     }

//     public static function createUser(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, Address $address, string $nationalID): Person {
//         switch ($userTypeID) {
//             case 1 << 5:
//                 return new BadgeAdmin($firstName, $lastName, $email, $phoneNo);
//             case 1 << 6:
//                 return new EventAdmin($firstName, $lastName, $email, $phoneNo);
//             case 1 << 7:
//                 return new VerificationAdmin($firstName, $lastName, $email, $phoneNo);
//             default:
//                 return new Volunteer($userTypeID, $firstName, $lastName, $email, $phoneNo, $address, $nationalID, new Badges(badgeLvl: 'Silver Tier'));
//         }
//     }

//     private function checkIfUserExists(string $email): bool {
//         $db = Database::getInstance()->getConnection();
//         $email = mysqli_real_escape_string($db, $email);
//         $query = "SELECT * FROM login WHERE email = '$email'";
//         $result = mysqli_query($db, $query);

//         return $result && mysqli_num_rows($result) > 0;
//     }
// }

?>