<?php

require_once 'vendor/autoload.php';


use Kreait\Firebase\Factory;

class FirebaseFacade {
    public $auth;

    public function __construct() {
        try {
            $firebase = (new Factory)->withServiceAccount(__DIR__ . '/../../config/firebase_credentials.json');


            $this->auth = $firebase->createAuth();
        } catch (Exception $e) {
            throw new Exception("Firebase Initialization Error: " . $e->getMessage());
        }
    }

    public function logUserData(string $name, string $email) {
        if(is)
        file_put_contents(__DIR__ . '/../../storage/user-log.txt', "User: $name, Email: $email\n", FILE_);
    }

    public function getAuth() {
        return $this->auth;
    }
}
