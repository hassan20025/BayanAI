<?php

require_once "UserRepository.php";
require_once "../../utils/utils.php";
require_once "../sessions/SessionRepository.php";
require_once "../departments/DepartmentService.php";
session_start();
header('Content-Type: application/json');

function login_user($email, $password) {

    if (!$email || !$password) {
        respond(400, "error", "Missing Data.");
    }

    $user = get_user_by_email($email);

    if (!$user || !password_verify($password, $user->getPassword())) {
        respond(400, "error", ["message" => "Wrong email or password."]);
    }

    $userId = $user->getId();
    $expiresAt = date("Y-m-d H:i:s", strtotime("+7 days"));

    $token = create_session($userId, $expiresAt);

    if (!$token) {
        respond(500, "error", ["message" => "Failed to create session."]);
    }

    setcookie(
        "session_token",
        $token,
        [
            "expires" => strtotime($expiresAt),
            "path" => "/",
            // "secure" => true,
            "httponly" => true,
            "samesite" => "Lax"
        ]
    );

    respond(200, "success", ["message" => "Logged in successfully"]);
}

function logout_user($token) {

    if ($token) {
        delete_session_by_token($token);
        setcookie("session_token", "", time() - 3600, "/");
    }

    respond(200, "success", ["message" => "Logged out successfully"]);
}

function get_me($sessionToken) {

    if (!isset($sessionToken)) {
        respond(401, "error", ["message" => "Not logged in"]);
    }
    
    $session = get_session_by_token($sessionToken);

    if (!$session || $session["expires_at"] < date("Y-m-d H:i:s")) {
        respond(401, "error", ["message" => "Session expired or invalid"]);
    }

    $user = find_user_by_id($session["user_id"]);

    if (!$user) {
        respond(404, "error", ["message" => "User not found"]);
    }

    respond(200, "success", $user);
}

function create_user($username, $email, $password, $department = null) {

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $user = find_user_by_email($email);

    if ($user) {
        respond(400, "error", "Email already used");
    }

    $user = find_user_by_username($username);
    if ($user) {
        respond(400, "error", "Username already used");
    }

    $user = new User(null, $email, $hashedPassword, $username, $department);
    $created = create_user_entity($user);

    if (!$created) {
        respond(500, "error", "Failed to create user.");
    }

    return $user;
}

function get_all_users() {
    return find_all_users();
}

function get_user_by_id($id) {
    $user = find_user_by_id($id);
    if (!$user) {
        respond(404, "error", ["message" => "User not found."]);
    }
    return $user;
}

function get_user_by_email($email) {

    $user = find_user_by_email($email);
    if (!$user) {
        respond(404, "error", ["message" => "User not found."]);
    }
    return $user;
}

function get_user_by_username($username) {

    $user = find_user_by_username($username);
    if (!$user) {
        respond(404, "error", ["message" => "User not found."]);
    }
    return $user;
}

function delete_user($id) {
    delete_user_by_id($id);
}

function update_user_wrapper($id, $data) {
    $user = get_user_by_id($id);
    if (!$user) {
        return false;
    }
    $updatedUserArray = array_merge((array)$user, $data);
    $updatedUser = new User(
        $updatedUserArray['id'],
        $updatedUserArray['email'],
        $updatedUserArray['password'],
        $updatedUserArray['username'],
        $updatedUserArray["department"]
    );
    return update_user($updatedUser);
}
