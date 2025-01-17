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
        print('nationalID'.$this->nationalID);
        error_log("MY CONSTRUCTOR, " . var_export($nationalID, true));

        $this->badge = $badge;
        $this->volunteerList = new VolunteerList();
        $this->insertVolunteer($this);
        $this->chooseRole();
        
    }

    public static function fetchById(int $userId): ?self
    {
        try {
            // Query to fetch data from person and volunteer tables
            $query = "
                SELECT 
                    p.userID, p.userTypeID, p.firstName, p.lastName, p.email, p.phoneNo, 
                    v.nationalID, v.address, v.badge
                FROM 
                    person p
                JOIN 
                    volunteer v 
                ON 
                    p.userID = v.userID
                WHERE 
                    p.userID = {$userId}
            ";
        
            // Execute the query
            $result = run_select_query($query);
        
            // Check if the result is a valid mysqli_result object
            if ($result && $result instanceof mysqli_result) {
                // Fetch the data
                $row = mysqli_fetch_assoc($result);
                if ($row) {
                    // Construct Address and Badge objects from database data
                    $address = new Address($row['name'], $row['parent_id'], $row['level']); // Modify based on your Address class
                    $badge = new Badges($row['badge']); // Modify based on your Badges class
        
                    // Create a new Volunteer object
                    $volunteer = new self(
                        $row['userTypeID'],
                        $row['firstName'],
                        $row['lastName'],
                        $row['email'],
                        $row['phoneNo'],
                        $address,
                        $row['nationalID'],
                        $badge
                    );
        
                    // Set the userID for the Volunteer object
                    $volunteer->setUserID((int)$row['userID']);
        
                    // Return the Volunteer object
                    return $volunteer;
                } else {
                    error_log("No volunteer found for user ID: {$userId}");
                }
            } else {
                // Log an error if the result is not a valid mysqli_result
                error_log("Error: Expected mysqli_result but received a different type.");
            }
        
            // If no data is found or the result is invalid, return null
            return null;
        } catch (Exception $e) {
            error_log("Error fetching Volunteer by ID: " . $e->getMessage());
            return null;
        }
    }
    
    

    public function insertVolunteer(Volunteer $volunteer){
        $conn = Database::getInstance()->getConnection();
        $nationalID = $volunteer->getNationalID();
        error_log("done, " . var_export($nationalID, true));
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

    public static function updateBadgeByUserID(int $userID, int $badgeID): bool
    {
    // Escape the parameters to prevent SQL injection
    $escapedUserID = mysqli_real_escape_string(Database::getInstance()->getConnection(), $userID);
    $escapedBadgeID = mysqli_real_escape_string(Database::getInstance()->getConnection(), $badgeID);

    // Construct the SQL query to update the badge ID for the given user ID
    $query = "UPDATE volunteer SET badge = '{$escapedBadgeID}' WHERE userID = '{$escapedUserID}'";

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

    public function setBadge(?Badges $badge): bool
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



abstract class VolunteerRoles extends Person
{
    protected Person $ref;  // Decorated User object





    // Constructor that also initializes the parent User class
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