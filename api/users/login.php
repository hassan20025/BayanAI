<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    $email = escapeshellcmd(htmlspecialchars($_POST["email"] ?? '', ENT_QUOTES, 'UTF-8'));
    $password = escapeshellcmd(htmlspecialchars($_POST["password"] ?? '', ENT_QUOTES, 'UTF-8'));
    login_user($email, $password);