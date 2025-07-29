<?php
header("Content-Type: application/json"); 
require "../vendor/autoload.php";
$config = require "../config.php";
$apiKey = $config["gemini_api_key"];

use Smalot\PdfParser\Parser;

function respond($statusCode, $status, $data) {
    http_response_code($statusCode);
    echo json_encode([
        "status" => $status,
        "data" => $data
    ]);
    exit;
}

function extractPdfText($filePath) {
    $parser = new Parser();
    $pdf = $parser->parseFile($filePath);
    return $pdf->getText();
}

function askGemini($pdfText) {
    global $apiKey;
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

    $prompt = <<<EOT
Analyze the following PDF content. Summarize the main points and categorize the content into meaningful topics or sections. Output the result as a JSON object with the following structure:
{
  "summary": "...",
  "categories": {
    "Category 1": "Relevant content here...",
    "Category 2": "More content..."
  }
}
Content:
$pdfText
EOT;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => substr($prompt, 0, 200)]
                ]
            ]
        ]
    ];

    $ch = curl_init($url . "?key=" . $apiKey);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        respond(500, "error", curl_error($ch));
    }

    curl_close($ch);
    return json_decode($response, true);
}

$pdfText = extractPdfText("../pdf/company.pdf");
$response = askGemini($pdfText);

// $responseText = $response["candidates"][0]["content"]["parts"][0]["text"];

// // // Match the first JSON object or array
// preg_match("/({.*}|\[.*\])/s", $responseText, $matches);

// if (!empty($matches)) {
//     $cleanJson = $matches[0];
//     $decoded = json_decode($cleanJson, true);

//     if (json_last_error() === JSON_ERROR_NONE) {
//         respond(200, "success", $decoded);
//     } else {
//         respond(200, "success", $cleanJson);
//     }
// } else {
//     respond(400, "error", "No valid JSON found in model response.");
// }

respond(200, "success", $response);