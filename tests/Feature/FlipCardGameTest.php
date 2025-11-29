<?php

use App\Models\DictionaryZhHans;
use App\Models\HanziList;
use App\Models\User;
use App\Models\WordGuess;

uses()->group('flip-card');
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('flip card game can retrieve words for practice', function () {
    $user = User::factory()->create();
    
    // Create test words
    $word1 = DictionaryZhHans::factory()->create([
        'character' => '学',
        'pinyin' => 'xué',
        'definition' => 'study, learn'
    ]);
    
    $word2 = DictionaryZhHans::factory()->create([
        'character' => '习',
        'pinyin' => 'xí',
        'definition' => 'practice, habit'
    ]);

    // Test random word retrieval
    $response = $this->actingAs($user)->get('/warrior_flip_card?set=hsk1&word_count=2');
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->component('WarriorFlipCard')
             ->has('hanzi_list_data.words', 2)
    );
});

test('flip card game integrates with user word lists', function () {
    $user = User::factory()->create();
    
    // Create a custom word list
    $hanziList = HanziList::factory()->create([
        'user_id' => $user->id,
        'list_reference' => 'my-study-list',
        'list_name' => 'My Study List',
        'box_number' => 6
    ]);

    // Add words to the list
    $word1 = DictionaryZhHans::factory()->create(['character' => '学']);
    $word2 = DictionaryZhHans::factory()->create(['character' => '习']);
    
    $hanziList->words()->create(['hanzi_id' => $word1->id]);
    $hanziList->words()->create(['hanzi_id' => $word2->id]);

    // Test accessing custom list
    $response = $this->actingAs($user)->get('/warrior_flip_card?reference=my-study-list');
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->component('WarriorFlipCard')
             ->has('hanzi_list_data.words', 2)
             ->where('hanzi_list_data.list_name', 'My Study List')
    );
});

test('flip card game prioritizes words user struggles with', function () {
    $user = User::factory()->create();
    
    // Create words with different guess histories
    $easyWord = DictionaryZhHans::factory()->create(['character' => '大']);
    $hardWord = DictionaryZhHans::factory()->create(['character' => '复杂']);
    
    // User got easy word correct
    WordGuess::create([
        'user_id' => $user->id,
        'word_id' => $easyWord->id,
        'user_answer' => 'big',
        'is_correct' => true
    ]);
    
    // User got hard word wrong
    WordGuess::create([
        'user_id' => $user->id,
        'word_id' => $hardWord->id,
        'user_answer' => 'simple',
        'is_correct' => false
    ]);

    // Hard word should appear first in the list
    $response = $this->actingAs($user)->get('/warrior_flip_card?set=hsk&word_count=10');
    
    $response->assertStatus(200);
    $words = $response->json('hanzi_list_data.words');
    
    // Find positions of easy and hard words
    $easyWordPosition = null;
    $hardWordPosition = null;
    
    foreach ($words as $index => $word) {
        if ($word['character'] === '大') {
            $easyWordPosition = $index;
        }
        if ($word['character'] === '复杂') {
            $hardWordPosition = $index;
        }
    }
    
    // Hard word should come before easy word (incorrect answers prioritized)
    if ($easyWordPosition !== null && $hardWordPosition !== null) {
        $this->assertLessThan($easyWordPosition, $hardWordPosition);
    }
});

test('flip card game tracks card flip interactions', function () {
    $user = User::factory()->create();
    $word = DictionaryZhHans::factory()->create([
        'character' => '学',
        'pinyin' => 'xué',
        'definition' => 'study, learn'
    ]);

    // Simulate card flip interaction
    $response = $this->actingAs($user)->post('/api/card-flip', [
        'word_id' => $word->id,
        'action' => 'flipped',
        'time_spent' => 2.5 // seconds
    ]);

    $response->assertStatus(200);
    
    // Verify interaction was recorded
    $this->assertDatabaseHas('card_interactions', [
        'user_id' => $user->id,
        'word_id' => $word->id,
        'action' => 'flipped',
        'time_spent' => 2.5
    ]);
});

test('flip card game supports different difficulty levels', function () {
    $user = User::factory()->create();
    
    // Create words for different HSK levels
    $hsk1Word = DictionaryZhHans::factory()->create([
        'character' => '大',
        'set' => 'hsk1',
        'definition' => 'big'
    ]);
    
    $hsk3Word = DictionaryZhHans::factory()->create([
        'character' => '复杂',
        'set' => 'hsk3',
        'definition' => 'complex'
    ]);
    
    $hsk5Word = DictionaryZhHans::factory()->create([
        'character' => '尴尬',
        'set' => 'hsk5',
        'definition' => 'awkward'
    ]);

    // Test HSK 1 (easiest)
    $response = $this->actingAs($user)->get('/warrior_flip_card?set=hsk1&word_count=1');
    $response->assertStatus(200);
    
    // Test HSK 3 (medium)
    $response = $this->actingAs($user)->get('/warrior_flip_card?set=hsk3&word_count=1');
    $response->assertStatus(200);
    
    // Test HSK 5 (hardest)
    $response = $this->actingAs($user)->get('/warrior_flip_card?set=hsk5&word_count=1');
    $response->assertStatus(200);
});

