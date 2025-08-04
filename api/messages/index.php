<?php
    require_once "../sessions/SessionService.php";
    require_once "MessageService.php";
    require_once "../../utils/utils.php";

    if ($_SERVER["REQUEST_METHOD"] != "GET") {
        respond(400, "error", "Invalid method.");
    }
    if (!isset($_GET['chatId']) || !is_numeric($_GET['chatId'])) {
        respond(400, "error", "Missing or invalid chatId parameter");
    }
    $chatId = $_GET["chatId"];
    $userId = get_authenticated_user_id();
    find_messages_by_chat_id($chatId);