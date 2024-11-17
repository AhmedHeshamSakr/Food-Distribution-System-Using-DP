<?php
class DonorView
{
    /**
     * Render the donation history of the donor.
     */
    public function renderDonationHistory(array $donationHistory)
    {
        echo '<div class="container mt-5">';
        echo '<h2>Your Donation History</h2>';

        if (empty($donationHistory)) {
            echo '<p class="alert alert-info">No donations made yet.</p>';
        } else {
            echo '<table class="table table-bordered">';
            echo '<thead class="thead-light">';
            echo '<tr>';
            echo '<th scope="col">Donation Date</th>';
            echo '<th scope="col">Amount $</th>';
            echo '<th scope="col">Payment Method</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($donationHistory as $donation) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($donation['donationDate']) . '</td>';
                echo '<td>' . htmlspecialchars($donation['donationAmount']) . '</td>';
                echo '<td>' . htmlspecialchars($donation['paymentMethod']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }

        echo '</div>';
    }

    /**
     * Render the donation form.
     */
    public function renderDonationForm()
    {
        echo '<div class="container mt-5">';
        echo '<h2>Make a Donation</h2>';
        echo '<form method="POST" action="" class="form-group">';
        
        // Amount field
        echo '<div class="mb-3">';
        echo '<label for="amount" class="form-label">Amount</label>';
        echo '<input type="number" name="amount" class="form-control" required>';
        echo '</div>';

        // Payment Method field
        echo '<div class="mb-3">';
        echo '<label for="paymentMethod" class="form-label">Payment Method</label>';
        echo '<select name="paymentMethod" class="form-control" required>';
        echo '<option value="Credit Card">Credit Card</option>';
        echo '<option value="Visa">Visa</option>';
        echo '<option value="Fawry">Fawry</option>';
        echo '</select>';
        echo '</div>';

        // Payment Details field
        echo '<div class="mb-3">';
        echo '<label for="paymentDetails" class="form-label">Payment Details</label>';
        echo '<input type="text" name="paymentDetails" class="form-control" required>';
        echo '</div>';

        echo '<input type="hidden" name="action" value="add_donation">';
        echo '<button type="submit" class="btn btn-primary">Donate</button>';
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
        echo '<div class="mb-3">';
        echo '<label for="email" class="form-label">Email</label>';
        echo '<input type="email" name="email" class="form-control" required>';
        echo '</div>';

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
        echo '<title>Donor Dashboard</title>';
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
}