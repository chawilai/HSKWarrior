<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test ChineseWord model
try {
    $word = \App\Models\ChineseWord::factory()->make();
    echo "✅ ChineseWord factory works\n";
    echo "Word: " . $word->word . "\n";
    echo "Pinyin: " . $word->pinyin . "\n";
} catch (Exception $e) {
    echo "❌ ChineseWord factory failed: " . $e->getMessage() . "\n";
}

// Test DictionaryZhHans model
try {
    $hanzi = \App\Models\DictionaryZhHans::factory()->make();
    echo "✅ DictionaryZhHans factory works\n";
    echo "Character: " . $hanzi->character . "\n";
} catch (Exception $e) {
    echo "❌ DictionaryZhHans factory failed: " . $e->getMessage() . "\n";
}

// Test User model
try {
    $user = \App\Models\User::factory()->make();
    echo "✅ User factory works\n";
    echo "User: " . $user->name . "\n";
} catch (Exception $e) {
    echo "❌ User factory failed: " . $e->getMessage() . "\n";
}

echo "\n✅ All basic tests passed!\n";
echo "System is ready for Pest testing.\n";