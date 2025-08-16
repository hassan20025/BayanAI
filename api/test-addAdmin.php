<?php
// Test what addAdmin.php returns
$url = 'http://localhost/bayanAI/api/addAdmin.php';

$data = [
    'name' => 'Test Admin',
    'email' => 'test@example.com'
];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Raw response:\n";
echo $result;
echo "\n\nResponse length: " . strlen($result);
?> 