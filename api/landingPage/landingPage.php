<?php
header('Content-Type: application/json');

$apiKey = 're_bHMJ8QEX_N7D86RqPN36Vhk6k9kvYWuJS';

// Build email details from POST
$subject = $_POST['subject'] ?? 'No subject';
$bodyHtml = "<p>Name: " . htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES) . "</p>"
          . "<p>Email: " . htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) . "</p>"
          . "<p>Message:</p><p>" . nl2br(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES)) . "</p>";

$data = [
    "from" => "BayanAI Contact Form <support@bayanai.es>", // Must be verified in Resend
    "to" => "bayanAIteam@outlook.com",
    "subject" => $subject,
    "html" => $bodyHtml
];

// Initialize cURL
$ch = curl_init('https://api.resend.com/emails');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute request
$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Output result
if ($error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Curl error: ' . $error
    ]);
} elseif ($httpCode >= 400) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Resend API error',
        'response' => json_decode($response, true)
    ]);
} else {
    echo json_encode([
        'status' => 'success',
        'message' => 'Message sent successfully!',
        'response' => json_decode($response, true)
    ]);
}
