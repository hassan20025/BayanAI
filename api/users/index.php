<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";

    respond(200, "success", get_all_users());