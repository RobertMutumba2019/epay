
<?php

// Token URL
$token_url = "https://switchapidev.centenarybank.co.ug/token/"; 

// Credentials (provided)
$username = "sunsys"; 
$password = "C0mpl3x@Sun="; 

// ===== FUNCTION TO GET TOKENS =====
function getTokens($url, $username, $password) {
    $payload = json_encode([
        "username" => $username,
        "password" => $password
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("cURL Error (getTokens): " . curl_error($ch));
    }
    curl_close($ch);

    return json_decode($response, true);
}

// ===== RUN TEST =====
$tokens = getTokens($token_url, $username, $password);

echo "Raw Token Response:\n";
print_r($tokens);

if (!empty($tokens['access'])) {
    echo "\n Access Token: " . $tokens['access'] . "\n";
}
