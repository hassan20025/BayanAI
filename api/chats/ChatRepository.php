<?php
require_once "Chat.php";
require_once "../../db/db.php";

function find_all_chats(?int $userId = null): array {
    global $mysqli;

    if ($userId !== null) {
        $stmt = $mysqli->prepare("SELECT * FROM chats WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
    } else {
        $stmt = $mysqli->prepare("SELECT * FROM chats ORDER BY created_at DESC");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $chats = [];
    while ($row = $result->fetch_assoc()) {
        $chat = new Chat($row["user_id"], $row["title"], $row["created_at"]);
        $chat->setId($row["id"]);
        $chats[] = $chat;
    }

    $stmt->close();
    return $chats;
}

function find_chat_by_id(int $id): ?Chat {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM chats WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $chat = new Chat($row["user_id"], $row["title"], $row["created_at"]);
        $chat->setId($row["id"]);
        return $chat;
    }

    return null;
}

function create_chat(Chat $chat): ?Chat {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO chats (user_id, title) VALUES (?, ?)");
    $userId = $chat->getUserId();
    $title = $chat->getTitle();
    $stmt->bind_param("is", $userId, $title);

    if ($stmt->execute()) {
        $chat->setId($mysqli->insert_id);
        return $chat;
    }

    return null;
}

function delete_chat_by_id(int $id): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM chats WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
