<?php
header('Content-Type: application/json');

require __DIR__ . '/../../vendor/autoload.php'; // adjust if vendor folder is elsewhere
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create log file path
$logFile = __DIR__ . '/mail_log.txt';

try {
    $mail = new PHPMailer(true);

    // Enable SMTP debug output
    $mail->SMTPDebug = 3; // 2 = verbose, 3 = more detail
    $mail->Debugoutput = function($str, $level) use ($logFile) {
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Level $level: $str\n", FILE_APPEND);
    };

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bayanAIteam@outlook.com'; // your email
    $mail->Password   = 'bayanteam2025'; // app password if 2FA enabled
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('bayanAIteam@outlook.com', 'BayanAI Contact Form');
    $mail->addAddress('bayanAIteam@outlook.com'); // recipient

    // Content
    $mail->isHTML(true);
    $mail->Subject = $_POST['subject'] ?? 'No subject';
    $mail->Body    = "Name: " . ($_POST['name'] ?? '') . "<br>Email: " . ($_POST['email'] ?? '') . "<br>Message:<br>" . nl2br($_POST['message'] ?? '');

    if ($mail->send()) {
        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unknown error while sending.']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Mailer Error: {$mail->ErrorInfo} | Exception: " . $e->getMessage()]);
}
