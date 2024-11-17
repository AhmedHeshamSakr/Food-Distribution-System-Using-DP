<?php

require_once 'Person.php';

class User extends Person
{
    protected const USER_TYPE_ID_MAP = [
        'volunteer' => 0,
        'donor' => 5,
        'reporter' => 6,
        '' => 7, // Placeholder for dynamic role assignment
    ];

    // Constructor that calls the parent constructor
    public function __construct(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, iLogin $login)
    {
        // Call the parent constructor to initialize the Person class
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }

    public function chooseRole(): bool
    {
        return $this->setUserTypeID(self::USER_TYPE_ID_MAP['']);  // This can be dynamically changed by decorators
    }
}