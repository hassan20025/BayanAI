<?php
    require_once "DocumentChunkService.php";
    require_once "../sessions/SessionService.php";
    require_once "../../utils/utils.php";

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        respond(405, "error", "Unsupported method.");
    }

    $question = $_POST["question"] ?? null;
    $chatId = $_POST["chatId"] ?? null;
    if (!$question || !$chatId) {
        respond(400, "error", "Missing data.");
    }
    $safe_chat_id = escapeshellcmd(htmlspecialchars($chatId, ENT_QUOTES, "UTF-8"));
    $safe_question = escapeshellcmd(htmlspecialchars($question, ENT_QUOTES, "UTF-8"));
    if (!ctype_digit($safe_chat_id)) {
        respond(400, "error", "Invalid data.");
    }

    get_answer($safe_question, $safe_chat_id, get_authenticated_user_id());