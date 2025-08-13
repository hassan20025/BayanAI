<?php
    require_once "ChatService.php";
    require_once "../../utils/utils.php";
    require_once "../sessions/SessionService.php";
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        respond(405, "error", ["message" => "Only POST allowed."]);
    }
    if (!isset($_POST['title'])) {
        respond(400, "error", ["message" => "Missing chat title."]);
    }
    $title = trim($_POST['title']);
    $safeTitle = escapeshellcmd(htmlspecialchars($title, ENT_QUOTES, 'UTF-8'));
    if ($safeTitle === "") {
        respond(400, "error", ["message" => "Chat title cannot be empty."]);
    }
    $user_id = get_authenticated_user_id();
    if (!$user_id) {
        respond(401, "error", ["message" => "Unauthorized."]);
    }
    $chat_id = create_new_chat($user_id, $safeTitle);
