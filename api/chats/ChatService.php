<?php
    require_once "ChatRepository.php";

    function get_user_chats(int $user_id): array {
        return array_filter(find_all_chats(), fn($chat) => $chat->getUserId() === $user_id);
    }

    function create_new_chat(int $user_id, string $title): bool {
        $chat = new Chat();
        $chat->setUserId($user_id);
        $chat->setTitle($title);
        return create_chat($chat);
    }
