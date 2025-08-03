<?php
    require_once "../../services/SessionService.php";
    require_once "../../utils/utils.php";

    if ($_SERVER["REQUEST_METHOD"] != "GET") {
        respond(400, "error", "Invalid method.");
    }
    $chatId = $_GET["chatId"];
    $userId = get_authenticated_user_id();
    find_messages_by_chat_id($chatId);
