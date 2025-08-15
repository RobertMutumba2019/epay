<?php
/**
 * T119 – Query Taxpayer Information by TIN or NIN/BRN
 * Fill the ENCRYPTION + SIGNATURE placeholders with your T104 outputs.
 */

function uuidv4(): string {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
}

/** ====== CRYPTO PLACEHOLDERS – replace with your real implementations ====== */
function encryptContent(string $plainJson): string {
    // Replace with the algorithm/key negotiated via T104.
    return base64_encode($plainJson); // placeholder: NO real encryption
}

function signContent(string $cipherB64): string {
    // Replace with your real signing per your T104 config.
    return hash('sha256', $cipherB64); // placeholder
}
/** ======================================================================== */

function buildEnvelope(array $inner, array $globalOverrides = []): array {
    $now = (new DateTime('now', new DateTimeZone('Africa/Kampala')))->format('Y-m-d H:i:s');

    $contentJson = json_encode($inner, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $cipherB64   = encryptContent($contentJson);
    $signature   = signContent($cipherB64);

    $globalInfo = array_merge([
        "appId"          => "AP01",
        "version"        => "1.1.20191201",
        "dataExchangeId" => uuidv4(),
        "interfaceCode"  => "T119",
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

function queryTaxpayer(?string $tin, ?string $ninBrn, string $baseUrl, array $globalOverrides = []): array {
    if ((empty($tin) && empty($ninBrn))) {
        return ['ok' => false, 'data' => null, 'error' => "Validation: tin and ninBrn cannot both be empty"];
    }
    if ($tin !== null && strlen($tin) > 20)   return ['ok'=>false,'data'=>null,'error'=>"Validation: tin too long"];
    if ($ninBrn !== null && strlen($ninBrn) > 20) return ['ok'=>false,'data'=>null,'error'=>"Validation: ninBrn too long"];

    $inner = [
        "tin"    => $tin ?? "",
        "ninBrn" => $ninBrn ?? ""
    ];
    $envelope = buildEnvelope($inner, $globalOverrides);

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

    $innerB64 = $resp['data']['content'] ?? null;
    $innerJson = $innerB64 ? base64_decode($innerB64) : null; // placeholder decryption
    $innerObj  = $innerJson ? json_decode($innerJson, true) : null;

    if (isset($innerObj['taxpayer'])) {
        return ['ok'=>true,'data'=>$innerObj['taxpayer'],'error'=>null];
    }

    $code = $resp['returnStateInfo']['returnCode'] ?? null;
    $msg  = $resp['returnStateInfo']['returnMessage'] ?? 'Unknown error';
    return ['ok'=>false,'data'=>null,'error'=>"Server error $code: $msg"];
}

// ================= UI PART =================
$baseUrl = "https://api.ura.go.ug/efris/1.0";
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tin    = trim($_POST['tin'] ?? '');
    $ninBrn = trim($_POST['ninBrn'] ?? '');
    $result = queryTaxpayer($tin ?: null, $ninBrn ?: null, $baseUrl);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>T119 – Query Taxpayer Info</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 500px; background: white; padding: 20px; border-radius: 8px; margin: auto; }
        h2 { text-align: center; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input[type="text"] { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007BFF; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; width: 100%; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; background: #eef; padding: 10px; border-radius: 4px; }
        .error { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 5px; border-bottom: 1px solid #ddd; }
        td:first-child { font-weight: bold; background: #f9f9f9; width: 40%; }
    </style>
</head>
<body>

<div class="container">
    <h2>T119 – Query Taxpayer Information</h2>
    <form method="POST">
        <label for="tin">TIN</label>
        <input type="text" id="tin" name="tin" value="<?= htmlspecialchars($_POST['tin'] ?? '') ?>">

        <label for="ninBrn">NIN/BRN</label>
        <input type="text" id="ninBrn" name="ninBrn" value="<?= htmlspecialchars($_POST['ninBrn'] ?? '') ?>">

        <button type="submit">Search</button>
    </form>

    <?php if ($result): ?>
        <div class="result">
            <?php if ($result['ok']): ?>
                <h3>Taxpayer Details</h3>
                <table>
                    <?php foreach ($result['data'] as $field => $value): ?>
                        <tr>
                            <td><?= htmlspecialchars($field) ?></td>
                            <td><?= htmlspecialchars($value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <div class="error">Error: <?= htmlspecialchars($result['error']) ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
