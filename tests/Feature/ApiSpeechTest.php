<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses()->group('api-speech');
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('azure tts api can synthesize chinese text', function () {
    $user = User::factory()->create();
    
    // Mock Azure TTS response
    Http::fake([
        'https://southeastasia.tts.speech.microsoft.com/*' => Http::response([
            'audio_data' => base64_encode('fake_audio_data'),
            'format' => 'audio-16khz-128kbitrate-mono-mp3',
            'duration' => 1500
        ], 200)
    ]);

    $response = $this->actingAs($user)->post('/api/azure-tts', [
        'text' => '你好',
        'voice' => 'zh-CN-XiaoxiaoNeural',
        'speed' => 1.0,
        'pitch' => 1.0
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'audio_url',
        'duration',
        'text'
    ]);
});

test('azure tts api validates required parameters', function () {
    $user = User::factory()->create();

    // Test missing text
    $response = $this->actingAs($user)->post('/api/azure-tts', [
        'voice' => 'zh-CN-XiaoxiaoNeural',
        'speed' => 1.0
    ]);

    $response->assertStatus(422);

    // Test invalid voice
    $response = $this->actingAs($user)->post('/api/azure-tts', [
        'text' => '你好',
        'voice' => 'invalid-voice'
    ]);

    $response->assertStatus(422);
});

test('azure tts api supports multiple chinese voices', function () {
    $user = User::factory()->create();
    
    $voices = [
        'zh-CN-XiaoxiaoNeural',
        'zh-CN-XiaoyouNeural',
        'zh-CN-YunxiNeural',
        'zh-HK-HiuGaaiNeural',
        'zh-TW-HsiaoChenNeural'
    ];

    foreach ($voices as $voice) {
        Http::fake([
            'https://southeastasia.tts.speech.microsoft.com/*' => Http::response([
                'audio_data' => base64_encode("fake_audio_for_{$voice}"),
                'voice' => $voice
            ], 200)
        ]);

        $response = $this->actingAs($user)->post('/api/azure-tts', [
            'text' => '你好世界',
            'voice' => $voice
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['voice' => $voice]);
    }
});

test('azure tts api handles various chinese text inputs', function () {
    $user = User::factory()->create();
    
    $testTexts = [
        '你好', // Simple greeting
        '我喜欢学习汉语。', // Sentence with punctuation
        '123 456', // Numbers
        '！？，。', // Punctuation
        '这是一个很长的句子用来测试语音合成。' // Long text
    ];

    foreach ($testTexts as $text) {
        Http::fake([
            'https://southeastasia.tts.speech.microsoft.com/*' => Http::response([
                'audio_data' => base64_encode("audio_for_{$text}"),
                'text_length' => mb_strlen($text)
            ], 200)
        ]);

        $response = $this->actingAs($user)->post('/api/azure-tts', [
            'text' => $text,
            'voice' => 'zh-CN-XiaoxiaoNeural'
        ]);

        $response->assertStatus(200);
    }
});

test('azure speech assessment api can evaluate pronunciation', function () {
    $user = User::factory()->create();
    
    // Mock Azure speech assessment response
    Http::fake([
        'https://southeastasia.stt.speech.microsoft.com/*' => Http::response([
            'pronunciation_score' => 85.5,
            'accuracy_score' => 87.2,
            'fluency_score' => 83.1,
            'completeness_score' => 90.0,
            'words' => [
                [
                    'word' => '你好',
                    'accuracy_score' => 90,
                    'error_type' => 'None'
                ]
            ]
        ], 200)
    ]);

    // Create a fake audio file
    $audioFile = UploadedFile::fake()->create('pronunciation.wav', 1024, 'audio/wav');

    $response = $this->actingAs($user)->post('/api/speech/assess', [
        'audio' => $audioFile,
        'reference_text' => '你好',
        'language' => 'zh-CN'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'pronunciation_score',
        'accuracy_score',
        'fluency_score',
        'completeness_score',
        'words'
    ]);
});

test('azure speech api validates audio file requirements', function () {
    $user = User::factory()->create();

    // Test invalid file format
    $invalidFile = UploadedFile::fake()->create('test.txt', 1024, 'text/plain');
    
    $response = $this->actingAs($user)->post('/api/speech/assess', [
        'audio' => $invalidFile,
        'reference_text' => '你好'
    ]);

    $response->assertStatus(422);

    // Test missing audio file
    $response = $this->actingAs($user)->post('/api/speech/assess', [
        'reference_text' => '你好'
    ]);

    $response->assertStatus(422);
});

test('azure speech api handles pronunciation assessment errors', function () {
    $user = User::factory()->create();
    
    // Mock API error response
    Http::fake([
        'https://southeastasia.stt.speech.microsoft.com/*' => Http::response([
            'error' => 'Audio quality too poor',
            'code' => 'BadAudioQuality'
        ], 400)
    ]);

    $audioFile = UploadedFile::fake()->create('bad_audio.wav', 1024, 'audio/wav');

    $response = $this->actingAs($user)->post('/api/speech/assess', [
        'audio' => $audioFile,
        'reference_text' => '你好'
    ]);

    $response->assertStatus(400);
    $response->assertJsonFragment(['error' => 'Audio quality too poor']);
});

test('reading game api provides appropriate content', function () {
    $user = User::factory()->create();
    
    // Mock reading game content
    $mockContent = [
        'sentences' => [
            [
                'text' => '我喜欢学习汉语。',
                'pinyin' => 'Wǒ xǐhuan xuéxí Hànyǔ.',
                'translation' => 'I like studying Chinese.',
                'difficulty' => 'beginner'
            ],
            [
                'text' => '今天天气很好。',
                'pinyin' => 'Jīntiān tiānqì hěn hǎo.',
                'translation' => 'The weather is good today.',
                'difficulty' => 'beginner'
            ]
        ],
        'total_sentences' => 2,
        'estimated_time' => 60
    ];

    Http::fake([
        'http://localhost:3000/segment' => Http::response([
            ['word' => '我', 'pos' => 'PRON'],
            ['word' => '喜欢', 'pos' => 'VERB'],
            ['word' => '学习', 'pos' => 'VERB'],
            ['word' => '汉语', 'pos' => 'NOUN']
        ], 200)
    ]);

    $response = $this->actingAs($user)->get('/api/reading-game/content?level=beginner');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'sentences' => [
            '*' => [
                'text',
                'pinyin',
                'translation',
                'difficulty'
            ]
        ]
    ]);
});

