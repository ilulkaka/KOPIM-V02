<?php
$secret = "RAHASIA_WEBHOOK";

// Ambil signature dari GitHub
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

// Validasi signature
if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    exit('Invalid signature');
}

// Jalankan git pull
$output = shell_exec('cd /home/ilulkaka/public_html/KOPIM-V02 && git pull origin main 2>&1');
echo "<pre>$output</pre>";