<?php
class Message {
    private int $id;
    private int $chat_id;
    private string $content;
    private string $created_at;
    private string $role;
    
    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getChatId(): int { return $this->chat_id; }
    public function setChatId(int $chat_id): void { $this->chat_id = $chat_id; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $content): void { $this->content = $content; }

    public function getCreatedAt(): string { return $this->created_at; }
    public function setCreatedAt(string $created_at): void { $this->created_at = $created_at; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }
}
