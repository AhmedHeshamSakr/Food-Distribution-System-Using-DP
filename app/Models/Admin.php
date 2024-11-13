<?php

require_once __DIR__ . "/../../config/DB.php";
require_once __DIR__ . '/Person.php';
require_once __DIR__ . '/Badges.php';

// class Admin extends Person
// {
//     public function __construct(
//         int $userTypeID,
//         string $firstName,
//         string $lastName,
//         string $email,
//         string $phoneNo,
//         iLogin $login,
//     ) {
//         parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
//     }

// }

class BadgeAdmin extends Person
{
    public function __construct(
        int $userTypeID,
        string $firstName,
        string $lastName,
        string $email,
        string $phoneNo,
        iLogin $login,
    ) {
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }
    public function assignBadge(Person $person, Badges $badge): bool
    {
        // Example logic to assign a badge to a person
        $query = "INSERT INTO badge (userID, badgeID, expiryDate) 
                  VALUES ('{$person->getUserID()}', '{$badge->getBadgeID()}', '{$badge->getExpiryDate()}')";

        return run_query($query);
    }

    public function revokeBadge(Person $person, int $badgeID): bool
    {
        // Example logic to revoke a badge from a person
        $query = "DELETE FROM badge WHERE userID = '{$person->getUserID()}' AND badgeID = '{$badgeID}'";

        return run_query($query);
    }
}


class EventAdmin extends Person
{
    public function __construct(
        int $userTypeID,
        string $firstName,
        string $lastName,
        string $email,
        string $phoneNo,
        iLogin $login,
    ) {
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }

    // public function createEvent(string $eventDetails): bool
    // {
        
    // }
}

class VerificationAdmin extends Person
{
    
}
?>