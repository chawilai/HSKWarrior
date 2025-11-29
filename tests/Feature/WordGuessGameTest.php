<?php

use App\Models\DictionaryZhHans;
use App\Models\User;
use App\Models\WordGuess;

uses()->group('word-guess');
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('word guess game can record user attempts', function () {
    $user = User::factory()->create();
    $word = DictionaryZhHans::factory()->create([
        'character' => '学',
        'pinyin' => 'xué',
        'definition' => 'study, learn'
    ]);

    // Record a correct guess
    $response = $this->actingAs($user)->post('/word-guess', [
        'word_id' => $word->id,
        'user_answer' => 'study',
        'is_correct' => true
    ]);

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('word_guesses', [
        'user_id' => $user->id,
        'word_id' => $word->id,
        'user_answer' => 'study',
        'is_correct' => true
    ]);
});

test('word guess game tracks incorrect attempts', function () {
    $user = User::factory()->create();
    $word = DictionaryZhHans::factory()->create([
        'character' => '学',
        'pinyin' => 'xué',
        'definition' => 'study, learn'
    ]);

    // Record an incorrect guess
    $response = $this->actingAs($user)->post('/word-guess', [
        'word_id' => $word->id,
        'user_answer' => 'play',
        'is_correct' => false
    ]);

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('word_guesses', [
        'user_id' => $user->id,
        'word_id' => $word->id,
        'user_answer' => 'play',
        'is_correct' => false
    ]);
});

test('word guess validation requires authentication', function () {
    $word = DictionaryZhHans::factory()->create();

    $response = $this->post('/word-guess', [
        'word_id' => $word->id,
        'user_answer' => 'test',
        'is_correct' => true
    ]);

    $response->assertStatus(302); // Should redirect to login
    $response->assertRedirect('/login');
});

test('word guess validation requires valid word_id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/word-guess', [
        'word_id' => 999999, // Invalid word ID
        'user_answer' => 'test',
        'is_correct' => true
    ]);

    $response->assertStatus(404);
});

test('word guess game provides feedback for learning', function () {
    $user = User::factory()->create();
    $word = DictionaryZhHans::factory()->create([
        'character' => '学',
        'pinyin' => 'xué',
        'definition' => 'study, learn',
        'meaning_thai' => 'เรียน'
    ]);

    // Make multiple attempts
    WordGuess::create([
        'user_id' => $user->id,
        'word_id' => $word->id,
        'user_answer' => 'play',
        'is_correct' => false
    ]);

    WordGuess::create([
        'user_id' => $user->id,
        'word_id' => $word->id,
        'user_answer' => 'study',
        'is_correct' => true
    ]);

    // Check user progress
    $correctGuesses = WordGuess::where('user_id', $user->id)
        ->where('word_id', $word->id)
        ->where('is_correct', true)
        ->count();

    $totalGuesses = WordGuess::where('user_id', $user->id)
        ->where('word_id', $word->id)
        ->count();

    $this->assertEquals(1, $correctGuesses);
    $this->assertEquals(2, $totalGuesses);
});

test('word guess game integrates with spaced repetition', function () {
    $user = User::factory()->create();
    
    // Create words in different lists
    $word1 = DictionaryZhHans::factory()->create(['character' => '学']);
    $word2 = DictionaryZhHans::factory()->create(['character' => '习']);
    $word3 = DictionaryZhHans::factory()->create(['character' => '工']);

    // Record guesses with different success rates
    WordGuess::create([
        'user_id' => $user->id,
        'word_id' => $word1->id,
        'user_answer' => 'study',
        'is_correct' => true,
        'created_at' => now()->subDays(1)
    ]);

    WordGuess::create([
        'user_id' => $user->id,
        'word_id' => $word2->id,
        'user_answer' => 'practice',
        'is_correct' => false,
        'created_at' => now()->subDays(2)
    ]);

    // Get words that need review (incorrect guesses)
    $wordsNeedReview = WordGuess::where('user_id', $user->id)
        ->where('is_correct', false)
        ->pluck('word_id')
        ->toArray();

    $this->assertContains($word2->id, $wordsNeedReview);
    $this->assertNotContains($word1->id, $wordsNeedReview);
});

test('word guess game provides hints and clues', function () {
    $user = User::factory()->create();
    $word = DictionaryZhHans::factory()->create([
        'character' => '学',
        'pinyin' => 'xué',
        'definition' => 'study, learn',
        'radical' => '子'
    ]);

    // Test that word data includes helpful information
    $response = $this->get("/api/word-hints/{$word->id}");
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'character',
        'pinyin',
        'radical',
        'stroke_count',
        'similar_words'
    ]);
});

test('word guess game handles multiple answer formats', function () {
    $user = User::factory()->create();
    $word = DictionaryZhHans::factory()->create([
        'character' => '学',
        'definition' => 'study, learn'
    ]);

    // Test various acceptable answers
    $acceptableAnswers = ['study', 'learn', 'to study', 'learning'];
    
    foreach ($acceptableAnswers as $answer) {
        $response = $this->actingAs($user)->post('/word-guess', [
            'word_id' => $word->id,
            'user_answer' => $answer,
            'is_correct' => true
        ]);

        $response->assertStatus(201);
    }
});

test('word guess statistics and analytics', function () {
    $user = User::factory()->create();
    
    // Create sample data
    for ($i = 0; $i < 10; $i++) {
        WordGuess::create([
            'user_id' => $user->id,
            'word_id' => DictionaryZhHans::factory()->create()->id,
            'user_answer' => 'test',
            'is_correct' => $i % 3 === 0 // 33% success rate
        ]);
    }

    // Calculate statistics
    $totalGuesses = WordGuess::where('user_id', $user->id)->count();
    $correctGuesses = WordGuess::where('user_id', $user->id)
        ->where('is_correct', true)
        ->count();
    
    $accuracyRate = $totalGuesses > 0 ? ($correctGuesses / $totalGuesses) * 100 : 0;

    $this->assertEquals(10, $totalGuesses);
    $this->assertEquals(4, $correctGuesses); // 0, 3, 6, 9
    $this->assertEquals(40.0, $accuracyRate);
});

test('word guess game difficulty progression', function () {
    $user = User::factory()->create();
    
    // Create words of different difficulty levels
    $easyWord = DictionaryZhHans::factory()->create([
        'character' => '大',
        'tag' => 'HSK 1'
    ]);
    
    $mediumWord = DictionaryZhHans::factory()->create([
        'character' => '复杂',
        'tag' => 'HSK 3'
    ]);
    
    $hardWord = DictionaryZhHans::factory()->create([
        'character' => '尴尬',
        'tag' => 'HSK 5'
    ]);

    // User should start with easier words
    $userLevel = 'HSK 1'; // Starting level
    
    $availableWords = DictionaryZhHans::where('set', 'like', "%{$userLevel}%")
        ->pluck('id')
        ->toArray();

    $this->assertContains($easyWord->id, $availableWords);
});

test('word guess game time tracking and performance', function () {
    $user = User::factory()->create();
    $word = DictionaryZhHans::factory()->create(['character' => '学']);

    $startTime = microtime(true);
    
    $response = $this->actingAs($user)->post('/word-guess', [
        'word_id' => $word->id,
        'user_answer' => 'study',
        'is_correct' => true
    ]);
    
    $endTime = microtime(true);
    $responseTime = $endTime - $startTime;

    $response->assertStatus(201);
    
    // Response should be fast (< 0.5 seconds)
    $this->assertLessThan(0.5, $responseTime, "Response took too long: {$responseTime} seconds");
});