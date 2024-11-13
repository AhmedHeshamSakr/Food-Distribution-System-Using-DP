<?php

require_once __DIR__ . "/../../config/DB.php"; 

class Badges
{
    private int $badgeID;
    private string $badgeLvl;
    private mysqli $connection;

    public function __construct(int $badgeID = 0, string $badgeLvl = '')
    {
        $this->badgeID = $badgeID;
        $this->badgeLvl = $badgeLvl;
        $this->connection = Database::getInstance()->getConnection(); // Get DB connection
    }

    // Getters
    public function getBadgeID(): int
    {
        return $this->badgeID;
    }

    public function getBadgeLvl(): string
    {
        return $this->badgeLvl;
    }

    // Setters
    public function setBadgeLvl(string $badgeLvl): void
    {
        $this->badgeLvl = $badgeLvl;
    }

    // CRUD Operations

    // CREATE: Insert a new badge
    public function insertBadge(): bool
    {
        $query = "INSERT INTO Badge (badgeLvl) VALUES (?)";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Error preparing query: " . $this->connection->error);
        }

        $stmt->bind_param('s', $this->badgeLvl);
        return $stmt->execute();
    }

    // READ: Get a badge by its ID
    public function getBadgeByID(int $badgeID): ?array
    {
        $query = "SELECT * FROM Badge WHERE badgeID = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Error preparing query: " . $this->connection->error);
        }

        $stmt->bind_param('i', $badgeID);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc(); // Return associative array of badge data
        }

        return null; // No badge found
    }

    // READ: Get all badges
    public function getAllBadges(): array
    {
        $query = "SELECT * FROM Badge";
        $result = $this->connection->query($query);

        if ($result && $result->num_rows > 0) {
            $badges = [];
            while ($row = $result->fetch_assoc()) {
                $badges[] = $row;
            }
            return $badges; // Return array of badges
        }

        return []; // No badges found
    }

    // UPDATE: Update an existing badge
    public function updateBadge(): bool
    {
        if ($this->badgeID === 0) {
            throw new Exception("Badge ID must be set before updating.");
        }

        $query = "UPDATE Badge SET badgeLvl = ? WHERE badgeID = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Error preparing query: " . $this->connection->error);
        }

        $stmt->bind_param('si', $this->badgeLvl, $this->badgeID);
        return $stmt->execute();
    }

    // DELETE: Delete a badge by its ID
    public function deleteBadge(): bool
    {
        if ($this->badgeID === 0) {
            throw new Exception("Badge ID must be set before deleting.");
        }

        $query = "DELETE FROM Badge WHERE badgeID = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Error preparing query: " . $this->connection->error);
        }

        $stmt->bind_param('i', $this->badgeID);
        return $stmt->execute();
    }
}

?>
