<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *");

    header("Access-Control-Allow-Methods: *");

    // Allow specific headers
    header("Access-Control-Allow-Headers: *");
    login_user($_POST["email"] ?? null, $_POST["password"] ?? null);