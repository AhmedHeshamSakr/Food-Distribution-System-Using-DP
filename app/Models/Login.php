<?php
require_once __DIR__ . "/../../config/firebase-config.php";


use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
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


class withGoogle implements iLogin
{
    private $auth;
    private $isAuthenticated = false;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        error_log("Firebase Auth object is null!");
    }

    public function login($credentials): bool
{
    error_log("Starting Google login process...");
    try {
        $idToken = $credentials['idToken'] ?? '';
        if (!$idToken) {
            error_log("No ID Token provided.");
            return false;
        }

        error_log("Verifying ID token: $idToken");
        $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        error_log("ID Token verified successfully.");

        $email = $verifiedIdToken->claims()->get('email') ?? '';
        $firstName = $verifiedIdToken->claims()->get('given_name') ?? '';
        $lastName = $verifiedIdToken->claims()->get('family_name') ?? '';

        if (!$email) {
            error_log("Failed to extract email from token claims.");
            return false;
        }

        error_log("Extracted user details: Email=$email, FirstName=$firstName, LastName=$lastName");

        if ($this->checkIfUserExists($email)) {
            error_log("User exists in the database.");
            $this->isAuthenticated = true;
            return true;
        }

        error_log("User does not exist. Creating new user...");
        $addressObject = new Address("Google Address", null, "City");
        if (!$addressObject->create()) {
            error_log("Failed to create address.");
            return false;
        }

        $user = $this->createUser(0, $firstName, $lastName, $email, "", $addressObject, "");
        $db = Database::getInstance()->getConnection();
        $query = "INSERT INTO login (email) VALUES ('$email')";

        if (!mysqli_query($db, $query)) {
            error_log("Failed to insert user into login table: " . mysqli_error($db));
            return false;
        }

        error_log("User successfully created and logged in.");
        $this->isAuthenticated = true;
        return true;
    } catch (FailedToVerifyToken $e) {
        error_log("Token verification failed: " . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log("Unexpected error: " . $e->getMessage());
        return false;
    }
}


    public function authenticate(string $username, string $password): bool
    {
        return false;
    }

    public function logout(): bool
    {
        $this->isAuthenticated = false;
        return true;
    }

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public static function createUser(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, Address $address, string $nationalID): Person
    {
        error_log("Creating User from Google Login with User Type ID: $userTypeID");

        switch ($userTypeID) {
            case 1 << 5:
                return new BadgeAdmin($firstName, $lastName, $email, $phoneNo);
            case 1 << 6:
                return new EventAdmin($firstName, $lastName, $email, $phoneNo);
            case 1 << 7:
                return new VerificationAdmin($firstName, $lastName, $email, $phoneNo);
            default:
                return new Volunteer($userTypeID, $firstName, $lastName, $email, $phoneNo, $address, $nationalID, new Badges(badgeLvl: 'Silver Tier'));
        }
    }

    private function checkIfUserExists(string $email): bool
    {
        error_log("Checking if user exists for email: $email");

        $db = Database::getInstance()->getConnection();
        if (!$db) {
            error_log("Database connection failed: " . mysqli_connect_error());
            return false;
        }

        $email = mysqli_real_escape_string($db, $email);
        $query = "SELECT * FROM login WHERE email = '$email'";
        error_log("Executing query: $query");

        $result = mysqli_query($db, $query);
        if (!$result) {
            error_log("Query failed: " . mysqli_error($db));
            return false;
        }

        $userExists = mysqli_num_rows($result) > 0;
        error_log("User exists: " . ($userExists ? "Yes" : "No"));
        return $userExists;
    }

}


?>