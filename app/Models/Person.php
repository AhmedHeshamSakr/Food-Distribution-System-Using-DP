<?php

require_once 'Login.php';
require_once __DIR__ . "/../../config/DB.php";

abstract class Person
{
    private int $userTypeID;
    private int $userID;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $phoneNo;
    private iLogin $login;

    public function __construct(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, iLogin $login)
    {
        $this->userTypeID = $userTypeID;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phoneNo = $phoneNo;
        $this->login = $login;
        $this->insertPerson($userTypeID, $firstName, $lastName, $email, $phoneNo);
    }

    public function insertPerson(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo): bool
    {
        $firstName = mysqli_real_escape_string(Database::getInstance()->getConnection(), $firstName);
        $lastName = mysqli_real_escape_string(Database::getInstance()->getConnection(), $lastName);
        $email = mysqli_real_escape_string(Database::getInstance()->getConnection(), $email);
        $phoneNo = mysqli_real_escape_string(Database::getInstance()->getConnection(), $phoneNo);

        $checkEmailQuery = "SELECT userID FROM person WHERE email = '{$email}' LIMIT 1";
        $checkEmailResult = run_select_query($checkEmailQuery);

        if ($checkEmailResult) {
            $this->userID = $checkEmailResult[0]['userID'];
            return false;
        }

        $query = "INSERT INTO person (userTypeID, firstName, lastName, email, phoneNo) 
                  VALUES ('{$userTypeID}', '{$firstName}', '{$lastName}', '{$email}', '{$phoneNo}')";

        $result = run_query($query);

        if ($result) {
            $this->userID = mysqli_insert_id(Database::getInstance()->getConnection());
            return true;
        }
        return false;
    }

    public function updatePerson(array $fieldsToUpdate): bool
    {
        $setQuery = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $escapedValue = mysqli_real_escape_string(Database::getInstance()->getConnection(), $value);
            $setQuery[] = "$field = '$escapedValue'";
        }
        $setQueryStr = implode(', ', $setQuery);
        $query = "UPDATE person SET $setQueryStr WHERE userID = '{$this->userID}'";
        return run_query($query);
    }

    public function deletePerson(): bool
    {
        $query = "DELETE FROM person WHERE userID = '{$this->userID}'";
        return run_query($query);
    }

    public function login(array $credentials): bool
    {
        // Attempt to log in using the provided iLogin instance
        return $this->login->login($credentials);
    }

    public function logout(): bool
    {
        // Use the iLogin interface's logout method
        return $this->login->logout();
    }

    public function getUserTypeID(): int
    {
        return $this->userTypeID;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNo(): string
    {
        return $this->phoneNo;
    }

    public function getLogin(): iLogin
    {
        return $this->login;
    }

    public function setUserTypeID(int $userTypeID): bool
    {
        $this->userTypeID = $userTypeID;
        return $this->updatePerson(['userTypeID' => $userTypeID]);
    }

    public function setFirstName(string $firstName): bool
    {
        $this->firstName = $firstName;
        return $this->updatePerson(['firstName' => $firstName]);
    }

    public function setLastName(string $lastName): bool
    {
        $this->lastName = $lastName;
        return $this->updatePerson(['lastName' => $lastName]);
    }

    public function setEmail(string $email): bool
    {
        $this->email = $email;
        return $this->updatePerson(['email' => $email]);
    }

    public function setPhoneNo(string $phoneNo): bool
    {
        $this->phoneNo = $phoneNo;
        return $this->updatePerson(['phoneNo' => $phoneNo]);
    }

    public function setLogin(iLogin $login): void
    {
        $this->login = $login;
    }
}