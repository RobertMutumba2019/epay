<?php
$apiHost = "switchapidev.centenarybank.co.ug";
$apiPort = 443;
$apiURL  = "https://$apiHost/api/token/";

// ---- Test 1: fsockopen ----
echo "Testing TCP connection to $apiHost:$apiPort ...\n";
$fp = @fsockopen($apiHost, $apiPort, $errno, $errstr, 5);
if (!$fp) {
    echo "fsockopen failed: $errstr ($errno)\n\n";
} else {
    echo "fsockopen successful: TCP connection established!\n\n";
    fclose($fp);
}

// ---- Test 2: cURL ----
echo "Testing HTTP(S) request to $apiURL ...\n";
$ch = curl_init($apiURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // skip SSL check

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "cURL failed: " . curl_error($ch) . "\n";
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "cURL successful! HTTP Status: $httpCode\n";
    echo "Response (truncated to 200 chars): " . substr($response, 0, 200) . "\n";
}
curl_close($ch);
