<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../Models/Reporter.php";
require_once __DIR__ . "/../Views/ReporterView.php";

class ReporterController
{
    private ReporterView $view;
    private Reporter $reporter;

    public function __construct(ReporterView $view)
    {
        // session_start(); // Ensure session is started to access the email
        session_start(); // Ensure session is started to access the email

        $email = $_SESSION['email'] ?? null;

        if ($email) {
            // Fetch user data from the database using the email
            $query = "SELECT * FROM person WHERE email = '$email' LIMIT 1";
            $result = mysqli_query(Database::getInstance()->getConnection(), $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $userData = mysqli_fetch_assoc($result);

                // Initialize Reporter with retrieved data
                $this->reporter = new Reporter(
                    $userData['userTypeID'],
                    $userData['firstName'],
                    $userData['lastName'],
                    $userData['email'],
                    $userData['phoneNo']
                );
            } else {
                throw new Exception("User not found in the database.");
            }
        } else {
            throw new Exception("User is not logged in.");
        }

        $this->view = $view;
    }

    /**
     * Handle the request and render the appropriate view.
     */
    public function handleRequest()
    {
        $action = $_POST['action'] ?? null;
        echo "before </br>";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "in post </br>";
            if ($action === 'submit_report') {
                echo "before submit report </br>";
                $this->handleFormSubmission();
                echo "after submit report </br>";
            } elseif ($action === 'update_info') {
                $this->handleUpdatePersonalInfo();
            }

            // Redirect to avoid form resubmission
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }

        $this->renderPage();
    }

    /**
     * Handle submitting a report.
     */
    public function handleFormSubmission()
{
    // Debugging line to check if the request is POST
    echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "<br>";

    // Check if the form is submitted
    if (isset($_POST['action']) && $_POST['action'] === 'submit_report') 
        {
        // Debugging line to verify the action value
        echo "Action: " . $_POST['action'] . "<br>";

        // Retrieve the form data
        $personInName = isset($_POST['personInName']) ? $_POST['personInName'] : '';
        $personInAddress = isset($_POST['personInAddress']) ? $_POST['personInAddress'] : '';
        $personInPhone = isset($_POST['personInPhone']) ? $_POST['personInPhone'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';


        // Call the submitReport method
    
       // $this->reporter->submitReport($personInName, $personInAddress, $personInPhone, $description);
        $result = $this->reporter->submitReport($personInName, $personInAddress, $personInPhone, $description);
    if ($result) {
        echo "Report submitted successfully.\n";
    } else {
        echo "Failed to submit report.\n";
    }

        // Debugging output
        echo "Name: " . htmlspecialchars($personInName) . "<br>";
        echo "Address: " . htmlspecialchars($personInAddress) . "<br>";
        echo "Phone: " . htmlspecialchars($personInPhone) . "<br>";
        echo "Description: " . htmlspecialchars($description) . "<br>";
    } else {
        // If the form is not submitted, echo a message
        echo "Form not submitted or incorrect action value.";
    }
}


    /**
     * Handle updating personal info.
     */
    private function handleUpdatePersonalInfo()
    {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];

        $phoneNo = $_POST['phoneNo'];

    

        $this->reporter->updatePersonalInfo($firstName, $lastName,  $phoneNo);
    }

    /**
     * Render the page with all relevant content.
     */
    public function renderPage()
    {
        $reportHistory = $this->reporter->getReportsByUserID($this->reporter->getUserID());
        // echo "Report History: <br>";
        // foreach ($reportHistory as $report) {
        //     echo "Report ID: " . htmlspecialchars($report['reportID']) . "<br>";
        //     echo "Person In Name: " . htmlspecialchars($report['personINname']) . "<br>";
        //     echo "Person In Address: " . htmlspecialchars($report['personINaddress']) . "<br>";
        //     echo "Person In Phone: " . htmlspecialchars($report['phoneINno']) . "<br>";
        //     echo "Description: " . htmlspecialchars($report['description']) . "<br>";
        //     echo "-------------------------<br>";
        // }

        // Render page header and required assets
        $this->view->renderPageHeader();

        // Render the view content (report history, report form, personal info form)
        $this->view->renderPersonalInfoForm();
        $this->view->renderReportForm();
        $this->view->renderReportHistory($reportHistory);

        // Render page footer with JS files
        $this->view->renderPageFooter();
    }
}
