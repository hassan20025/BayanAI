<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";
    require_once "../sessions/SessionService.php";
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        respond(405, "error", "Method unsupported");
    }
    if (!isset($_POST["userId"])) {
        respond(400, "error", ["message" => "missing data."]);
    }
    $userId = $_POST["userId"];
    $safeUserId = escapeshellcmd(htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'));
    delete_user(get_authenticated_user_id(), $safeUserId);
    respond(200, "success", ["message" => "User deleted."]);