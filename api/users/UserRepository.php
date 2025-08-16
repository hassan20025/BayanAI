<?php
require_once "User.php";
require_once __DIR__ . "/../../db/db.php";

global $mysqli;

function find_all_users(): array {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = new User(+$row["id"], $row["email"], $row["password"], $row["username"], $row["department"], +$row["can_upload"], $row["role"]);
    }
    return $users;
}

function find_user_by_id(int $id): ?User {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        return new User($row["id"], $row["email"], $row["password"], $row["username"], $row["department"], $row["can_upload"], $row["role"]);
    }
    return null;
}

function find_user_by_email(string $email): ?User {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        return new User($row["id"], $row["email"], $row["password"], $row["username"], $row["department"], $row["can_upload"], $row["role"]);
    }
    return null;
}

function find_user_by_username(string $username): ?User {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        return new User($row["id"], $row["email"], $row["password"], $row["username"], $row["department"], $row["can_upload"], $row["role"]);
    }
    return null;
}

function create_user_entity(User $user): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO users (email, password, username, department, can_upload, role) VALUES (?, ?, ?, ?, ?, ?)");
    $email = $user->getEmail();
    $password = $user->getPassword();
    $username = $user->getUsername();
    $dept = $user->getDepartment();
    $can_upload = $user->get_can_upload();
    $role = $user->getRole();
    $stmt->bind_param("ssssis", $email, $password, $username, $dept, $can_upload, $role);
    return $stmt->execute();
}

function update_user(User $user): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ?, password = ?, department = ?, can_upload = ?, role = ? WHERE id = ?");
    $email = $user->getEmail();
    $role = $user->getRole();
    $password = $user->getPassword();
    $username = $user->getUsername();
    $id = $user->getId();
    $dept = $user->getDepartment();
    $can_upload = $user->get_can_upload();
    $stmt->bind_param("ssssisi", $email, $password, $username, $dept, $can_upload, $role, $id);
    return $stmt->execute();
}

function delete_user_by_id(int $id): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
