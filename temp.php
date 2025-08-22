<?php

// require_once "../../utils/utils.php";
// require_once "DocumentChunkService.php";
// require_once "../users/UserService.php";
// require_once "../sessions/SessionService.php";

// $configPath = __DIR__ . "/../../config.php";

// if (!file_exists($configPath)) {
//     respond(500, "error", "Missing config.php file");
// }

// $config = require $configPath;

// if (!is_array($config) || !isset($config["gemini_api_key"])) {
//     respond(500, "error", "Invalid config.php format or missing gemini_api_key");
// }

// $API_KEY = $config["gemini_api_key"];

// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     respond(405, "error", "Only POST allowed");
// }

// if (!isset($_FILES['files']) || empty($_FILES['files']['name'][0])) {
//     respond(400, "error", "No files uploaded");
// }

// // Create upload directory if it doesn't exist
// $uploadDir = __DIR__ . '/../../uploads/knowledge/';
// if (!file_exists($uploadDir)) {
//     if (!mkdir($uploadDir, 0755, true)) {
//         respond(500, "error", "Failed to create upload directory");
//     }
// }

// // Validate and sanitize file uploads
// $allowedTypes = [
//     'text/plain' => 'txt',
//     'application/pdf' => 'pdf',
//     'application/msword' => 'doc',
//     'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
//     'image/jpeg' => 'jpg',
//     'image/png' => 'png'
// ];

// $fileNames = [];
// $fileSizes = [];
// $fileTypes = [];
// $storedFiles = [];
// $combinedParts = [];

// foreach ($_FILES['files']['tmp_name'] as $index => $tmpPath) {
//     if ($_FILES['files']['error'][$index] !== UPLOAD_ERR_OK) {
//         continue;
//     }

//     $finfo = finfo_open(FILEINFO_MIME_TYPE);
//     $mimeType = finfo_file($finfo, $tmpPath);
//     finfo_close($finfo);

//     if (!array_key_exists($mimeType, $allowedTypes)) {
//         respond(400, "error", "Invalid file type: " . htmlspecialchars($_FILES['files']['name'][$index]));
//     }

//     // Sanitize filename
//     $originalName = $_FILES['files']['name'][$index];
//     $sanitizedName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "", basename($originalName));
//     $extension = $allowedTypes[$mimeType];
//     $newFilename = uniqid('doc_', true) . '.' . $extension;
//     $destination = $uploadDir . $newFilename;

//     // Store the file securely
//     if (!move_uploaded_file($tmpPath, $destination)) {
//         respond(500, "error", "Failed to store file: " . htmlspecialchars($originalName));
//     }

//     // Set permissions
//     chmod($destination, 0644);

//     $fileNames[] = $sanitizedName;
//     $fileSizes[] = $_FILES['files']['size'][$index];
//     $fileTypes[] = $extension;
//     $storedFiles[] = $destination;

//     $fileContent = file_get_contents($destination);
//     $base64Content = base64_encode($fileContent);

//     $combinedParts[] = [
//         'text' => "File: $sanitizedName\n" .
//                   "Type: $mimeType\n" .
//                   "Content: [FILE_CONTENT_PLACEHOLDER]"
//     ];
// }

// if (empty($combinedParts)) {
//     foreach ($storedFiles as $file) {
//         if (file_exists($file)) {
//             unlink($file);
//         }
//     }
//     respond(400, "error", "No files processed successfully.");
// }

// // Now create a single parts array with all files and the prompt
// // echo json_encode($combinedParts);
// // exit;
// // $parts = [
// //     [
// //         'text' => "I'm sending you multiple files. Please process them together as a single request.\n\n" .
// //                   "Files:\n" . implode("\n", array_map(function($part) {
// //                       return $part['text'];
// //                   }, $combinedParts)) . "\n\n" .
// //                   "Instructions:\n" .
// //                   "1. Extract all text content from each uploaded file\n" .
// //                   "2. Return the results as a JSON array with one element per file\n" .
// //                   "3. Format: [\"extracted text here\", \"extracted text here\"]\n" .
// //                   "4. Preserve all text including headers, paragraphs, lists, and tables\n" .
// //                   "5. Maintain original formatting where possible\n" .
// //                   "6. If a file cannot be processed, include it with \"error\" field explaining the issue"
// //     ]
// // ];

