<?php
    require_once "DocumentChunkService.php";
    require_once "../../utils/utils.php";
    require_once "../sessions/SessionService.php";

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        respond(405, "error", "Unsupported method.");
    }

    $chunk_id = $_POST["chunk_id"] ?? null;
    if (!$chunk_id) {
        respond(400, "error", "Missing data.");
    }
    $safe_chunk_id = escapeshellcmd(htmlspecialchars($chunk_id, ENT_QUOTES, "UTF-8"));
    if (!ctype_digit($safe_chunk_id)) {
        respond(400, "error", "Invalid data.");
    }

    delete_chunk(get_authenticated_user_id(), (int) $safe_chunk_id);