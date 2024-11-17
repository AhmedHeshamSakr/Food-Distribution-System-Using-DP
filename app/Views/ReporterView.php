<?php

class ReporterView
{
    /**
     * Render the page header and required assets (Bootstrap, etc.).
     */
    public function renderPageHeader()
    {
        echo '<!DOCTYPE html>';
        echo '<html lang="en">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>Reporter Dashboard</title>';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">';
        echo '</head>';
        echo '<body>';
    }

    /**
     * Render the page footer with JS files.
     */
    public function renderPageFooter()
    {
        echo '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>';
        echo '</body>';
        echo '</html>';
    }

    /**
     * Render the report history of the reporter.
     */
    public function renderReportHistory(array $reportHistory)
    {
        echo '<div class="container mt-5">';
        echo '<h2>Your Report History</h2>';

        if (empty($reportHistory)) {
            echo '<p class="alert alert-info">No reports submitted yet.</p>';
        } else {
            echo '<table class="table table-bordered">';
            echo '<thead class="thead-light">';
            echo '<tr>';
            echo '<th scope="col">Report ID</th>';
            echo '<th scope="col">Person In Need Name</th>';
            echo '<th scope="col">PIN Address</th>';
            echo '<th scope="col">PIN phone number</th>';
            echo '<th scope="col">Report Description</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($reportHistory as $report) {


                echo '<tr>';
                echo '<td>' . htmlspecialchars($report['reportID']) . '</td>';
                echo '<td>' . htmlspecialchars($report['personINname']) . '</td>';
                echo '<td>' . htmlspecialchars($report['personINaddress']) . '</td>';
                echo '<td>' . htmlspecialchars($report['phoneINno']) . '</td>';
                echo '<td>' . htmlspecialchars($report['description']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }

        echo '</div>';
    }

    /**
     * Render the report submission form.
     */
    public function renderReportForm()
    {
        echo '<div class="container mt-5">';
        echo '<h2>Submit a Report</h2>';
        echo '<form method="POST" action="" class="form-group">';

        // Person Name field
        echo '<div class="mb-3">';
        echo '<label for="personInName" class="form-label">Name</label>';
        echo '<input type="text" name="personInName" class="form-control" required>';
        echo '</div>';

        // Person Address field
        echo '<div class="mb-3">';
        echo '<label for="personInAddress" class="form-label">Address</label>';
        echo '<input type="text" name="personInAddress" class="form-control" required>';
        echo '</div>';

        // Person Phone Number field
        echo '<div class="mb-3">';
        echo '<label for="personInPhone" class="form-label">Phone Number</label>';
        echo '<input type="text" name="personInPhone" class="form-control" required>';
        echo '</div>';

        // Description field
        echo '<div class="mb-3">';
        echo '<label for="description" class="form-label">Description</label>';
        echo '<textarea name="description" class="form-control" rows="5" required></textarea>';
        echo '</div>';

        echo '<input type="hidden" name="action" value="submit_report">';
        echo '<button type="submit" class="btn btn-primary">Submit Report</button>';
        echo '</form>';
        echo '</div>';
    }


    /**
     * Render the form to update personal information.
     */
    public function renderPersonalInfoForm()
    {
        echo '<div class="container mt-5">';
        echo '<h2>Update Your Information</h2>';

        // Button to toggle the form
        echo '<button class="btn btn-secondary" id="toggleUpdateForm" onclick="toggleForm()">Edit Info</button>';

        // Hidden form to update personal information
        echo '<form method="POST" action="" id="updateForm" class="form-group mt-3" style="display: none;">';

        // First Name field
        echo '<div class="mb-3">';
        echo '<label for="firstName" class="form-label">First Name</label>';
        echo '<input type="text" name="firstName" class="form-control" required>';
        echo '</div>';

        // Last Name field
        echo '<div class="mb-3">';
        echo '<label for="lastName" class="form-label">Last Name</label>';
        echo '<input type="text" name="lastName" class="form-control" required>';
        echo '</div>';

        // Email field
        // echo '<div class="mb-3">';
        // echo '<label for="email" class="form-label">Email</label>';
        // echo '<input type="email" name="email" class="form-control" required>';
        // echo '</div>';

        // Phone Number field
        echo '<div class="mb-3">';
        echo '<label for="phoneNo" class="form-label">Phone Number</label>';
        echo '<input type="text" name="phoneNo" class="form-control" required>';
        echo '</div>';

        echo '<input type="hidden" name="action" value="update_info">';
        echo '<button type="submit" class="btn btn-primary">Save Changes</button>';
        echo '</form>';
        echo '</div>';

        // Add JavaScript to handle form toggling
        echo '<script>
            function toggleForm() {
                const form = document.getElementById("updateForm");
                if (form.style.display === "none") {
                    form.style.display = "block";
                } else {
                    form.style.display = "none";
                }
            }
        </script>';
    }
}

