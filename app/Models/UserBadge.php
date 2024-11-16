<?php

require_once __DIR__ . "/../../config/DB.php"; 


class UserBadge
{
    private int $userID;
    private int $badgeID;
    private string $dateAwarded;
    private string $expiryDate;

    // Constructor to initialize the properties
    public function __construct(int $userID, int $badgeID, string $dateAwarded, string $expiryDate)
    {
        $this->userID = $userID;
        $this->badgeID = $badgeID;
        $this->dateAwarded = $dateAwarded;
        $this->expiryDate = $expiryDate;
    }

    // Getters and Setters
    public function getUserID(): int
    {
        return $this->userID;
    }


    public function getBadgeID(): int
    {
        return $this->badgeID;
    }

    

    public function getDateAwarded(): string
    {
        return $this->dateAwarded;
    }

    public function setDateAwarded(string $dateAwarded): void
    {
        $this->dateAwarded = $dateAwarded;
    }

    public function getExpiryDate(): string
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(string $expiryDate): void
    {
        $this->expiryDate = $expiryDate;
    }

    // Create a new UserBadge record in the database
    public function create(): bool
    {
        $query = "INSERT INTO userbadge (userID, badgeID, dateAwarded, expiryDate) 
                  VALUES ('{$this->userID}', '{$this->badgeID}', '{$this->dateAwarded}', '{$this->expiryDate}')";
        return run_query($query); // Assuming run_query is your function for executing SQL queries
    }

    // Read a UserBadge record from the database by userID and badgeID
    public static function read(int $userID, int $badgeID): ?UserBadge
    {
        $query = "SELECT * FROM userbadge WHERE userID = '{$userID}' AND badgeID = '{$badgeID}'";
        $result = run_select_query($query); // Assuming run_select_query fetches the result

        if ($result && count($result) > 0) {
            $data = $result[0];
            return new UserBadge($data['userID'], $data['badgeID'], $data['dateAwarded'], $data['expiryDate']);
        }

        return null;
    }

    // Update an existing UserBadge record
    public function update(): bool
    {
        $query = "UPDATE userbadge
                  SET dateAwarded = '{$this->dateAwarded}', expiryDate = '{$this->expiryDate}' 
                  WHERE userID = '{$this->userID}' AND badgeID = '{$this->badgeID}'";
        return run_query($query);
    }

    // Delete a UserBadge record from the database
    public function delete(): bool
    {
        $query = "DELETE FROM userbadge WHERE userID = '{$this->userID}' AND badgeID = '{$this->badgeID}'";
        return run_query($query);
    }
}
?>
