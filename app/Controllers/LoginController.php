<?php

require_once __DIR__ . '/../Models/Login.php';
require_once __DIR__ . '/../Views/LoginView.php';

class LoginController
{
    private $loginHandler;
    private $view;
    private $mode; // Tracks whether the view is for login or register

    public function __construct()
    {
        // Initialize the login handler with empty credentials
        $this->loginHandler = new withEmail('', '');
        // Initialize the view
        $this->view = new EmailLoginView($this->loginHandler);
        $this->mode = 'login'; // Default to login view
    }

    /**
     * Main handler for the login flow.
     */
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processFormSubmission();
        }

        $this->renderPage();
    }

    /**
     * Process form submissions (login, register, logout).
     */
    private function processFormSubmission()
    {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'login':
                $this->handleLogin();
                break;

            case 'register':
                $this->handleRegistration();
                break;

            case 'logout':
                $this->handleLogout();
                break;

            case 'show_register':
                $this->mode = 'register';
                break;

            case 'show_login':
                $this->mode = 'login';
                break;

            default:
                echo "<p>Invalid action.</p>";
        }
    }

    /**
     * Handle the login action.
     */
    private function handleLogin()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->loginHandler->login(['email' => $email, 'password' => $password])) {
            header("Location:../app/Views/HomePageView.php"); // Change 'dashboard.php' to your target page
            exit();
        } else {
            echo "<p>Invalid email or password. Please try again.</p>";
        }
    }

    /**
     * Handle the registration action.
     */
    private function handleRegistration()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->loginHandler->register($email, $password)) {
            echo "<p>Registration successful! You can now log in.</p>";
            $this->mode = 'login'; // Redirect to login after registration
        } else {
            echo "<p>Registration failed. The email might already be in use.</p>";
        }
    }

    /**
     * Handle the logout action.
     */
    private function handleLogout()
    {
        if ($this->loginHandler->logout()) {
            echo "<p>You have been logged out successfully.</p>";
        }
    }

    /**
     * Render the appropriate page based on the user's authentication status and mode.
     */
    private function renderPage()
    {
        if ($this->loginHandler->isAuthenticated()) {
            $this->view->renderStatus();
            $this->view->renderLogoutButton();
        } else {
            // Render the login or registration form based on the current mode
            $this->view->renderForm($this->mode);
        }
    }
}