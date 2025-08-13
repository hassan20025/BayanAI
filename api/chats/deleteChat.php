<?php
    require_once "ChatService.php";
    require_once "../../utils/utils.php";
    require_once "../sessions/SessionService.php";
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        respond(405, "error", ["message" => "Only POST allowed."]);
    }
    if (!isset($_POST['chatId'])) {
        respond(400, "error", ["message" => "Missing chat id."]);
    }
    $chatId = escapeshellcmd(htmlspecialchars($_POST["chatId"], ENT_QUOTES, 'UTF-8'));
    if (!ctype_digit($chatId)) {
        respond(400, "error", ["message" => "Invalid data."]);
    }
    delete_chat($user_id, $chatId);
