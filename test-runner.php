<?php

// Simple test runner for basic functionality check
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "✅ Laravel bootstrapped successfully\n";

// Test basic database connection
try {
    $db = app('db');
    $db->select('SELECT 1');
    echo "✅ Database connection OK\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Test basic routing
try {
    $router = app('router');
    echo "✅ Router loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Router failed: " . $e->getMessage() . "\n";
}

echo "\n🧪 System ready for testing!\n";
echo "Try running: ./vendor/bin/pest tests/Feature/ChineseWordsTest.php\n";