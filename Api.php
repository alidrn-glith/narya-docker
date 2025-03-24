<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_message = $data['message'] ?? null;
$bot_name = $data['bot_name'] ?? 'دستیار فروشگاه';

if (!$user_message) {
    echo json_encode(['error' => 'Missing user message.']);
    exit;
}

$api_key = getenv('OPENAI_KEY');
if (!$api_key) {
    echo json_encode(['error' => 'Missing OpenAI API Key.']);
    exit;
}

$payload = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "شما یک ربات پاسخگوی فروشگاه هستید. مودب و کمک‌کننده باشید."],
        ["role" => "user", "content" => $user_message]
    ]
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $api_key"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(['error' => 'cURL Error: ' . $error]);
    exit;
}

echo $response;
