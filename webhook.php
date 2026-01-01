<?php
$secret = 'RAHASIA_WEBHOOK';

// Ambil payload & signature
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    exit('Invalid signature');
}

// Lokasi project (SUBFOLDER)
$projectPath = '/home/u823236415/public_html/KOPIM-V2';

// Deploy
$cmd = "cd $projectPath && git pull origin main 2>&1";
$output = shell_exec($cmd);

echo "<pre>$output</pre>";