// // $parts = [
// //     [
// //         'text' => "I'm sending you a file. Please process it as a single complete document.\n\n" .
// //                   "Instructions:\n" .
// //                   "1. Extract ALL text content in ONE piece\n" .
// //                   "2. Do NOT split the content into parts\n" .
// //                   "3. Return the complete text as a single string\n" .
// //                   "4. Preserve all formatting exactly as in the original"
// //     ]
// // ];

// $parts = [
//     [
//         'text' => "Process these files with STRICT rules:
// 1. ONE array element per COMPLETE file
// 2. NEVER split content within a file
// 3. NO 'continued' markers or duplicates
// 4. Format EXACTLY like this:
// ```json
// [
//     \"Full content from file 1\",
//     \"Full content from file 2\" 
// ]"]];

// // $parts = [
// //     [
// //         'text' => "I'm sending you multiple files to process. For each file:
        
// // 1. Extract ALL text content as a SINGLE COMPLETE STRING
// // 2. Do NOT split content within a file - keep each file's content whole
// // 3. Return as a JSON array where:
// //    - Each element corresponds to one input file
// //    - Each element contains the complete extracted text from that file
// // 4. Format exactly like this example:
// // ```json
// // [
// //     \"Full text content from file 1\",
// //     \"Full text content from file 2\",
// //     \"Full text content from file 3\"
// // ]"]];

// // Add the actual file contents as inline data
// foreach ($_FILES['files']['tmp_name'] as $index => $tmpPath) {
//     if ($_FILES['files']['error'][$index] === UPLOAD_ERR_OK) {
//         $fileContent = file_get_contents($storedFiles[$index]);
//         $base64Content = base64_encode($fileContent);
        
//         $parts[] = [
//             'inlineData' => [
//                 'mimeType' => finfo_file(finfo_open(FILEINFO_MIME_TYPE), $storedFiles[$index]),
//                 'data' => $base64Content
//             ]
//         ];
//     }
// }

// $payload = json_encode([
//     'contents' => [
//         [
//             'role' => 'user',
//             'parts' => $parts
//         ]
//     ],
//     'generationConfig' => [
//         'temperature' => 0.1,
//         // 'maxOutputTokens' => 8192
//     ]
// ]);

// if (json_last_error() !== JSON_ERROR_NONE) {
//     respond(500, "error", "Invalid request payload");
// }

// $ch = curl_init();
// curl_setopt_array($ch, [
//     CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$API_KEY",
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_POST => true,
//     CURLOPT_HTTPHEADER => [
//         'Content-Type: application/json'
//     ],
//     CURLOPT_POSTFIELDS => $payload,
//     CURLOPT_TIMEOUT => 120
// ]);

// $response = curl_exec($ch);
// $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// $curlError = curl_error($ch);
// curl_close($ch);

// if ($curlError) {
//     respond(500, "error", "Network error occurred");
// }

// if ($httpCode >= 200 && $httpCode < 300) {
//     $parsed = json_decode($response, true);
//     if (json_last_error() !== JSON_ERROR_NONE) {
//         respond(500, "error", "Invalid response from API");
//     }

//     $text = $parsed['candidates'][0]['content']['parts'][0]['text'] ?? null;

//     if ($text) {
//         echo $text;
//         exit;
//         $cleanedText = preg_replace('/^```(?:json)?\n([\s\S]*?)\n```$/', '$1', $text);

//         $json = json_decode($cleanedText, true);

//         $userId = get_authenticated_user_id();
//         $user = get_user_by_id($userId);
//         if (!$user->get_can_upload() && $user->getRole() != "manager") {
//             respond(403, "error", "You do not have permission to perform this action.");
//         }
//         $department = $user->getDepartment();

//         echo $cleanedText;
//         exit;
//         create_chunks(json_decode($cleanedText), $department, $fileSizes, $fileNames, $fileTypes);
//         // if (json_last_error() === JSON_ERROR_NONE) {
//         //     create_chunks($json, $department, $fileSizes, $fileNames, $fileTypes);
//         // } else {
//         //     create_chunks([json_decode($cleanedText)], $department, $fileSizes, $fileNames, $fileTypes);
//         // }
//     } else {
//         respond(500, "error", "API response format unexpected");
//     }
// } else {
//     $error = json_decode($response, true);
//     $message = $error['error']['message'] ?? "Unknown error from API";
//     respond($httpCode, "error", $message);
// }

