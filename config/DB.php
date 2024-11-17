<?php

// Singleton Database Class
class Database
{
    private static ?Database $instance = null;
    private mysqli $connection;

    private function __construct()
    {
        $configs = require "config.php";
        $this->connection = new mysqli($configs->DB_SERVER, $configs->DB_USERNAME, $configs->DB_PASSWORD, $configs->DB_DATABASE);
        //!!!!!!!!!!!!!!!1!!!IF YOU ARE NOT JUMANA COMMENT THE LINE BELOW AND USE THE LINE ABOVE!!!!!!!!!!!!!!!!!!!!!!!!
        //$this->connection = new mysqli($configs->DB_SERVER, $configs->DB_USERNAME, $configs->DB_PASSWORD, $configs->DB_DATABASE, $configs->DB_PORT); 
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    public function closeConnection()
    {
        if (isset($this->connection)) {
            $this->connection->close();
        }
        self::$instance = null;
    }
}

// Executes multiple SQL queries and optionally displays their results
function run_queries(array $queries, bool $echo = false): array
{
    $conn = Database::getInstance()->getConnection();
    $results = [];

    foreach ($queries as $query) {
        $results[] = execute_query($conn, $query, $echo);
    }

    return $results;
}

// Executes a single SQL query and returns TRUE on success or FALSE on failure
function run_query(string $query, bool $echo = false): bool
{
    $results = run_queries([$query], $echo);
    return $results[0] === true;
}

// Executes a SELECT SQL query and fetches results
function run_select_query(string $query, bool $echo = false): array|bool
{
    $conn = Database::getInstance()->getConnection();
    $result = execute_select_query($conn, $query, $echo);

    return $result !== false ? $result : false;
}

// Helper function to execute a query and return TRUE on success or FALSE on failure
function execute_query(mysqli $conn, string $query, bool $echo = false): bool
{
    $result = $conn->query($query);

    if ($echo) {
        echo '<pre>' . $query . '</pre>';
        echo $result === true ? "Query ran successfully<br/>" : "Error: " . $conn->error;
        echo "<hr/>";
    }

    return $result === true;
}

// Helper function to execute a SELECT query and return fetched results or FALSE if none
function execute_select_query(mysqli $conn, string $query, bool $echo = false): array|bool
{
    $result = $conn->query($query);

    if ($echo) {
        echo '<pre>' . $query . '</pre>';
    }

    if ($result && $result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        if ($echo) {
            print_r($data);
        }
        echo "<hr/>";
        return $data;
    } elseif ($echo) {
        echo "0 results<br/><hr/>";
    }

    return false;
}

// Close database connection (to be called after all operations are complete)
function close_connection()
{
    Database::getInstance()->closeConnection();
}
