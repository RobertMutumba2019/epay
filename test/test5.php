<?php


$BASE_URL  = "https://switchapidev.centenarybank.co.ug";
$USERNAME  = "sunsys";
$PASSWORD  = "C0mpl3x@Sun=";

// Token endpoint (check spec: could be /token/ or /api/token/)
$TOKEN_URL = $BASE_URL . "/api/token/";  

// ==== FUNCTION TO GET TOKENS ====
function getAccessToken(string $url, string $username, string $password): array {
    $payload = json_encode([
        "username" => $username,
        "password" => $password
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            "Content-Type: application/json",
            "Accept: application/json"
        ],
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_SSL_VERIFYPEER => true,   // set false only if dev cert issues
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        throw new RuntimeException("cURL Error: " . curl_error($ch));
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);
    if ($status !== 200 || !is_array($data)) {
        throw new RuntimeException("Token request failed (HTTP $status): " . $response);
    }

    return $data;
}

// ==== USAGE ====
try {
    $tokens = getAccessToken($TOKEN_URL, $USERNAME, $PASSWORD);

    echo "Access Token: " . $tokens['access_token'] . PHP_EOL;

    if (isset($tokens['refresh_token'])) {
        echo "Refresh Token: " . $tokens['refresh_token'] . PHP_EOL;
    }

    if (isset($tokens['expires_in'])) {
        echo "Expires In: " . $tokens['expires_in'] . " seconds" . PHP_EOL;
    }

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
