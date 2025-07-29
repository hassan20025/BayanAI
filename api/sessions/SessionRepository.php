<?php
require_once "../../db/db.php";
class SessionRepository {
    private $conn;

    public function __construct() {
        global $mysqli;
        $this->conn = $mysqli;
    }

    public function createSession(int $userId, string $expiresAt): ?string {
        $token = bin2hex(random_bytes(32)); // 64-character secure token

        $stmt = $this->conn->prepare("
            INSERT INTO sessions (user_id, session_token, expires_at)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $userId, $token, $expiresAt);

        if ($stmt->execute()) {
            return $token;
        }

        return null;
    }

    public function getSessionByToken(string $token): ?array {
        $stmt = $this->conn->prepare("
            SELECT * FROM sessions WHERE session_token = ? AND expires_at > NOW()
        ");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }


    public function deleteSessionByToken(string $token): bool {
        $stmt = $this->conn->prepare("
            DELETE FROM sessions WHERE session_token = ?
        ");
        $stmt->bind_param("s", $token);
        return $stmt->execute();
    }

    public function cleanExpiredSessions(): bool {
        $stmt = $this->conn->prepare("
            DELETE FROM sessions WHERE expires_at < NOW()
        ");
        return $stmt->execute();
    }

}

$sessionRepository = new SessionRepository();
