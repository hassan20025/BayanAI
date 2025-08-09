<?php
class User implements JsonSerializable {
    private $id;
    private $email;
    private $password;
    private $username;
    private $department;
    private $can_upload;

    function __construct($id, $email, $password, $username, $department = null, $can_upload = false) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->department = $department;
        $this->can_upload = $can_upload;
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

    public function getDepartment() {
        return $this->department;
    }

    public function setDepartment($department) {
        $this->department = $department;
    }

    public function get_can_upload() {
        return $this->can_upload;
    }

    public function set_can_upload($can_upload) {
        $this->can_upload = $can_upload;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'department' => $this->department,
            'canUpload' => $this->can_upload,
        ];
    }
}
