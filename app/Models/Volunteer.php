<?php
require_once 'User.php';
require_once 'Address.php';
require_once 'Badges.php';


class Volunteer extends User
{
    private Address $address;
    public string $nationalID;
    public Badges $badge;

    // Constructor that calls the parent constructor
    public function __construct(
        int $userTypeID, 
        string $firstName, 
        string $lastName, 
        string $email, 
        string $phoneNo, 
        iLogin $login, 
        Address $address, 
        string $nationalID,
        Badges $badge
    ) {
        
        // Call the parent constructor to initialize the User (and Person) properties
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login );
        // Initialize the Volunteer-specific properties
        $this->address = $address;
        $this->nationalID = $nationalID;
        $this->badge = $badge;
        $this->insertVolunteer($address, $nationalID);
        $this->chooseRole();
        
    }

    public function insertVolunteer(Address $address, string $nationalID): bool
    {
        //$conn = Database::getInstance()->getConnection();
        
        $address = $this->address->getID();
        $address = mysqli_real_escape_string(Database::getInstance()->getConnection(), $address);
        $nationalID = mysqli_real_escape_string(Database::getInstance()->getConnection(), $nationalID);
        $userid = $this->getUserID();
        $defaultBadge = $this->badge->getBadgeID();
        // SQL query to insert the person into the database
        $query = "INSERT INTO volunteer (userID, nationalID, `address`, badge) 
                VALUES ('{$userid}', '{$nationalID}', '{$address}', '{$defaultBadge}')";

        // Run the query and return whether it was successful
        return run_query($query);
    }

    public function updateVolunteer(array $fieldsToUpdate): bool
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
        $query = "UPDATE volunteer SET $setQueryStr WHERE userID = '{$this->getUserID()}'";
    
        // Run the query and return the result
        return run_query($query);
    }

    public function deleteVolunteer(): bool
    {
        $query = "DELETE FROM volunteer WHERE userID = '{$this->getUserID()}'";
        return run_query($query);
    }

    public function getAddress(): int
    {
        return $this->address->getID();
    }

    public function getNationalID(): string
    {
        return $this->nationalID;
    }

    public function getBadge(): int
    {
        return $this->badge->getBadgeID();
    }

    public function setBadge(Badges $badge): bool
    {
        // Optional validation (e.g., ensure it's a valid Badge object)
        if (!$badge instanceof Badges) {
            return false;  // Not a valid badge
        }
    
        $this->badge = $badge;


        $fieldsToUpdate = [
            'badge' => $this->badge->getBadgeID()
        ];

        return $this->updateVolunteer($fieldsToUpdate);
    }

    public function setNationalID(string $nationalID): bool
    {
        $this->nationalID = $nationalID;

        $fieldsToUpdate = [
            'nationalID' => $this->nationalID
        ];

        return $this->updateVolunteer($fieldsToUpdate);
    }

    public function setAddress(Address $address): bool
    {
        $this->address = $address;

        $fieldsToUpdate = [
            'address' => $this->address->getID()
        ];

        return $this->updateVolunteer($fieldsToUpdate);
    }
    public function chooseRole(): bool{
        $this->setUserTypeID(0);
        return true;
   }
    
    
}



abstract class VolunteerRoles extends User
{
    protected User $ref;  // Decorated User object





    // Constructor that also initializes the parent User class
    public function __construct(User $ref)
    {
        parent::__construct(
            $ref->getUserTypeID(), 
            $ref->getFirstName(), 
            $ref->getLastName(), 
            $ref->getEmail(), 
            $ref->getPhoneNo(), 
            $ref->getLogin()
        );
        $this->ref = $ref;
    }
    public function hasRole(int $roleFlag, ): bool {

        $myuserTypeID = $this->ref->getUserTypeID();
        echo 'this is the role flag'.$roleFlag . '</br>';
        echo 'this is the user type id'.$myuserTypeID . '</br>';
        return ($myuserTypeID & $roleFlag) == $roleFlag;
    }
    
    public function getAllRoles(): array {
        $roles = [];

    
        // Check each role flag in userTypeID
        if ($this->hasRole(self::COOK_FLAG)) {
            $roles[] = 'Cook';
        }
        if ($this->hasRole(self::DELIVERY_FLAG)) {
            $roles[] = 'DeliveryGuy';
        }
        if ($this->hasRole(self::COORDINATOR_FLAG)) {
            $roles[] = 'Coordinator';
        }
        if ($this->hasRole(self::REPORTER_FLAG)) {
            $roles[] = 'Reporter';
        }
        if ($this->hasRole(self::DONOR_FLAG)) {
            $roles[] = 'Donor';
        }
    
        return $roles;
    }

    public function chooseRole(): bool{
         $this->setUserTypeID(0);
         return true;
    }
    
    

 
}