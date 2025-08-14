<?php
    require_once "UserService.php";
    require_once "../../utils/utils.php";

    class UserController {

        private $userService;

        function __construct() {
            global $userService;
            $this->userService = $userService;
        }

        function get() {
            respond(200, "success", ["message" => "message"]);
        }

        function post() {

        }

    }

$userController = new UserController();
