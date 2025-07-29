<?php
    require_once "UserService.php";
    header("Access-Control-Allow-Origin: *");

    header("Access-Control-Allow-Methods: *");

    header("Access-Control-Allow-Headers: *");

    get_me($_COOKIE["session_token"] ?? null);