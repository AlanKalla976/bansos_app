<?php
header('Content-Type: text/plain');
$url = 'http://localhost/bansos-app/public/storage/ktp/ktp_5.jpg';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in the output
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP CODE: " . $http_code . "\n";
echo "Response Length: " . strlen($response) . "\n";

// Let's parse and print only the header part
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $header_size);
echo "Headers:\n" . $headers . "\n";
?>
