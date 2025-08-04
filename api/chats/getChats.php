<?php
    require_once "../sessions/SessionService.php";
    require_once "../../utils/utils.php";
    require_once "ChatService.php";

    if ($_SERVER["REQUEST_METHOD"] != "GET") {
        respond(400, "error", "Invalid method.");
    }
    $userId = get_authenticated_user_id();
    get_user_chats($userId);
