<?php

require_once __DIR__ . "/../../config/DB.php";

class Address
{
    private int $id = 0; // Default to 0 to handle uninitialized state
    private string $name; // Name of the address (e.g., 'Egypt', 'Cairo')
    private ?int $parent_id; // Parent ID for hierarchical addresses (e.g., 'Cairo' under 'Egypt')
    private string $level; // Level of the address ('Country', 'State', 'City', 'Neighborhood')

    // Constructor to initialize properties
    public function __construct(string $name, ?int $parent_id, string $level)
    {
        // Validate the level
        $validLevels = ['Country', 'State', 'City', 'Neighborhood'];
        if (!in_array($level, $validLevels)) {
            throw new Exception("Invalid level: {$level}. Must be one of 'Country', 'State', 'City', 'Neighborhood'.");
        }

        $this->name = $name;
        $this->parent_id = $parent_id;
        $this->level = $level;

        // Check for duplicates and create if not exists
        $duplicateId = $this->findDuplicate();
        if ($duplicateId !== null) {
            $this->id = $duplicateId;
        } else {
            $this->create();
        }
    }

    // Create a new address record in the database
    public function create(): bool
    {
        // Check for duplicates
        $duplicateId = $this->findDuplicate();
        if ($duplicateId !== null) {
            $this->id = $duplicateId;
            return false; // Address already exists, no need to create
        }

        // Handle null values for parent_id
        $parentIdValue = is_null($this->parent_id) ? "NULL" : (int)$this->parent_id;

        // Prepare the SQL statement
        $sql = "INSERT INTO address (name, parent_id, level) VALUES ('{$this->name}', {$parentIdValue}, '{$this->level}')";

        // Execute the query
        $result = run_query($sql);

        if ($result) {
            // Fetch and set the last inserted ID
            $this->id = $this->getLastInsertedID();
            return true;
        }

        return false;
    }

    private function getLastInsertedID(): int
    {
        // Return the ID of the last inserted record in the address table
        $sql = "SELECT LAST_INSERT_ID() AS id";
        $result = run_select_query($sql);

        return $result ? (int)$result[0]['id'] : 0;
    }

    public function getId(): int
    {
        // Ensure that the ID is set, if it's not initialized yet, return 0
        if ($this->id === 0) {
            throw new Exception("Address ID is not initialized. Ensure the address has been created.");
        }
        return $this->id;
    }

    // Read an address record from the database
    public static function read(?int $id): ?Address
    {
        $query = "SELECT * FROM address WHERE id = {$id}";
        $result = run_select_query($query);

        if ($result && count($result) > 0) {
            $data = $result[0];
            return new Address($data['name'], $data['parent_id'], $data['level']);
        }

        return null;
    }

    // In Address class: Add this method to get the ID by name.
    public static function getIdByName(string $name): ?int
    {
        $sql = "SELECT id FROM address WHERE name = '{$name}'";
        $result = run_select_query($sql);

        if ($result && count($result) > 0) {
            return (int)$result[0]['id'];
        }

        return null; // Return null if not found
    }

    // Find duplicate address
    private function findDuplicate(): ?int
    {
        $parentIdValue = is_null($this->parent_id) ? "IS NULL" : "= {$this->parent_id}";
        $sql = "SELECT id FROM address WHERE name = '{$this->name}' AND parent_id {$parentIdValue} AND level = '{$this->level}' LIMIT 1";
        $result = run_select_query($sql);

        if ($result && count($result) > 0) {
            return (int)$result[0]['id'];
        }

        return null;
    }

    public static function getCountries(): array
{
    $sql = "SELECT id, name FROM address WHERE level = 'Country'";
    $result = run_select_query($sql);

    if ($result && count($result) > 0) {
        return $result;
    }

    return []; 
}

public static function getCities(): array
{
    $sql = "SELECT id, name FROM address WHERE level = 'City'";
    $result = run_select_query($sql);

    if ($result && count($result) > 0) {
        return $result;
    }

    return []; 
}

public static function getCitiesByCountry(int $countryId): array
{
    $sql = "SELECT id, name FROM address WHERE level = 'City' AND parent_id = {$countryId}";
    $result = run_select_query($sql);

    if ($result && count($result) > 0) {
        return $result;
    }

    return []; 
}

    // Getters and Setters
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function setParentId(?int $parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    // Delete an address (this can be enhanced if needed)
    public function delete(): bool
    {
        $sql = "DELETE FROM address WHERE id = {$this->id}";
        return run_query($sql);
    }
}
