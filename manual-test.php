<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class SimpleTest extends TestCase
{
    use CreatesApplication;

    public function testBasicRoute()
    {
        $response = $this->get('/');
        $this->assertEquals(200, $response->getStatusCode());
        echo "✅ Basic route test passed\n";
    }

    public function testChineseWordsRoute()
    {
        $response = $this->get('/chinese_words?level=HSK 1');
        $this->assertEquals(200, $response->getStatusCode());
        echo "✅ Chinese words route test passed\n";
    }
}

// Run tests
$test = new SimpleTest();
$test->setUp();

try {
    $test->testBasicRoute();
    $test->testChineseWordsRoute();
    echo "\n🎉 All tests passed!\n";
} catch (Exception $e) {
    echo "\n❌ Test failed: " . $e->getMessage() . "\n";
}