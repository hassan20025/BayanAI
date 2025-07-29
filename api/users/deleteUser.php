<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";

    if (!isset($_GET["userId"])) {
        respond(400, "error", ["message" => "missing data."]);
    }
    delete_user($_GET["userId"]);
    respond(200, "success", ["message" => "User deleted."]);