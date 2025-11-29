<?php

namespace Tests\Feature;

use App\Models\AiSentence;
use App\Services\AiChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class AiSentenceTest extends TestCase
{
    // use RefreshDatabase; // Don't use RefreshDatabase to avoid wiping user's data if not configured correctly

    public function test_generate_sentences_endpoint()
    {
        // Mock the service to avoid calling real OpenAI API
        $mockService = Mockery::mock(AiChatService::class);
        $mockService->shouldReceive('generateSentences')
            ->once()
            ->with('你好', 'HSK 1')
            ->andReturn([
                'sentences' => [
                    [
                        'sentence' => '你好吗？',
                        'pinyin' => 'Nǐ hǎo ma?',
                        'translation' => 'How are you?'
                    ],
                    [
                        'sentence' => '你好，我是大卫。',
                        'pinyin' => 'Nǐ hǎo, wǒ shì Dàwèi.',
                        'translation' => 'Hello, I am David.'
                    ]
                ]
            ]);

        $this->app->instance(AiChatService::class, $mockService);

        $response = $this->postJson('/api/ai-sentences/generate', [
            'word' => '你好',
            'level' => 'HSK 1'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'sentences' => [
                    '*' => ['word', 'sentence', 'pinyin', 'translation', 'hash']
                ]
            ]);

        // Verify database
        $this->assertDatabaseHas('ai_sentences', [
            'word' => '你好',
            'sentence' => '你好吗？'
        ]);
    }
    
    public function test_prevents_duplicates()
    {
         // Create existing sentence
         $hash = md5('你好吗？' . '|' . 'How are you?');
         AiSentence::create([
             'word' => '你好',
             'sentence' => '你好吗？',
             'pinyin' => 'Nǐ hǎo ma?',
             'translation' => 'How are you?',
             'hash' => $hash
         ]);
         
         $countBefore = AiSentence::count();
         
         // Mock service returning same sentence
         $mockService = Mockery::mock(AiChatService::class);
         $mockService->shouldReceive('generateSentences')
            ->once()
            ->andReturn([
                'sentences' => [
                    [
                        'sentence' => '你好吗？',
                        'pinyin' => 'Nǐ hǎo ma?',
                        'translation' => 'How are you?'
                    ]
                ]
            ]);
            
        $this->app->instance(AiChatService::class, $mockService);
        
        $response = $this->postJson('/api/ai-sentences/generate', [
            'word' => '你好',
            'level' => 'HSK 1'
        ]);
        
        $response->assertStatus(200);
        
        // Count should remain same (or +0)
        $this->assertEquals($countBefore, AiSentence::count());
    }
}
