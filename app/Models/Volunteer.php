<?php
require_once 'User.php';
require_once 'Address.php';
require_once 'Badges.php';


#################################### THESE ARE JUST  DUMMY CLASSES FOR TESTING #############################################



class Badge
{
    private int $badgeID;
    private string $badgeName;

    public function __construct(int $badgeID, string $badgeName)
    {
        $this->badgeID = $badgeID;
        $this->badgeName = $badgeName;
    }

    public function getBadgeID(): int
    {
        return $this->badgeID;
    }

    public function getBadgeName(): string
    {
        return $this->badgeName;
    }
}



class Address
{
    // Properties corresponding to the table columns
    private int $id;
    private string $name;
    private ?int $parent_id;
    private string $level;

    // Constructor to initialize the Address object
    public function __construct(int $id, string $name, ?int $parent_id, string $level)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parent_id = $parent_id;
        $this->level = $level;
    }

    // Function to get the address ID
    public function getAddressID(): int
    {
        return $this->id;
    }
}




#################################################################################

class Volunteer extends User
{
    private Address $address;
    public string $nationalID;
    public Badge $badge;

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
        Badge $badge
    ) {
        // Call the parent constructor to initialize the User (and Person) properties
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login, );

        // Initialize the Volunteer-specific properties
        $this->address = $address;
        $this->nationalID = $nationalID;
        $this->badge = $badge;
        $this->insertVolunteer($address, $nationalID);
        
    }

    public function insertVolunteer(Address $address, string $nationalID): bool
    {
        //$conn = Database::getInstance()->getConnection();
        
        $address = $this->address->getAddressID();
        $address = mysqli_real_escape_string(Database::getInstance()->getConnection(), $address);
        $nationalID = mysqli_real_escape_string(Database::getInstance()->getConnection(), $nationalID);
        $userid = $this->getUserID();
        $defaultBadge = 0;
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
        return $this->address->getAddressID();
    }

    public function getNationalID(): string
    {
        return $this->nationalID;
    }

    public function getBadge(): int
    {
        return $this->badge->getBadgeID();
    }

    public function setBadge(Badge $badge): bool
    {
        // Optional validation (e.g., ensure it's a valid Badge object)
        if (!$badge instanceof Badge) {
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
            'address' => $this->address->getAddressID()
        ];

        return $this->updateVolunteer($fieldsToUpdate);
    }
    



    
}

 abstract class VolunteerRoles extends User
 {


 }
?>