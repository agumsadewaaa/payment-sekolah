<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::create('/get-tagihan-by-siswa/1', 'GET');
$response = $kernel->handle($request);

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Content:\n";
echo $response->getContent() . "\n";

// Try to parse and prettify
$json = json_decode($response->getContent(), true);
if ($json) {
    echo "\n\nParsed JSON:\n";
    echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
?>
