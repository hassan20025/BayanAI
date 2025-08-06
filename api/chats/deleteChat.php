<?php
require_once "ChatService.php";
require_once "../../utils/utils.php";
require_once "../sessions/SessionService.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['chatId'])) {
        respond(400, "error", ["message" => "Missing chat title."]);
    }
    $chatId = $_POST["chatId"];

    $user_id = get_authenticated_user_id();
    if (!$user_id) {
        respond(401, "error", ["message" => "Unauthorized."]);
    }

    delete_chat($user_id, $chatId);
}
