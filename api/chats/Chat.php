<?php
class Chat {
    private int $id;
    private int $user_id;
    private string $title;
    private string $created_at;

    public function getId(): int { 
        return $this->id;
    }
    public function setId(int $id): void { 
        $this->id = $id;
    }

    public function getUserId(): int { 
        return $this->user_id;
    }
    public function setUserId(int $user_id): void { 
        $this->user_id = $user_id;
    }

    public function getTitle(): string { 
        return $this->title; 
    }
    public function setTitle(string $title): void { 
        $this->title = $title; 
    }

    public function getCreatedAt(): string { 
        return $this->created_at; 
    }
    public function setCreatedAt(string $created_at): void { 
        $this->created_at = $created_at; 
    }
}
