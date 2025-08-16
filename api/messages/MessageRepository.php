<?php
require_once "Message.php";
require_once __DIR__ . "/../../db/db.php";

function find_messages_by_chat_id(int $chat_id): array {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM messages WHERE chat_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $chat_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $messages = [];
    while ($row = $res->fetch_assoc()) {
        $msg = new Message();
        $msg->setId($row["id"]);
        $msg->setChatId($row["chat_id"]);
        $msg->setContent($row["content"]);
        $msg->setRole($row["role"]);
        $msg->setCreatedAt($row["created_at"]);
        $messages[] = $msg;
    }

    return $messages;
}

function create_message(Message $message): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO messages (chat_id, content, role) VALUES (?, ?, ?)");
    $chatId = $message->getChatId();
    $content = $message->getContent();
    $role = $message->getRole();
    $stmt->bind_param("iss", $chatId, $content, $role);
    return $stmt->execute();
}

function delete_messages_by_chat_id(int $chat_id): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM messages WHERE chat_id = ?");
    $stmt->bind_param("i", $chat_id);
    return $stmt->execute();
}
