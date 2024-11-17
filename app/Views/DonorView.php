<?php
// Assuming the Donor class exists and has the required methods
require_once '/../Models/Donor.php';
// require_once 'DonorView.php';

class DonorView
{
    private $donor;

    public function __construct(Donor $donor)
    {
        $this->donor = $donor;
    }

    /**
     * Render the donor's donation history in a tabular format.
     */
    
    public function renderDonationHistory(): void
    {
        $donationHistory = $this->donor->fetchDonationHistory();

        if (empty($donationHistory)) {
            echo "<p>No donation history available.</p>";
            return;
        }

        echo "<h2>Donation History</h2>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr>
                <th>Donation ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Payment Method</th>
              </tr>";

        foreach ($donationHistory as $donation) {
            echo "<tr>
                    <td>{$donation['donationID']}</td>
                    <td>{$donation['donationDate']}</td>
                    <td>\${$donation['donationAmount']}</td>
                    <td>{$donation['paymentMethod']}</td>
                  </tr>";
        }

        echo "</table>";
    }

    /**
     * Render a form for adding a donation.
     */
    public function renderAddDonationForm(): void
    {
        echo <<<HTML
        <h2>Add Donation</h2>
        <form method="post" action="addDonation.php">
            <label for="amount">Donation Amount:</label>
            <input type="number" id="amount" name="amount" step="0.01" required>
            <br>
            <label for="paymentMethod">Payment Method:</label>
            <select id="paymentMethod" name="paymentMethod" required>
                <option value="Fawry">Fawry</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Visa">Visa</option>
            </select>
            <br>
            <label for="paymentDetails">Payment Details:</label>
            <textarea id="paymentDetails" name="paymentDetails" placeholder="Enter payment details here..." required></textarea>
            <br>
            <button type="submit">Submit Donation</button>
        </form>
        HTML;
    }

    /**
     * Render a form for updating personal information.
     */
    public function renderUpdatePersonalInfoForm(): void
    {
        $firstName = $this->donor->getFirstName();
        $lastName = $this->donor->getLastName();
        $email = $this->donor->getEmail();
        $phoneNo = $this->donor->getPhoneNo();

        echo <<<HTML
        <h2>Update Personal Information</h2>
        <form method="post" action="updatePersonalInfo.php">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="$firstName" required>
            <br>
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="$lastName" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="$email" required>
            <br>
            <label for="phoneNo">Phone Number:</label>
            <input type="text" id="phoneNo" name="phoneNo" value="$phoneNo" required>
            <br>
            <button type="submit">Update Info</button>
        </form>
        HTML;
    }

    /**
     * Render a form for updating an existing donation.
     */
    public function renderUpdateDonationForm(): void
    {
        echo <<<HTML
        <h2>Update Donation</h2>
        <form method="post" action="updateDonation.php">
            <label for="donationID">Donation ID:</label>
            <input type="number" id="donationID" name="donationID" required>
            <br>
            <label for="amount">Donation Amount:</label>
            <input type="number" id="amount" name="amount" step="0.01" required>
            <br>
            <label for="paymentMethod">Payment Method:</label>
            <select id="paymentMethod" name="paymentMethod" required>
                <option value="Fawry">Fawry</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Visa">Visa</option>
            </select>
            <br>
            <button type="submit">Update Donation</button>
        </form>
        HTML;
    }
}


// Create an instance of Donor with some mock data
$donor = new Donor(1); // Assuming the constructor takes a donor ID or similar identifier

// Create an instance of DonorView
$donorView = new DonorView($donor);

// Render the donor's donation history
$donorView->renderDonationHistory();

// Render the form for adding a donation
$donorView->renderAddDonationForm();

// Render the form for updating personal information
$donorView->renderUpdatePersonalInfoForm();

// Render the form for updating an existing donation
$donorView->renderUpdateDonationForm();
?>