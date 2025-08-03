<?php
    require_once "DocumentChunkService.php";
    require_once "../../utils/utils.php";

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        respond(409, "error", "Unsupported method.");
    }

    $question = $_POST["question"] ?? null;
    if (!$question) {
        respond(400, "error", "Missing data.");
    }

    get_answer($question);