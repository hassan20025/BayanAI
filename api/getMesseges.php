<?php
require_once '../db/db.php';
require_once '../api/authUser.php';

// Query to get user messages and their corresponding bot replies
$query = "
    SELECT 
        m1.id as msg_id,
        m1.chat_id,
        u.username,
        m1.content as user_message,
        m1.created_at as user_time,
        m2.content as bot_response,
        m2.created_at as bot_time
    FROM messages m1
    INNER JOIN chats c ON m1.chat_id = c.id
    INNER JOIN users u ON c.user_id = u.id
    LEFT JOIN messages m2 
        ON m2.chat_id = m1.chat_id 
        AND m2.role = 'bot' 
        AND m2.id > m1.id
    WHERE m1.role = 'user'
    ORDER BY m1.created_at ASC
";

$result = $db->query($query);

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        "time" => $row["user_time"],
        "username" => $row["username"],
        "message" => $row["user_message"],
        "response" => $row["bot_response"] ?? null
    ];
}

header('Content-Type: application/json');
echo json_encode($messages);
