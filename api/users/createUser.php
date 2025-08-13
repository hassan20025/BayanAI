<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        respond(405, "error", "Method unsupported");
    }
    if (!isset($_POST['email'], $_POST['password'], $_POST['username'])) {
        respond(400, "error", ["message" => "missing data."]);
    }
    $email = escapeshellcmd(htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'));
    $password = escapeshellcmd(htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8'));
    $username = escapeshellcmd(htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8'));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(400, "error", ["message" => "Invalid Email."]);
    }
    create_user($username, $email, $password);
    respond(201, "success", ["message" => "User created successfully"]);
