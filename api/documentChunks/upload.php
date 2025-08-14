<?php

require_once "../../utils/utils.php";
require_once "DocumentChunkService.php";
require_once "../users/UserService.php";
require_once "../sessions/SessionService.php";

$configPath = __DIR__ . "/../../config.php";

if (!file_exists($configPath)) {
    respond(500, "error", "Missing config.php file");
}

$config = require $configPath;

if (!is_array($config) || !isset($config["gemini_api_key"])) {
    respond(500, "error", "Invalid config.php format or missing gemini_api_key");
}

$API_KEY = $config["gemini_api_key"];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, "error", "Only POST allowed");
}

if (!isset($_FILES['files']) || empty($_FILES['files']['name'][0])) {
    respond(400, "error", "No files uploaded");
}

$prompt = 'Extract all text content from each uploaded file and return the results as a JSON array. 
Format: ["extracted text here", "extracted text here"]
Preserve all text including headers, paragraphs, lists, and tables. Maintain original formatting where possible.
If a file cannot be processed, include it with "error" field explaining the issue.';

$parts = [];

foreach ($_FILES['files']['tmp_name'] as $index => $tmpPath) {
    if ($_FILES['files']['error'][$index] !== UPLOAD_ERR_OK) continue;

    $fileName = $_FILES['files']['name'][$index];
    $mimeType = mime_content_type($tmpPath);
    $fileContent = file_get_contents($tmpPath);
    $base64Content = base64_encode($fileContent);

    $parts[] = [
        'inlineData' => [
            'mimeType' => $mimeType,
            'data' => $base64Content
        ]
    ];
}

if (empty($parts)) {
    respond(400, "error", "No files processed successfully.");
}

$parts[] = ['text' => $prompt];

$payload = json_encode([
    'contents' => [
        [
            'role' => 'user',
            'parts' => $parts
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.1,
        'maxOutputTokens' => 8192
    ]
]);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$API_KEY",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 120
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    respond(500, "error", "Network error: " . $curlError);
}

if ($httpCode >= 200 && $httpCode < 300) {
    $parsed = json_decode($response, true);
    $text = $parsed['candidates'][0]['content']['parts'][0]['text'] ?? null;

    if ($text) {
        $cleanedText = preg_replace('/^```(?:json)?\n([\s\S]*?)\n```$/', '$1', $text);

        $json = json_decode($cleanedText, true);

        $userId = get_authenticated_user_id();
        $user = get_user_by_id($userId);
        if (json_last_error() === JSON_ERROR_NONE) {
            create_chunks($json, $user->getDepartment());
        } else {
            create_chunks($cleanedText, $user->getDepartment());
        }
    } else {
        respond(500, "error", "Gemini response format unexpected.");
    }
} else {
    $error = json_decode($response, true);
    $message = $error['error']['message'] ?? "Unknown error from Gemini API";
    respond($httpCode, "error", $message);
}