<?php
require_once '../db/db.php';
$message = "";

// ---- PROCESS PAYMENT FORM ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subscription_id = intval($_POST['subscription_id']);
    $card_number = trim($_POST['card_number']);
    $expiry = trim($_POST['expiry']);
    $cvv = trim($_POST['cvv']);
    $amount = floatval($_POST['amount']);

    if ($card_number && $expiry && $cvv && $amount > 0) {
        $payment_success = rand(0, 1) ? true : false;
        $transaction_id = uniqid("TXN_");
        $status = $payment_success ? 'success' : 'failed';

        $stmt = $db->prepare("INSERT INTO payments (subscription_id, transaction_id, amount, status, paid_at) 
                              VALUES (?, ?, ?, ?, NOW())");
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

// ---- FETCH SUBSCRIPTION ----
$subscription = $db->query("SELECT * FROM subscriptions WHERE subscription_id=1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Page</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
<?php include 'new.css'; ?>
</style>
</head>
<body>
    <nav class="fixed-nav glass-effect">
        <div class="nav-container">
            <div class="nav-left">
                <a href="#" class="logo angular-text">BayanAI</a>
            </div>

            <div class="nav-center desktop-only">
                <div class="nav-links">
                    <a href="#home" class="nav-link spaced-text">Home</a>
                    <a href="#about" class="nav-link spaced-text">About Us</a>
                    <a href="#features" class="nav-link spaced-text">Features</a>
                    <a href="#pricing" class="nav-link spaced-text">Pricing</a>
                    <a href="#contact" class="nav-link spaced-text">Contact Us</a>
                </div>
            </div>

            <div class="mobile-menu-toggle mobile-only">
                <button id="mobile-menu-button" aria-label="Toggle mobile menu">
                    <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x hidden"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="mobile-menu mobile-only hidden glass-effect">
            <div class="mobile-nav-links">
                <a href="#home" class="mobile-nav-link spaced-text">Home</a>
                <a href="#about" class="mobile-nav-link spaced-text">About Us</a>
                <a href="#features" class="mobile-nav-link spaced-text">Features</a>
                <a href="#pricing" class="mobile-nav-link spaced-text">Pricing</a>
                <a href="#contact" class="mobile-nav-link spaced-text">Contact Us</a>
            </div>
        </div>
    </nav>
<div class="container d-flex justify-content-center mt-5 mb-5">
    <div class="row g-3">

        <!-- Payment Method -->
        <div class="col-md-6">
            <span>Payment Method</span>
            <div class="card">
                <form method="POST">
                    <input type="hidden" name="subscription_id" value="<?= $subscription['subscription_id'] ?>">
                    <input type="hidden" name="amount" value="9.99">

                    <div class="accordion" id="accordionExample">

                        <!-- Credit Card -->
                        <div class="card">
                            <div class="card-header p-0">
                                <button class="btn btn-light btn-block text-left p-3 rounded-0" 
                                        data-toggle="collapse" data-target="#collapseOne" 
                                        aria-expanded="true" aria-controls="collapseOne">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>Credit card</span>
                                        <div class="icons">
                                            <img src="https://i.imgur.com/2ISgYja.png" width="30">
                                            <img src="https://i.imgur.com/W1vtnOV.png" width="30">
                                            <img src="https://i.imgur.com/35tC99g.png" width="30">
                                        </div>
                                    </div>
                                </button>
                            </div>

                            <div id="collapseOne" class="collapse show" data-parent="#accordionExample">
                                <div class="card-body payment-card-body">
                                    <span class="font-weight-normal card-text">Card Number</span>
                                    <div class="input">
                                        <i class="fa fa-credit-card"></i>
                                        <input type="text" class="form-control" name="card_number" placeholder="0000 0000 0000 0000" required>
                                    </div>

                                    <div class="row mt-3 mb-3">
                                        <div class="col-md-6">
                                            <span class="font-weight-normal card-text">Expiry Date</span>
                                            <div class="input">
                                                <i class="fa fa-calendar"></i>
                                                <input type="text" class="form-control" name="expiry" placeholder="MM/YY" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="font-weight-normal card-text">CVC/CVV</span>
                                            <div class="input">
                                                <i class="fa fa-lock"></i>
                                                <input type="text" class="form-control" name="cvv" placeholder="000" required>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block free-button">
                                        Pay $9.99
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <?php if($message): ?>
                <div class="alert alert-info mt-3"><?= $message ?></div>
            <?php endif; ?>
        </div>

        <!-- Summary -->
        <div class="col-md-6">
            <span>Summary</span>
            <div class="card">
                <div class="d-flex justify-content-between p-3">
                    <div class="d-flex flex-column">
                        <span><?= ucfirst($subscription['status']) ?> Plan</span>
                        <a href="#" class="billing">Save 20% with annual billing</a>
                    </div>
                    <div class="mt-1">
                        <sup class="super-price">$9.99</sup>
                        <span class="super-month">/Month</span>
                    </div>
                </div>
                <hr class="mt-0 line">
                <div class="p-3 d-flex justify-content-between">
                    <div class="d-flex flex-column">
                        <span>Today you pay (USD)</span>
                        <small>After 30 days $9.99</small>
                    </div>
                    <span>$0</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
