<?php
    require_once "MessageRepository.php";
    require_once "../chats/ChatService.php";

    function get_chat_messages(int $userId, int $chat_id): array {
        $chat = find_chat_by_id($chat_id);
        if (!$chat || $chat->getUserId() != $userId) {
            respond(404, "error", "Chat not found");
        }
        return find_messages_by_chat_id($chat_id);
    }
    
    function send_message($userId, int $chat_id, string $content, string $role): bool {
        $message = new Message();
        $chat = find_chat_by_id($chat_id);

        if (!$chat || ($userId && $chat->getUserId() != $userId)) {
            respond(404, "error", "Chat not found");
        }
        $message->setChatId($chat_id);
        $message->setContent($content);
        $message->setRole($role);
        return create_message($message);
    }