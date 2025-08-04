<?php
class Chat implements JsonSerializable {
    private int $id;
    private int $user_id;
    private string $title;
    private string $created_at;

    public function __construct(int $user_id, string $title, ?string $created_at = null) {
        $this->user_id = $user_id;
        $this->title = $title;
        $this->created_at = $created_at ?? date('Y-m-d H:i:s');
    }

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

    public function jsonSerialize(): mixed {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'created_at' => $this->created_at
        ];
    }

}
