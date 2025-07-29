<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";
    header('Content-Type: application/json');
    login_user($_POST["email"], $_POST["password"]);