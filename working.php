<?php

$base_url = "https://switchapidev.centenarybank.co.ug"; 
$username = "sunsys"; 
$password = "C0mpl3x@Sun="; 



// ===== FUNCTION TO GET TOKENS =====
function getTokens($base_url, $username, $password) {
    $url = $base_url . "/api/token/";

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
        die("cURL Error: " . curl_error($ch));
    }
    curl_close($ch);

    return json_decode($response, true);
}

// ===== FUNCTION TO GET FOREX RATES =====
function getForexRates($base_url, $access_token, $aux_no, $resp_type = "json") {
    $url = $base_url . "/forex_rates";

    $payload = json_encode([
        "AUX_NO" => $aux_no,
        "RESP_TYPE" => $resp_type
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $access_token
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("cURL Error: " . curl_error($ch));
    }
    curl_close($ch);

    return json_decode($response, true);
}

// ===== USAGE =====
$tokens = getTokens($base_url, $username, $password);

if (!empty($tokens['access'])) {
    echo "Access Token: " . $tokens['access'] . PHP_EOL;

    // Example AUX_NO — should be unique for each request
    $aux_no = "KLFLKFKLF"; 
    $forex_rates = getForexRates($base_url, $tokens['access'], $aux_no);

    echo "Forex Rates:\n";
    print_r($forex_rates);
} else {
    echo "Failed to get tokens:\n";
    print_r($tokens);
}
