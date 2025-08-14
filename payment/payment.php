<?php
require_once '../db/db.php';
$message = "";

// ---- PROCESS PAYMENT FORM ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subscription_id = intval($_POST['subscription_id']);
    $name = trim($_POST['name']);
    $card_number = trim($_POST['card_number']);
    $expiry = trim($_POST['expiry']);
    $cvv = trim($_POST['cvv']);
    $amount = floatval($_POST['amount']);

    // Basic validation
    if ($name && $card_number && $expiry && $cvv && $amount > 0) {
        // Simulate payment gateway
        $payment_success = rand(0, 1) ? true : false;
        $transaction_id = uniqid("TXN_");
        $status = $payment_success ? 'success' : 'failed';
        
        // Record payment
        $stmt = $db->prepare("INSERT INTO payments (subscription_id, transaction_id, amount, status, paid_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isds", $subscription_id, $transaction_id, $amount, $status);
        $stmt->execute();

        if ($payment_success) {
            $message = "✅ Payment successful! Transaction ID: $transaction_id";
            $db->query("UPDATE subscriptions SET status='active', updated_at=NOW() WHERE subscription_id=$subscription_id");
        } else {
            $message = "❌ Payment failed. Please try again.";
        }
    } else {
        $message = "⚠ Please fill in all fields.";
    }
}

// ---- FETCH SUBSCRIPTION (example: subscription_id=1) ----
$subscription = $db->query("SELECT * FROM subscriptions WHERE subscription_id=1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Page</title>
<style>
    body {
        background-color: rgba(30, 41, 59, 0.8);
        font-family: Arial, sans-serif;
        margin: 0; padding: 0;
        display: flex; justify-content: center; align-items: center;
        height: 100vh; color: #fff;
    }
    .container {
        background: #1e293b;
        padding: 20px;
        border-radius: 10px;
        max-width: 400px; width: 100%;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }
    h2 { text-align: center; margin-bottom: 20px; }
    label { display: block; margin: 10px 0 5px; }
    input {
        width: 90%; padding: 10px;
        border: none; border-radius: 5px;
        margin-bottom: 15px;
    }
    button {
        width: 90%; padding: 10px;
        background: #4CAF50; color: #fff;
        border: none; border-radius: 5px;
        font-size: 16px; cursor: pointer;
    }
    button:hover { background: #45a049; }
    .message {
        margin-top: 15px; padding: 10px;
        border-radius: 5px; background: #333;
        text-align: center;
    }
    @media (max-width: 500px) {
        .container { margin: 10px; }
    }
</style>
</head>
<body>
<div class="container">
    <h2>Payment</h2>
    <?php if($subscription): ?>
        <p>Subscription ID: <?= $subscription['subscription_id'] ?></p>
        <p>Status: <?= ucfirst($subscription['status']) ?></p>
        <p>Amount: $<strong>50.00</strong></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="subscription_id" value="<?= $subscription['subscription_id'] ?>">
        <input type="hidden" name="amount" value="50.00">
        <label>Cardholder Name</label>
        <input type="text" name="name" placeholder="John Doe" required>
        <label>Card Number</label>
        <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required>
        <label>Expiry Date</label>
        <input type="text" name="expiry" placeholder="MM/YY" required>
        <label>CVV</label>
        <input type="text" name="cvv" placeholder="123" required>
        <button type="submit">Pay Now</button>
    </form>
    <?php if($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>
</div>
</body>
</html>
