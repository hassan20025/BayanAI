<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        respond(405, "error", "Unsupported method");
    }
    if (!isset($_POST["userId"])) {
        respond(400, "error", "Missing data");
    }

    $userId = escapeshellcmd(htmlspecialchars($_POST["userId"]));
    if (!ctype_digit($userId)) {
        respond(400, "error", "Invalid data");
    }

    update_user_wrapper($userId, $_POST);