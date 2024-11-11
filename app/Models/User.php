<?php

require_once 'Person.php';

abstract class User extends Person
{
    protected const USER_TYPE_ID_MAP = [

        'volunteer' => 1,
        'donor' => 2,
        'reporter' => 3,
    ];

    // Constructor that calls the parent constructor
    public function __construct(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, iLogin $login)
    {
        // Call the parent constructor to initialize the Person class
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }

    public function chooseRole(string $role): bool
    {
        return $this->setUserTypeID(self::USER_TYPE_ID_MAP[$role]);
    }
}
?>
