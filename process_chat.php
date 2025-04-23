<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

if (isset($_POST['message'])) {
    $user_message = trim($_POST['message']);
    // Save the user message in chat history
    $_SESSION['chat_history'][] = ['sender' => 'user', 'message' => $user_message];

    // Set up the Google Generative Language API endpoint with your API key
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $API_KEY;
    $payload = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $user_message]
                ]
            ]
        ]
    ];
    $json_payload = json_encode($payload);

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json_payload)
    ]);

    // Execute the API call
    $result = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Check for cURL errors
    if ($curl_error) {
        $ai_response = "Curl Error: " . $curl_error;
    } 
    // Check for non-200 HTTP response status
    elseif ($http_status !== 200) {
        $ai_response = "Error: API returned status code " . $http_status . ". Response: " . $result;
    } 
    else {
        $response = json_decode($result, true);
        // Extract the answer from the correct JSON structure
        if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            $ai_response = trim($response['candidates'][0]['content']['parts'][0]['text']);
        } else {
            $ai_response = "Error: Unexpected API response: " . $result;
        }
    }

    // Save the AI response in chat history
    $_SESSION['chat_history'][] = ['sender' => 'ai', 'message' => $ai_response];

    echo $ai_response;
} else {
    echo "No message received.";
}
?>
