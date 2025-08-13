<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";
    require_once "../sessions/SessionService.php";

    get_authenticated_user_id();
    respond(200, "success", get_all_users());