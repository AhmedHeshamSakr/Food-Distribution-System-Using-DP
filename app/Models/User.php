<?php

require_once 'Person.php';

abstract class User extends Person
{

    protected const USER_TYPE_ID_MAP = [
        'admin' => 1,
        'volunteer' => 2,
        'donor' => 3,
        'reporter' => 4,
    ];

    public function chooseRole(string $role): bool
    {
        return $this->setUserTypeID(self::USER_TYPE_ID_MAP[$role]);
    }
}

?>