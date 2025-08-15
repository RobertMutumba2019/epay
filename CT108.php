<?php
/**
 * T108 – Invoice Details by Invoice Number
 * Encryption + signing placeholders must be replaced with real logic from T104.
 */

function uuidv4(): string {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
}

function encryptContent(string $plainJson): string {
    return base64_encode($plainJson); // placeholder
}

function signContent(string $cipherB64): string {
    return hash('sha256', $cipherB64); // placeholder
}

function buildEnvelope(array $inner, string $interfaceCode, array $globalOverrides = []): array {
    $now = (new DateTime('now', new DateTimeZone('Africa/Kampala')))->format('Y-m-d H:i:s');

    $contentJson = json_encode($inner, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $cipherB64   = encryptContent($contentJson);
    $signature   = signContent($cipherB64);

    $globalInfo = array_merge([
        "appId"          => "AP01",
        "version"        => "1.1.20191201",
        "dataExchangeId" => uuidv4(),
        "interfaceCode"  => $interfaceCode,
        "requestCode"    => "TP",
        "requestTime"    => $now,
        "responseCode"   => "TA",
        "userName"       => "admin",
        "deviceMAC"      => "FFFFFFFFFFFF",
        "deviceNo"       => "00022000634",
        "tin"            => "1009830865",
        "brn"            => "",
        "taxpayerID"     => "1",
        "longitude"      => "0",
        "latitude"       => "0"
    ], $globalOverrides);

    return [
        "data" => [
            "content"  => $cipherB64,
            "signature"=> $signature,
            "dataDescription" => [
                "codeType"   => "0",
                "encryptCode"=> "1",
                "zipCode"    => "0"
            ]
        ],
        "globalInfo" => $globalInfo,
        "returnStateInfo" => new stdClass()
    ];
}

function queryInvoiceDetails(string $invoiceNo, string $baseUrl, array $globalOverrides = []): array {
    if (empty($invoiceNo)) {
        return ['ok'=>false,'data'=>null,'error'=>"Invoice number is required"];
    }
    if (strlen($invoiceNo) > 40) {
        return ['ok'=>false,'data'=>null,'error'=>"Invoice number too long"];
    }

    $inner = ["invoiceNo" => $invoiceNo];
    $envelope = buildEnvelope($inner, "T108", $globalOverrides);

    $url = rtrim($baseUrl, "/") . "/";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($envelope, JSON_UNESCAPED_UNICODE),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60
    ]);
    $raw = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($raw === false || $httpCode >= 400) {
        return ['ok'=>false,'data'=>null,'error'=>"HTTP error ($httpCode): $err / $raw"];
    }

    $resp = json_decode($raw, true);

    $innerB64  = $resp['data']['content'] ?? null;
    $innerJson = $innerB64 ? base64_decode($innerB64) : null; // placeholder
    $innerObj  = $innerJson ? json_decode($innerJson, true) : null;

    if (is_array($innerObj) && count($innerObj) > 0) {
        return ['ok'=>true,'data'=>$innerObj,'error'=>null];
    }

    $code = $resp['returnStateInfo']['returnCode'] ?? null;
    $msg  = $resp['returnStateInfo']['returnMessage'] ?? 'Unknown error';
    return ['ok'=>false,'data'=>null,'error'=>"Server error $code: $msg"];
}

// ================= UI PART =================
$baseUrl = "https://efris.ura.go.ug/efris-web/efrisService"; // Replace with your T103 webServiceURL
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoiceNo = trim($_POST['invoiceNo'] ?? '');
    $result = queryInvoiceDetails($invoiceNo, $baseUrl);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>T108 – Invoice Details</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; background: white; padding: 20px; border-radius: 8px; margin: auto; }
        h2 { text-align: center; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input[type="text"] { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007BFF; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; width: 100%; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; background: #eef; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .error { color: red; font-weight: bold; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>

<div class="container">
    <h2>T108 – Invoice Details</h2>
    <form method="POST">
        <label for="invoiceNo">Invoice Number</label>
        <input type="text" id="invoiceNo" name="invoiceNo" value="<?= htmlspecialchars($_POST['invoiceNo'] ?? '') ?>">

        <button type="submit">Search</button>
    </form>

    <?php if ($result): ?>
        <div class="result">
            <?php if ($result['ok']): ?>
                <h3>Invoice Data</h3>
                <pre><?= htmlspecialchars(json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
            <?php else: ?>
                <div class="error">Error: <?= htmlspecialchars($result['error']) ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
