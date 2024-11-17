<?php


class EmailLoginView
{
    private $loginHandler;

    public function __construct(withEmail $loginHandler)
    {
        $this->loginHandler = $loginHandler;
    }

    /**
     * Render the login form.
     */
    public function renderLoginForm(): void
    {
        echo <<<HTML
        <h2>Login</h2>
        <form method="post" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit" name="action" value="login">Login</button>
        </form>
        HTML;
    }

    /**
     * Render the registration form.
     */
    public function renderRegistrationForm(): void
    {
        echo <<<HTML
        <h2>Register</h2>
        <form method="post" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit" name="action" value="register">Register</button>
        </form>
        HTML;
    }

    /**
     * Render the logout button.
     */
    public function renderLogoutButton(): void
    {
        echo <<<HTML
        <form method="post" action="">
            <button type="submit" name="action" value="logout">Logout</button>
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