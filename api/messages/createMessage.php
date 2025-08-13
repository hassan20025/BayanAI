<?php
require_once "../sessions/SessionService.php";
require_once "../../utils/utils.php";
require_once "MessageService.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(405, "error", ["message" => "Only POST allowed."]);
}

if (!isset($_POST["content"], $_POST["chatId"])) {
    respond(400, "error", ["message" => "Missing data."]);
}

$rawContent = $_POST["content"];
$rawChatId = $_POST["chatId"];

$safeContent = escapeshellcmd(htmlspecialchars(trim($rawContent), ENT_QUOTES, 'UTF-8'));
$safeChatId = escapeshellcmd(htmlspecialchars($rawChatId, ENT_QUOTES, 'UTF-8'));

if (!ctype_digit($safeChatId)) {
    respond(400, "error", ["message" => "Invalid chat ID."]);
}
$chatId = (int)$safeChatId;

if ($safeContent === "") {
    respond(400, "error", ["message" => "Message cannot be empty."]);
}

$userId = get_authenticated_user_id();
send_message($userId, $safeChatId, $safeContent, "user");

respond(201, "success", ["message" => "Message created successfully."]);