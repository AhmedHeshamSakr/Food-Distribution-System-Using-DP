
<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load configuration
$configs = require "config.php";

// Create a database connection 
$conn = new mysqli($configs->DB_SERVER, $configs->DB_USERNAME, $configs->DB_PASSWORD, $configs->DB_DATABASE);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully<br/><hr/>";


function run_queries(array $queries, bool $echo = false): array
{
    /**
     * Executes multiple SQL queries and optionally displays their results.
     *
     * @param array $queries An array of SQL query strings to be executed.
     * @param bool $echo If true, outputs the query and its result (success or error).
     * @return array An array of results, each being TRUE on success or FALSE on failure.
     */

    global $conn;
    $results = [];

    foreach ($queries as $query) {
        $result = $conn->query($query);
        $results[] = $result;

        if ($echo) {
            echo '<pre>' . $query . '</pre>';
            echo $result === TRUE ? "Query ran successfully<br/>" : "Error: " . $conn->error;
            echo "<hr/>";
        }
    }

    return $results;
}


function run_query(string $query, bool $echo = false): bool
{
     /**
     * Executes a single SQL query and optionally displays its result.
     *
     * @param string $query A single SQL query string to be executed.
     * @param bool $echo If true, outputs the query and its result (success or error).
     * @return bool TRUE on success, FALSE on failure.
     */
    // Wrapper for single queries using `run_queries` to reduce code duplication
    $result = run_queries([$query], $echo);
    return $result[0] === TRUE;
}

function run_select_query(string $query, bool $echo = false): array|bool
{

    /**
     * Executes a SELECT SQL query, fetches results, and optionally displays the query and data.
     *
     * @param string $query A SQL SELECT query string to be executed.
     * @param bool $echo If true, outputs the query and the fetched data.
     * @return array|bool An array of associative arrays for each row if results are found, or FALSE if no results.
     */
    
    global $conn;
    $result = $conn->query($query);

    if ($echo) {
        echo '<pre>' . $query . '</pre>';
    }

    // Process results if query was successful and returned rows
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



// // Function to insert data with prepared statements
// function insert_data(string $table, array $data): bool
// {
//     global $conn;
//     $columns = implode(", ", array_keys($data));
//     $placeholders = implode(", ", array_fill(0, count($data), '?'));
//     $stmt = $conn->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");

//     if (!$stmt) {
//         echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
//         return false;
//     }

//     // Bind parameters dynamically
//     $types = str_repeat('s', count($data));
//     $stmt->bind_param($types, ...array_values($data));

//     $success = $stmt->execute();
//     $stmt->close();
//     return $success;
// }

// // Function to update data with prepared statements
// function update_data(string $table, array $data, string $conditions): bool
// {
//     global $conn;
//     $setClause = implode("=?, ", array_keys($data)) . "=?";
//     $stmt = $conn->prepare("UPDATE $table SET $setClause WHERE $conditions");

//     if (!$stmt) {
//         echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
//         return false;
//     }

//     $types = str_repeat('s', count($data));
//     $stmt->bind_param($types, ...array_values($data));

//     $success = $stmt->execute();
//     $stmt->close();
//     return $success;
// }

// // Function to delete data with prepared statements
// function delete_data(string $table, string $conditions): bool
// {
//     global $conn;
//     $stmt = $conn->prepare("DELETE FROM $table WHERE $conditions");

//     if (!$stmt) {
//         echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
//         return false;
//     }

//     $success = $stmt->execute();
//     $stmt->close();
//     return $success;
// }

// Close database connection (to be called after all operations are complete)
function close_connection()
{
    global $conn;
    $conn->close();
}
?>
