<?php
    require_once "UserService.php";

    get_me($_COOKIE["session_token"] ?? null);