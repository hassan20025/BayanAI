<?php
    require_once "ChatRepository.php";
    require_once "../../utils/utils.php";

    function get_user_chats(int $user_id) {
        respond(200, "success", find_all_chats($user_id));
    }

    function create_new_chat(int $user_id, string $title) {
        $chat = new Chat($user_id, $title);
        $chat = create_chat($chat);
        if (!$chat) {
            respond(400, "error", "Failed to create chat");
        }
        respond(200, "success", $chat);
    }

    function delete_chat($user_id, $chatId) {
        $chat = find_chat_by_id($chatId);
        if (!$chat || $chat->getUserId() != $user_id) {
            respond(404, "error", "Chat not found");
        }
        delete_chat_by_id($chatId);
    }
