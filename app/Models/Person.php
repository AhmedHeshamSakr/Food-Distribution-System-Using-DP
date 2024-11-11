<?php
//////////////////////////////////// DUMMY LOGIN




// interface iLogin
// {
//     public function authenticate(string $username, string $password): bool;
//     public function logout(): bool;
    
// }

// class DummyLogin implements iLogin
// {
//     public $isAuthenticated = false;

//     public function authenticate(string $username, string $password): bool
//     {
//         // Simulate successful authentication
//         $this->isAuthenticated = true;
//         return $this->isAuthenticated;
//     }

//     public function logout(): bool
//     {
//         $this->isAuthenticated = false;
//         return !$this->isAuthenticated;
//     }
// }




///////////////////////////////////

require_once __DIR__ . "/../../config/DB.php";

abstract class Person
{
    private int $userTypeID;
    private int $userID;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $phoneNo;
    private iLogin $login;

    public function __construct(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, iLogin $login)
    {
        $this->userTypeID = $userTypeID;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phoneNo = $phoneNo;
        $this->login = $login;
        $this->insertPerson($userTypeID,$firstName, $lastName, $email, $phoneNo);
    }

    public function insertPerson(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo): bool
    {
        // Sanitize inputs to prevent SQL injection (if not already done)
        $firstName = mysqli_real_escape_string(Database::getInstance()->getConnection(), $firstName);
        $lastName = mysqli_real_escape_string(Database::getInstance()->getConnection(), $lastName);
        $email = mysqli_real_escape_string(Database::getInstance()->getConnection(), $email);
        $phoneNo = mysqli_real_escape_string(Database::getInstance()->getConnection(), $phoneNo);

        // SQL query to insert the person into the database
        $query = "INSERT INTO person (userTypeID, firstName, lastName, email, phoneNo) 
                VALUES ('{$userTypeID}', '{$firstName}', '{$lastName}', '{$email}', '{$phoneNo}')";

        // Run the query and return whether it was successful
        $result = run_query($query);
        
        if ($result) {
            $this->userID = mysqli_insert_id(Database::getInstance()->getConnection());
            return true;
    }
        return false;
    }




    public function updatePerson(array $fieldsToUpdate): bool
    {
        // Create an array to hold the SET part of the SQL query
        $setQuery = [];
        
        // Loop through the fieldsToUpdate array and create the SET portion of the query
        foreach ($fieldsToUpdate as $field => $value) {
            // Escape the value to prevent SQL injection
            $escapedValue = mysqli_real_escape_string(Database::getInstance()->getConnection(), $value);
            $setQuery[] = "$field = '$escapedValue'";
        }

        // Join the setQuery array into a string with commas
        $setQueryStr = implode(', ', $setQuery);

        // Construct the full SQL query
        $query = "UPDATE person SET $setQueryStr WHERE userID = '{$this->userID}'";

        // Run the query and return the result
        return run_query($query);
    }




    public function deletePerson(): bool
    {
        $query = "DELETE FROM person WHERE userID = '{$this->userID}'";
        return run_query($query);
    }




    public function getUserTypeID(): int
    {
        return $this->userTypeID;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }



    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNo(): string
    {
        return $this->phoneNo;
    }
    public function getLogin(): iLogin
    {
        return $this->login;
    }


    public function setUserTypeID(int $userTypeID): bool
    {
        $this->userTypeID = $userTypeID;
        $fieldsToUpdate = [
            'userTypeID' => $this->userTypeID
        ];
        return $this->updatePerson($fieldsToUpdate); 
    }


    public function setFirstName(string $firstName): bool
    {
        $this->firstName = $firstName;
        $fieldsToUpdate = [
                'firstName' => $this->firstName
            ];
        return $this->updatePerson($fieldsToUpdate); 
    }


    public function setLastName(string $lastName): bool
    {
        $this->lastName = $lastName;
        $fieldsToUpdate = [
            'lastName' => $this->lastName
        ];
        return $this->updatePerson($fieldsToUpdate); 
    }

    public function setEmail(string $email): bool
    {
        $this->email = $email;
        $fieldsToUpdate = [
            'email' => $this->email
        ];
        return $this->updatePerson($fieldsToUpdate); 
    }

    public function setPhoneNo(string $phoneNo): bool
    {
        $this->phoneNo = $phoneNo;
        $fieldsToUpdate = [
            'phoneNo' => $this->phoneNo
        ];
       return $this->updatePerson($fieldsToUpdate); 
    }

    public function setLogin(iLogin $login): void
    {
        $this->login = $login;
    }


    public function logout():bool
    {
        $this->login->isAuthenticated = false;
        return !$this->login->isAuthenticated;
    }
}


?>