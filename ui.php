<?php
// ===== CONFIGURATION =====
$base_url = "https://switchapidev.centenarybank.co.ug"; // Change to UAT or PROD as needed
$username = "your_username"; // replace with your actual username
$password = "your_password"; // replace with your actual password

// ===== FUNCTION TO GET TOKENS =====
function getTokens($base_url, $username, $password) {
    $url = $base_url . "/token/";

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
        return ["error" => "cURL Error: " . curl_error($ch)];
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
        return ["error" => "cURL Error: " . curl_error($ch)];
    }
    curl_close($ch);

    return json_decode($response, true);
}

// ===== USAGE =====
$tokens = getTokens($base_url, $username, $password);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forex Rates</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
        .loading {
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>Forex Rates</h1>
    <?php
    if (!empty($tokens['error'])) {
        echo "<p class='error'>Failed to get tokens: " . htmlspecialchars($tokens['error']) . "</p>";
    } elseif (!empty($tokens['access'])) {
        // Example AUX_NO — should be unique for each request
        $aux_no = "KLFLKFKLF";
        $forex_rates = getForexRates($base_url, $tokens['access'], $aux_no);

        if (!empty($forex_rates['error'])) {
            echo "<p class='error'>Error fetching forex rates: " . htmlspecialchars($forex_rates['error']) . "</p>";
        } elseif (!empty($forex_rates) && is_array($forex_rates)) {
            echo "<table>";
            echo "<tr><th>Currency</th><th>Buy Rate</th><th>Sell Rate</th></tr>";
            // Assuming forex_rates contains an array of rates
            foreach ($forex_rates as $rate) {
                if (isset($rate['currency'], $rate['buy_rate'], $rate['sell_rate'])) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($rate['currency']) . "</td>";
                    echo "<td>" . htmlspecialchars($rate['buy_rate']) . "</td>";
                    echo "<td>" . htmlspecialchars($rate['sell_rate']) . "</td>";
                    echo "</tr>";
                }
            }
            echo "</table>";
        } else {
            echo "<p class='error'>No forex rates available or unexpected response format.</p>";
        }
    } else {
        echo "<p class='error'>Failed to get tokens: Invalid response.</p>";
    }
    ?>
</body>
</html>