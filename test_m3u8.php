<?php
$url = "https://s3.bfllvip.com/video/qingyuniandiyiji/737c2ec959ce/index.m3u8";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Content length: " . strlen($result) . "\n";
echo "\n=== First 1000 chars ===\n";
echo substr($result, 0, 1000) . "\n";
