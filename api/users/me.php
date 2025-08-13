<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";
    header("Access-Control-Allow-Origin: *");

    header("Access-Control-Allow-Methods: *");

    header("Access-Control-Allow-Headers: *");

    if ($_SERVER["REQUEST_METHOD"] != "GET") {
        respond(405, "error", "Unsupported method.");
    }
    get_me($_COOKIE["session_token"] ?? null);