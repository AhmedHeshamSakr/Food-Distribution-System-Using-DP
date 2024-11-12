<?php

interface iLogin {
    public function login($credentials): bool;
    public function authenticate(string $username, string $password): bool;
    public function logout(): bool;
}

class withGoogle implements iLogin {
    private $email;
    private $password;
    private $isAuthenticated = false;

    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function login($credentials): bool {
        // Implement Google authentication logic here
        $this->isAuthenticated = $this->authenticate($credentials['email'], $credentials['password']);
        return $this->isAuthenticated;
    }

    public function authenticate(string $username, string $password): bool {
        // Placeholder for Google-specific authentication
        return true; // Assume success for this example
    }

    public function logout(): bool {
        $this->isAuthenticated = false;
        return !$this->isAuthenticated;
    }
}

class withFacebook implements iLogin {
    private $email;
    private $password;
    private $isAuthenticated = false;

    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function login($credentials): bool {
        // Implement Facebook authentication logic here
        $this->isAuthenticated = $this->authenticate($credentials['email'], $credentials['password']);
        return $this->isAuthenticated;
    }

    public function authenticate(string $username, string $password): bool {
        // Placeholder for Facebook-specific authentication
        return true; // Assume success for this example
    }

    public function logout(): bool {
        $this->isAuthenticated = false;
        return !$this->isAuthenticated;
    }
}

class withEmail implements iLogin {
    private $email;
    private $password;
    private $isAuthenticated = false;

    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function login($credentials): bool {
        // Implement email-based authentication logic here
        $this->isAuthenticated = $this->authenticate($credentials['email'], $credentials['password']);
        return $this->isAuthenticated;
    }

    public function authenticate(string $username, string $password): bool {
        // Placeholder for email-specific authentication
        return true; // Assume success for this example
    }

    public function logout(): bool {
        $this->isAuthenticated = false;
        return !$this->isAuthenticated;
    }
}
