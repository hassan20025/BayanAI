<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";

    $userService->updateUser($_POST["id"], $_POST);