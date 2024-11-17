<?php

require_once 'Person.php';

 class User extends Person
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

    

    public function chooseRole(): bool {
        $this->setUserTypeID(0);
        return true;
    }


}