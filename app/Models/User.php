<?php

require_once 'Person.php';

abstract class User extends Person
{
    public const COOK_FLAG = 1 << 0;       // Binary 00001
    public const DELIVERY_FLAG = 1 << 1;   // Binary 00010
    public const COORDINATOR_FLAG = 1 << 2; // Binary 00100
    public const REPORTER_FLAG = 1 << 3;   // Binary 01000
    public const DONOR_FLAG = 1 << 4;  // Binary 10000 

    private int $roleType =0;
    // Constructor that calls the parent constructor
    public function __construct(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, iLogin $login)
    {
        // Call the parent constructor to initialize the Person class
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
        $this->chooseRole();
    }

    public function hasRole(int $roleFlag): bool {

        // Check if the specific role is set in userTypeID using bitwise AND operation
        echo $this->getUserTypeID();

        return ($this->getUserTypeID() & $roleFlag) == $roleFlag;
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

    public function chooseRole(): bool {
        $this->setUserTypeID(0);
        return true;
    }


}