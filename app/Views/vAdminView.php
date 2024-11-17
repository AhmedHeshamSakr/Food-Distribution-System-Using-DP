<?php

function renderAdminPanel($reports, $actionResult = null) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Verification Admin Panel</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            table, th, td {
                border: 1px solid #ddd;
            }
            th, td {
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f4f4f4;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            button, select {
                padding: 5px 10px;
                margin: 5px 0;
            }
        </style>
    </head>
    <body>";

    echo "<h1>Verification Admin Panel</h1>";

    if ($actionResult) {
        echo "<p style='color: green;'><strong>$actionResult</strong></p>";
    }

    echo "<h2>All Active Reports</h2>";
    if (count($reports) > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr>
                <th>Person in Need Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>";

        foreach ($reports as $report) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($report['personINname']) . "</td>";
            echo "<td>" . htmlspecialchars($report['personINaddress']) . "</td>";
            echo "<td>" . htmlspecialchars($report['phoneINno']) . "</td>";
            echo "<td>" . htmlspecialchars($report['description']) . "</td>";
            echo "<td>" . htmlspecialchars($report['status']) . "</td>";
            echo "<td>
                    <form method='post' style='display:inline-block;'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($report['reportID']) . "'>
                        <input type='hidden' name='action' value='update'>
                        <select name='status'>
                            <option value='Pending'>Pending</option>
                            <option value='Acknowledged'>Acknowledged</option>
                            <option value='In Progress'>In Progress</option>
                            <option value='Completed'>Completed</option>
                        </select>
                        <button type='submit'>Update</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No active reports found.</p>";
    }
}

?>
