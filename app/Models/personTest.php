<?php
echo "PHP is running!";

require_once '../../config/DB.php';

require_once 'Person.php';


// Dummy iLogin for testing
$dummyLogin = new DummyLogin();

// Create a new person instance for testing (using dummy data)
$person = new class(1, 'Nina', 'Richie', 'john.ddoe@example.com', '1234567890', $dummyLogin) extends Person {
    public function __construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login) {
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }
};

// Test Insert
$userTypeID = 1;
$firstName = 'Jane';
$lastName = 'Smith';
$email = 'jane.smith@example.com';
$phoneNo = '0987654321';



// Test Select Query (get person by email for example)
$query = "SELECT * FROM person WHERE email = 'jane.smith@example.com'";
$selectResult = run_select_query($query, true);  // Output the result to check if it's correct
echo $selectResult ? "Select Query Success\n" : "Select Query Failed\n";

// Test Update (let's change the first name)
$newFirstName = 'Janet';
$updateResult = $person->setFirstName($newFirstName);
echo $updateResult ? "Update Success\n" : "Update Failed\n";

// Test Delete (assuming userID = 1 exists in DB)
//$deleteResult = $person->deletePerson();
//echo $deleteResult ? "Delete Success\n" : "Delete Failed\n";

// Test Logout using the dummy login
$logoutResult = $person->logout();
echo $logoutResult ? "Logout Success\n" : "Logout Failed\n";

// Close connection
close_connection();


?>