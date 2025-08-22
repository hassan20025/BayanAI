<?php
require_once "../api/sessions/SessionService.php";
require_once "../utils/utils.php";
require_once "../api/users/UserService.php";

require_once '../db/db.php';

$user_id = get_authenticated_user_id();

$stmt = $db->prepare("SELECT subscription_id FROM subscriptions WHERE user_id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$subscription = $stmt->get_result()->fetch_assoc();

if (!$subscription) {
    echo json_encode(["status" => "unsubscribed", "message" => "Invalid account (no subscription found)"]);
    exit;
}

$subscription_id = $subscription['subscription_id'];

$stmt = $db->prepare("SELECT status FROM payments WHERE subscription_id=? ORDER BY paid_at DESC LIMIT 1");
$stmt->bind_param("i", $subscription_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result && $result['status'] === 'success') {
    echo json_encode(["status" => "subscribed"]);
} else {
    echo json_encode(["status" => "unsubscribed", "message" => "Invalid account"]);
}
