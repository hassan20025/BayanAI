<?php
require_once "sessionRepository.php";
require_once "../../utils/utils.php";

function get_authenticated_user_id(): int {
    if (!isset($_COOKIE["session_token"])) {
        respond(401, "error", ["message" => "Not authenticated."]);
    }

    $token = $_COOKIE["session_token"];
    $session = get_session_by_token($token);

    if (!$session) {
        respond(401, "error", ["message" => "Invalid or expired session."]);
    }

    return intval($session["user_id"]);
}
