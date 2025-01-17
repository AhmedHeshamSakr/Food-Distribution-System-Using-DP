<?php
require_once 'User.php';
require_once 'Address.php';
require_once 'Badges.php';
require_once 'Iterator.php';


class Volunteer extends Person
{
    private Address $address;
    public string $nationalID;
    public Badges $badge;

    private VolunteerList $volunteerList;

    // Constructor that calls the parent constructor
    public function __construct(
        int $userTypeID, 
        string $firstName, 
        string $lastName, 
        string $email, 
        string $phoneNo, 
        // iLogin $login, 
        Address $address, 
        string $nationalID,
        Badges $badge,
    ) { 
        // Call the parent constructor to initialize the User (and Person) properties
        parent::__construct( $firstName, $lastName, $email, $phoneNo,$userTypeID );
        // Initialize the Volunteer-specific properties
        $this->address = $address;
        $this->nationalID = $nationalID;
        $this->badge = $badge;
        $this->volunteerList = new VolunteerList();
        $this->insertVolunteer($this);
        $this->chooseRole();
    }

    public function chooseRole(): bool{
        $this->setUserTypeID(0);
        return true;
   }
    public function insertVolunteer(Volunteer $volunteer){
        $conn = Database::getInstance()->getConnection();
        $nationalID = $volunteer->getNationalID();
        print($nationalID);
        $address = $volunteer->getAddress();
        
        $userid = $volunteer->getUserID();
        $defaultBadge = $volunteer->getBadge();
        $this->volunteerList->addVolunteer($volunteer);
        // SQL query to insert the person into the database
        $query = "INSERT INTO volunteer (userID, nationalID, `address`, badge) 
                VALUES ('{$userid}', '{$nationalID}', '{$address}', '{$defaultBadge}')";
        // Run the query and return whether it was successful
        return run_query($query);
    }
    public function updateVolunteer(array $fieldsToUpdate): bool
    {
        $setQuery = [];
        foreach ($fieldsToUpdate as $field => $value) {
            // Escape the value to prevent SQL injection
            $escapedValue = mysqli_real_escape_string(Database::getInstance()->getConnection(), $value);
            $setQuery[] = "$field = '$escapedValue'";
        }
        $setQueryStr = implode(', ', $setQuery);
        $query = "UPDATE volunteer SET $setQueryStr WHERE userID = '{$this->getUserID()}'";
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
 
    // public function insertVolunteer(Address $address, string $nationalID): bool
    // {
    //     //$conn = Database::getInstance()->getConnection();
        
    //     $address = $this->address->getID();
    //     $address = mysqli_real_escape_string(Database::getInstance()->getConnection(), $address);
    //     $nationalID = mysqli_real_escape_string(Database::getInstance()->getConnection(), $nationalID);
    //     $userid = $this->getUserID();
    //     $defaultBadge = $this->badge->getBadgeID();
    //     $this->volunteerList->addVolunteer($this);
    //     // SQL query to insert the person into the database
    //     $query = "INSERT INTO volunteer (userID, nationalID, `address`, badge) 
    //             VALUES ('{$userid}', '{$nationalID}', '{$address}', '{$defaultBadge}')";

    //     // Run the query and return whether it was successful
    //     return run_query($query);
    // }
    
    
}



abstract class VolunteerRoles extends Person
{
    protected Person $ref;  // Decorated User object
    public function __construct(Person $ref)
    {
        parent::__construct( 
            $ref->getFirstName(), 
            $ref->getLastName(), 
            $ref->getEmail(), 
            $ref->getPhoneNo(),
            $ref->getUserTypeID()
            // $ref->getLogin()
        );
        $this->ref = $ref;
    }
    public function hasRole(int $roleFlag, ): bool {

        $myuserTypeID = $this->getUserTypeID();
        //echo 'this is the role flag'.$roleFlag . '</br>';
        //echo 'this is the user type id'.$myuserTypeID . '</br>';
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