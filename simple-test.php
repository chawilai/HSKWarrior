<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

// Create a simple test without TestCase
$request = Illuminate\Http\Request::create('/chinese_words?level=HSK 1', 'GET');
$response = $app->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Content Type: " . $response->headers->get('content-type') . "\n";

if ($response->getStatusCode() === 200) {
    echo "✅ Chinese words route is working!\n";
} else {
    echo "❌ Chinese words route failed with status: " . $response->getStatusCode() . "\n";
}

// Test basic route
$request2 = Illuminate\Http\Request::create('/', 'GET');
$response2 = $app->handle($request2);

echo "\nHome route status: " . $response2->getStatusCode() . "\n";

if ($response2->getStatusCode() === 200) {
    echo "✅ Home route is working!\n";
} else {
    echo "❌ Home route failed with status: " . $response2->getStatusCode() . "\n";
}

echo "\n🎯 Basic route testing completed!\n";