<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_message = $data['message'] ?? null;
$bot_name = $data['bot_name'] ?? 'دستیار فروشگاه';

if (!$user_message) {
    echo json_encode(['error' => 'Missing user message']);
    exit;
}

$api_key = getenv('OPENAI_KEY');

$payload = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "شما یک دستیار فارسی به نام {$bot_name} هستید و به سوالات کاربران فروشگاه پاسخ می‌دهید."],
        ["role" => "user", "content" => $user_message]
    ],
    "temperature" => 0.7
];

$response = send_to_gpt($payload, $api_key);
echo json_encode(['reply' => $response]);
exit;

function send_to_gpt($payload, $api_key) {
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        return 'خطای اتصال: ' . curl_error($ch);
    }

    curl_close($ch);
    $result = json_decode($result, true);
    return $result['choices'][0]['message']['content'] ?? 'پاسخی دریافت نشد.';
}
