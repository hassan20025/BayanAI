<?php
header("Content-Type: application/json"); 
    // header("Access-Control-Allow-Origin: *");

    // $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    // $allowed_origins = [
    //     'http://localhost',
    //     'http://localhost'
    // ];

    // if (in_array($origin, $allowed_origins)) {
    //     header("Access-Control-Allow-Origin: $origin");
    //     header("Access-Control-Allow-Credentials: true");
    // }

    // header("Access-Control-Allow-Methods: *");

    // header("Access-Control-Allow-Headers: *");
    // header('Access-Control-Max-Age: 86400');


require_once __DIR__ . "/../vendor/autoload.php";
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

function cosineSimilarity(array $vec1, array $vec2): float {
    if (count($vec1) !== count($vec2)) {
        throw new InvalidArgumentException("Vectors must be of the same length.");
    }

    $dotProduct = 0;
    $normA = 0;
    $normB = 0;

    for ($i = 0; $i < count($vec1); $i++) {
        $dotProduct += $vec1[$i] * $vec2[$i];
        $normA += pow($vec1[$i], 2);
        $normB += pow($vec2[$i], 2);
    }

    if ($normA == 0 || $normB == 0) {
        return 0.0;
    }

    return $dotProduct / (sqrt($normA) * sqrt($normB));
}
