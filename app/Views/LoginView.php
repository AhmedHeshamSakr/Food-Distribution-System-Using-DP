<?php
class EmailLoginView
{
    private $loginHandler;

    public function __construct(withEmail $loginHandler)
    {
        $this->loginHandler = $loginHandler;
    }

    /**
     * Render the appropriate form based on the mode (login or register).
     */
    public function renderForm(string $mode): void
    {
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">'; // Bootstrap CDN
        echo '<div class="container mt-5">';

        if ($mode === 'login') {
            $this->renderLoginForm();
        } elseif ($mode === 'register') {
            $this->renderRegistrationForm();
        }

        echo '</div>';
    }

    /**
     * Render the login form with Bootstrap styling.
     */
    private function renderLoginForm(): void
    {
        echo <<<HTML
        <h2>Login</h2>
        <form method="post" action="" class="form-group">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="action" value="login" class="btn btn-primary">Login</button>
        </form>
        <form method="post" action="">
            <button type="submit" name="action" value="show_register" class="btn btn-link">Don't have an account? Register here</button>
        </form>
        HTML;
    }

    /**
     * Render the registration form with Bootstrap styling.
     */
    private function renderRegistrationForm(): void
    {
        echo <<<HTML
        <h2>Register</h2>
        <form method="post" action="" class="form-group">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="firstName" class="form-label">First Name:</label>
                <input type="text" id="firstName" name="firstName" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name:</label>
                <input type="text" id="lastName" name="lastName" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phoneNo" class="form-label">Phone Number:</label>
                <input type="text" id="phoneNo" name="phoneNo" class="form-control" required>
            </div>

            <button type="submit" name="action" value="register" class="btn btn-success">Register</button>
        </form>
        <form method="post" action="">
            <button type="submit" name="action" value="show_login" class="btn btn-link">Already have an account? Login here</button>
        </form>
        HTML;
    }

    /**
     * Render the logout button with Bootstrap styling.
     */
    public function renderLogoutButton(): void
    {
        echo <<<HTML
        <form method="post" action="">
            <button type="submit" name="action" value="logout" class="btn btn-danger">Logout</button>
        </form>
        HTML;
    }

    /**
     * Render the authentication status.
     */
    public function renderStatus(): void
    {
        if ($this->loginHandler->isAuthenticated()) {
            echo "<p>You are logged in as {$this->loginHandler->getEmail()}.</p>";
        } else {
            echo "<p>You are not logged in.</p>";
        }
    }
}