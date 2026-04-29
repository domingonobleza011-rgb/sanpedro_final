<?php
session_start();
header('Content-Type: application/json');

// Pro-tip: Create a new key soon since this one was shared!
$apiKey = "AIzaSyCCbxJcpm2qoPO48qUAeY0H-0wjAheyrQ8";

$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite-preview:generateContent?key=" . $apiKey;

// Get the user's message
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['error' => 'No message provided']);
    exit;
}

// 1. Setup System Instructions
$systemContext = "You are the official  Assistant for Barangay San Pedro. 
Your goal is to guide residents through these specific services:
- Barangay ID: Needs Full name, Birthdate, Proof of Residency.
- Cert. of Indigency: Needs Purpose and Resident Profile.
- Blotter/Complaints: Needs incident details and parties involved.
- Youth Profiling: For residents aged 15-30[cite: 122, 128].
If a resident is not yet registered, advise them to complete their Resident Profile first[cite: 181].";

// 2. Prepare the Request Body
$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $systemContext . "\n\nUser Question: " . $userMessage]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7,
        "maxOutputTokens" => 500
    ]
];

// 3. Send Request via cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// --- SSL FIX FOR LOCAL DEVELOPMENT (LARAGON) ---
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
// -----------------------------------------------

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['reply' => 'cURL Error: ' . $err]);
} else {
    $result = json_decode($response, true);
    
    // Check if Google sent an error message instead of a reply
    if (isset($result['error'])) {
        echo json_encode(['reply' => 'Google API Error: ' . $result['error']['message']]);
    } else {
        // The path to the text response in the JSON
        $botReply = $result['candidates'][0]['content']['parts'][0]['text'] ?? "I'm having trouble thinking right now.";
        echo json_encode(['reply' => $botReply]);
    }
}