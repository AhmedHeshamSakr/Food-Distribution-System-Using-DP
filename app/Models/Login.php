<?php

//HANIA : TO BE REVISED AND WELL RESTRUCTUREEDDDD // MADE TO TEST REPORTER

interface iLogin {
    public function login($credentials): bool;
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
        // Set $isAuthenticated based on success
        $this->isAuthenticated = true; // Placeholder for successful login
        return $this->isAuthenticated;
    }

    public function isAuthenticated(): bool {
        return $this->isAuthenticated;
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
        // Set $isAuthenticated based on success
        $this->isAuthenticated = true; // Placeholder for successful login
        return $this->isAuthenticated;
    }

    public function isAuthenticated(): bool {
        return $this->isAuthenticated;
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
        // Set $isAuthenticated based on success
        $this->isAuthenticated = true; // Placeholder for successful login
        return $this->isAuthenticated;
    }

    public function isAuthenticated(): bool {
        return $this->isAuthenticated;
    }
}



?>
