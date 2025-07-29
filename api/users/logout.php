<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";

    logout_user($_COOKIE["session_token"] ?? null);