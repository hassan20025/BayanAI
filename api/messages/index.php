<?php
require_once "../sessions/SessionService.php";
require_once "MessageService.php";
require_once "../../utils/utils.php";

    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        respond(405, "error", "Invalid method.");
    }

    if (!isset($_GET['chatId'])) {
        respond(400, "error", "Missing chatId parameter.");
    }

    $chatId = $_GET['chatId'];
    $safeChatId = escapeshellcmd(htmlspecialchars($chatId, ENT_QUOTES, 'UTF-8'));

    if (!ctype_digit($safeChatId)) {
        respond(400, "error", "Invalid chatId parameter.");
    }

    $userId = get_authenticated_user_id();
    respond(200, "success", get_chat_messages($userId, $chatId));