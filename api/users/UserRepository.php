<?php
require_once "User.php";
require_once "../../db/db.php";

// Use global $mysqli from db.php
global $mysqli;

function find_all_users(): array {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = new User($row["id"], $row["email"], $row["password"], $row["username"]);
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
        return new User($row["id"], $row["email"], $row["password"], $row["username"]);
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
        return new User($row["id"], $row["email"], $row["password"], $row["username"]);
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
        return new User($row["id"], $row["email"], $row["password"], $row["username"]);
    }
    return null;
}

function create_user_entity(User $user): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO users (email, password, username) VALUES (?, ?, ?)");
    $email = $user->getEmail();
    $password = $user->getPassword();
    $username = $user->getUsername();
    $stmt->bind_param("sss", $email, $password, $username);
    return $stmt->execute();
}

function update_user(User $user): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
    $email = $user->getEmail();
    $password = $user->getPassword();
    $username = $user->getUsername();
    $id = $user->getId();
    $stmt->bind_param("sssi", $username, $email, $password, $id);
    return $stmt->execute();
}

function delete_user_by_id(int $id): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
