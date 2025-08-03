<?php
require_once "../../services/SessionService.php";
require_once "../../utils/utils.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["content"], $_POST["chat_id"])) {
        respond(400, "error", ["message" => "Missing data."]);
    }

    $content = trim($_POST["content"]);
    $chatId = intval($_POST["chat_id"]);

    if ($content === "") {
        respond(400, "error", ["message" => "Message cannot be empty."]);
    }

    $userId = get_authenticated_user_id();
    send_message($userId, $chatId, $content, "user");

    respond(201, "success", ["message" => "Message created successfully."]);
}
