<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST['email'], $_POST['password'], $_POST['username'])) {
            respond(400, "error", ["message" => "missing data."]);
        }
        if (!validateEmail($_POST["email"])) {
            respond(400, "error", ["message" => "Invalid Email."]);
        }
        create_user($_POST["username"], $_POST["email"], $_POST["password"]);
        respond(201, "success", ["message" => "User created successfully"]);
    }