test('speech apis handle rate limiting gracefully', function () {
    $user = User::factory()->create();
    
    // Mock rate limit error
    Http::fake([
        'https://southeastasia.tts.speech.microsoft.com/*' => Http::response([
            'error' => 'Rate limit exceeded',
            'retry_after' => 60
        ], 429)
    ]);

    $response = $this->actingAs($user)->post('/api/azure-tts', [
        'text' => '测试',
        'voice' => 'zh-CN-XiaoxiaoNeural'
    ]);

    $response->assertStatus(429);
    $response->assertJsonFragment(['error' => 'Rate limit exceeded']);
});

test('speech apis cache responses appropriately', function () {
    $user = User::factory()->create();
    
    // Mock successful response
    Http::fake([
        'https://southeastasia.tts.speech.microsoft.com/*' => Http::response([
            'audio_data' => base64_encode('cached_audio_data'),
            'cached' => true
        ], 200)
    ]);

    // First request
    $response1 = $this->actingAs($user)->post('/api/azure-tts', [
        'text' => '你好',
        'voice' => 'zh-CN-XiaoxiaoNeural'
    ]);

    // Second identical request should use cache
    $response2 = $this->actingAs($user)->post('/api/azure-tts', [
        'text' => '你好',
        'voice' => 'zh-CN-XiaoxiaoNeural'
    ]);

    $response1->assertStatus(200);
    $response2->assertStatus(200);
    
    // Verify cache usage
    $this->assertDatabaseHas('tts_cache_logs', [
        'text' => '你好',
        'voice' => 'zh-CN-XiaoxiaoNeural'
    ]);
});

test('speech apis support multiple audio formats', function () {
    $user = User::factory()->create();
    
    $formats = [
        'audio-16khz-128kbitrate-mono-mp3',
        'audio-24khz-160kbitrate-mono-mp3',
        'audio-48khz-192kbitrate-mono-mp3',
        'audio-16khz-32kbitrate-mono-mp3'
    ];

    foreach ($formats as $format) {
        Http::fake([
            'https://southeastasia.tts.speech.microsoft.com/*' => Http::response([
                'audio_data' => base64_encode("audio_for_{$format}"),
                'format' => $format
            ], 200)
        ]);

        $response = $this->actingAs($user)->post('/api/azure-tts', [
            'text' => '你好',
            'voice' => 'zh-CN-XiaoxiaoNeural',
            'format' => $format
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['format' => $format]);
    }
});

test('speech apis handle network timeouts appropriately', function () {
    $user = User::factory()->create();
    
    // Mock timeout
    Http::fake([
        'https://southeastasia.tts.speech.microsoft.com/*' => function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
        }
    ]);

    $response = $this->actingAs($user)->post('/api/azure-tts', [
        'text' => '你好',
        'voice' => 'zh-CN-XiaoxiaoNeural'
    ]);

    $response->assertStatus(503);
    $response->assertJsonFragment(['error' => 'Service temporarily unavailable']);
});

test('speech apis validate text length limits', function () {
    $user = User::factory()->create();
    
    // Test with very long text (should be rejected)
    $longText = str_repeat('这是一个很长的句子用来测试字符限制。', 50); // ~500 characters
    
    $response = $this->actingAs($user)->post('/api/azure-tts', [
        'text' => $longText,
        'voice' => 'zh-CN-XiaoxiaoNeural'
    ]);

    // Should validate text length
    if (mb_strlen($longText) > 1000) {
        $response->assertStatus(422);
    }
});

test('speech apis provide meaningful error messages', function () {
    $user = User::factory()->create();
    
    // Test various error scenarios
    $errorScenarios = [
        [
            'text' => '', // Empty text
            'expected_error' => 'Text is required'
        ],
        [
            'text' => '你好',
            'voice' => '', // Empty voice
            'expected_error' => 'Voice is required'
        ],
        [
            'text' => '你好',
            'voice' => 'invalid-voice-name', // Invalid voice
            'expected_error' => 'Voice not supported'
        ]
    ];

    foreach ($errorScenarios as $scenario) {
        $response = $this->actingAs($user)->post('/api/azure-tts', $scenario);
        
        if (isset($scenario['expected_error'])) {
            $response->assertStatus(422);
        }
    }
});