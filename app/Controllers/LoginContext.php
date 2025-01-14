<?php

require_once __DIR__ . "/../Models/Login.php";


class LoginContext {
    private $strategy;

    public function setStrategy(iLogin $strategy) {
        $this->strategy = $strategy;
    }

    public function login($credentials) {
        return $this->strategy->login($credentials);
    }
}
?>
