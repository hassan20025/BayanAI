<?php
require_once "../../services/ChatService.php";
require_once "../../utils/utils.php";
require_once "../sessions/SessionService.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['title'])) {
        respond(400, "error", ["message" => "Missing chat title."]);
    }

    $title = trim($_POST['title']);
    if ($title === "") {
        respond(400, "error", ["message" => "Chat title cannot be empty."]);
    }

    $user_id = get_authenticated_user_id();
    if (!$user_id) {
        respond(401, "error", ["message" => "Unauthorized."]);
    }

    $chat_id = create_new_chat($user_id, $title);
    if (!$chat_id) {
        respond(500, "error", ["message" => "Failed to create chat."]);
    }

    respond(201, "success", ["message" => "Chat created successfully.", "chat_id" => $chat_id]);
}
