<?php
class User {
    private $id;
    private $email;
    private $password;
    private $username;

    function __construct($id, $email, $password, $username) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }
}
