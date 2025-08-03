<?php
require_once "Chat.php";
require_once "../../db/db.php";

function find_all_chats(): array {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM chats ORDER BY created_at DESC");
    $chats = [];

    while ($row = $result->fetch_assoc()) {
        $chat = new Chat();
        $chat->setId($row["id"]);
        $chat->setUserId($row["user_id"]);
        $chat->setTitle($row["title"]);
        $chat->setCreatedAt($row["created_at"]);
        $chats[] = $chat;
    }

    return $chats;
}

function find_chat_by_id(int $id): ?Chat {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM chats WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $chat = new Chat();
        $chat->setId($row["id"]);
        $chat->setUserId($row["user_id"]);
        $chat->setTitle($row["title"]);
        $chat->setCreatedAt($row["created_at"]);
        return $chat;
    }

    return null;
}

function create_chat(Chat $chat): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO chats (user_id, title) VALUES (?, ?)");
    $userId = $chat->getUserId();
    $title = $chat->getTitle();
    $stmt->bind_param("is", $userId, $title);
    return $stmt->execute();
}

function delete_chat_by_id(int $id): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM chats WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