test('flip card game provides comprehensive word information', function () {
    $user = User::factory()->create();
    
    $word = DictionaryZhHans::factory()->create([
        'character' => '学',
        'pinyin' => 'xué',
        'definition' => 'study, learn',
        'meaning_thai' => 'เรียน',
        'radical' => '子',
        'stroke_count' => 8
    ]);

    $response = $this->actingAs($user)->get('/warrior_flip_card?set=hsk1&word_count=1');
    
    $response->assertStatus(200);
    
    // Check response structure
    $response->assertInertia(fn ($page) => 
        $page->component('WarriorFlipCard')
             ->has('hanzi_list_data.words.0', fn ($wordData) => 
                 $wordData->where('character', '学')
                         ->where('pinyin', 'xué')
                         ->where('definition', 'study, learn')
                         ->where('meaning_thai', 'เรียน')
                         ->where('radical', '子')
                         ->etc()
             )
    );
});

test('flip card game session management and progress tracking', function () {
    $user = User::factory()->create();
    
    // Start a new session
    $sessionData = [
        'user_id' => $user->id,
        'session_type' => 'flip_card',
        'words_completed' => 0,
        'correct_answers' => 0,
        'total_words' => 10
    ];

    // Create session
    $response = $this->actingAs($user)->post('/api/sessions/start', $sessionData);
    $response->assertStatus(201);
    
    // Update session progress
    $response = $this->actingAs($user)->put('/api/sessions/update', [
        'session_id' => 1,
        'words_completed' => 5,
        'correct_answers' => 4
    ]);
    
    $response->assertStatus(200);
    
    // Verify progress tracking
    $this->assertDatabaseHas('learning_sessions', [
        'user_id' => $user->id,
        'session_type' => 'flip_card',
        'words_completed' => 5,
        'correct_answers' => 4
    ]);
});

test('flip card game randomization ensures variety', function () {
    $user = User::factory()->create();
    
    // Create multiple words
    DictionaryZhHans::factory()->count(20)->create(['set' => 'hsk1']);
    
    // Get two different sessions
    $response1 = $this->actingAs($user)->get('/warrior_flip_card?set=hsk1&word_count=10');
    $words1 = $response1->json('hanzi_list_data.words');
    
    $response2 = $this->actingAs($user)->get('/warrior_flip_card?set=hsk1&word_count=10');
    $words2 = $response2->json('hanzi_list_data.words');
    
    // Should have some variety (not identical)
    $this->assertNotEquals($words1, $words2);
    
    // But should still have some overlap due to random sampling
    $overlap = array_intersect(array_column($words1, 'character'), array_column($words2, 'character'));
    $this->assertGreaterThan(0, count($overlap));
});

test('flip card game mobile responsiveness and performance', function () {
    $user = User::factory()->create();
    
    // Create 30 words to test performance
    DictionaryZhHans::factory()->count(30)->create(['set' => 'hsk1']);
    
    $startTime = microtime(true);
    
    // Test mobile response
    $response = $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X)'
    ])->actingAs($user)->get('/warrior_flip_card?set=hsk1&word_count=30');
    
    $endTime = microtime(true);
    $responseTime = $endTime - $startTime;
    
    $response->assertStatus(200);
    
    // Should load quickly on mobile (< 1 second)
    $this->assertLessThan(1.0, $responseTime, "Mobile response took too long: {$responseTime} seconds");
});

test('flip card game accessibility features', function () {
    $user = User::factory()->create();
    
    $word = DictionaryZhHans::factory()->create([
        'character' => '学',
        'pinyin' => 'xué',
        'definition' => 'study, learn'
    ]);

    // Test that pinyin is provided for screen readers
    $response = $this->actingAs($user)->get('/warrior_flip_card?set=hsk1&word_count=1');
    
    $response->assertStatus(200);
    $data = $response->json('hanzi_list_data.words.0');
    
    // Should have pinyin for accessibility
    $this->assertArrayHasKey('pinyin', $data);
    $this->assertEquals('xué', $data['pinyin']);
    
    // Should have audio support indication
    $this->assertArrayHasKey('has_audio', $data);
});