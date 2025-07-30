<?php
header("Content-Type: application/json"); 
require "../vendor/autoload.php";
require "./utils.php";
$config = require "../config.php";
$apiKey = $config["gemini_api_key"];

use Smalot\PdfParser\Parser;

function extractPdfText($filePath) {
    $parser = new Parser();
    $pdf = $parser->parseFile($filePath);
    return $pdf->getText();
}

function askGemini($pdfText) {
    global $apiKey;
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

    $prompt = <<<EOT
Return ONLY valid JSON without any markdown formatting, code blocks, or additional text. No ```json``` tags.

Analyze the following PDF content and return this exact JSON structure:
{
  "summary": "Brief summary of the main points",
  "categories": {
    "Category 1": "Relevant content here",
    "Category 2": "More content"
  }
}

Content:
$pdfText
EOT;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.1,
            "topK" => 1,
            "topP" => 0.1,
            "maxOutputTokens" => 2048
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
    
    $geminiResponse = json_decode($response, true);
    
    // Extract the actual content from Gemini's response
    if (isset($geminiResponse['candidates'][0]['content']['parts'][0]['text'])) {
        $rawText = $geminiResponse['candidates'][0]['content']['parts'][0]['text'];
        
        // Remove markdown code blocks if present
        $cleanedText = preg_replace('/```json\s*|\s*```/', '', $rawText);
        $cleanedText = trim($cleanedText);
        
        // Parse the cleaned JSON
        $analysisResult = json_decode($cleanedText, true);
        
        if ($analysisResult !== null) {
            return $analysisResult;
        } else {
            // Fallback: try to extract JSON from the raw text
            if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                $extractedJson = json_decode($matches[0], true);
                if ($extractedJson !== null) {
                    return $extractedJson;
                }
            }
            return ["error" => "Could not parse JSON response"];
        }
    }
    
    return ["error" => "No valid response from Gemini"];
}

// function storeAnalysisInDB($analysisData, $pdo, $documentId = null) {
//     try {
//         // Create table if it doesn't exist
//         $createTableSQL = "
//             CREATE TABLE IF NOT EXISTS pdf_analysis (
//                 id INT AUTO_INCREMENT PRIMARY KEY,
//                 document_id INT NULL,
//                 summary TEXT,
//                 categories JSON,
//                 full_analysis JSON,
//                 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//                 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//             )
//         ";
//         $pdo->exec($createTableSQL);
        
//         // Insert the analysis data
//         $sql = "INSERT INTO pdf_analysis (document_id, summary, categories, full_analysis) VALUES (?, ?, ?, ?)";
//         $stmt = $pdo->prepare($sql);
        
//         $summary = $analysisData['summary'] ?? '';
//         $categories = json_encode($analysisData['categories'] ?? []);
//         $fullAnalysis = json_encode($analysisData);
        
//         $stmt->execute([$documentId, $summary, $categories, $fullAnalysis]);
        
//         return [
//             'success' => true,
//             'analysis_id' => $pdo->lastInsertId(),
//             'message' => 'Analysis stored successfully'
//         ];
        
//     } catch (PDOException $e) {
//         return [
//             'success' => false,
//             'error' => 'Database error: ' . $e->getMessage()
//         ];
//     }
// }

// function askGemini($pdfText) {
//     global $apiKey;
//     $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

//     $prompt = <<<EOT
// You must respond ONLY with valid JSON. Do not include any explanatory text, greetings, or additional comments. 

// Analyze the following PDF content and return ONLY this JSON structure:
// {
//   "summary": "Brief summary of the main points",
//   "categories": {
//     "Category 1": "Relevant content here",
//     "Category 2": "More content"
//   }
// }

// Content:
// $pdfText
// EOT;

//     $data = [
//         "contents" => [
//             [
//                 "parts" => [
//                     ["text" => $prompt] // Removed the substr limitation
//                 ]
//             ]
//         ],
//         "generationConfig" => [
//             "temperature" => 0.1,
//             "topK" => 1,
//             "topP" => 0.1,
//             "maxOutputTokens" => 2048
//         ]
//     ];

//     $ch = curl_init($url . "?key=" . $apiKey);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         "Content-Type: application/json"
//     ]);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

//     $response = curl_exec($ch);

//     if (curl_errno($ch)) {
//         respond(500, "error", curl_error($ch));
//     }

//     curl_close($ch);
//     return json_decode($response, true);
// }

$pdfText = extractPdfText("../pdf/test.pdf");
$response = askGemini($pdfText);
respond(200, "success", $response);