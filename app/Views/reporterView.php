<?php

class ReporterView {
    public function renderForm() {
        echo '
        <h2>Submit a New Report</h2>
        <form method="POST" action="">
            <label for="personInName">Person in Need Name:</label>
            <input type="text" id="personInName" name="personInName" required><br>

            <label for="personInAddress">Person in Need Address:</label>
            <input type="text" id="personInAddress" name="personInAddress" required><br>

            <label for="personInPhone">Person in Need Phone:</label>
            <input type="text" id="personInPhone" name="personInPhone" required><br>

            <label for="description">Description of Need:</label>
            <textarea id="description" name="description" required></textarea><br>

            <button type="submit" name="submitReport">Submit Report</button>
        </form>
        <hr>
        ';
    }

    public function renderActiveReports($reports) {
        echo '<h2>Active Reports</h2>';
        if (empty($reports)) {
            echo '<p>No active reports found.</p>';
        } else {
            echo '<table border="1">
                    <thead>
                        <tr>
                            <th>Report ID</th>
                            <th>Person in Need Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($reports as $report) {
                echo '
                <tr>
                    <td>' . htmlspecialchars($report['reportID']) . '</td>
                    <td>' . htmlspecialchars($report['personINname']) . '</td>
                    <td>' . htmlspecialchars($report['personINaddress']) . '</td>
                    <td>' . htmlspecialchars($report['phoneINno']) . '</td>
                    <td>' . htmlspecialchars($report['description']) . '</td>
                    <td>' . htmlspecialchars($report['status']) . '</td>
                </tr>';
            }
            echo '</tbody></table>';
        }
    }
}
