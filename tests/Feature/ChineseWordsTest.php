<?php

use App\Models\ChineseWord;
use App\Models\Tag;

// ใช้ uses() แบบง่ายๆ สำหรับ Pest v3
uses()->group('chinese-words');
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('can retrieve chinese words by HSK level', function () {
    // Create test data
    $hsk1Word = ChineseWord::factory()->create([
        'word' => '你好',
        'pinyin' => 'nǐ hǎo',
        'tag' => 'HSK 1',
        'meaning_eng' => 'Hello',
        'meaning_thai' => 'สวัสดี'
    ]);

    $hsk2Word = ChineseWord::factory()->create([
        'word' => '喜欢',
        'pinyin' => 'xǐ huān',
        'tag' => 'HSK 2',
        'meaning_eng' => 'Like',
        'meaning_thai' => 'ชอบ'
    ]);

    // Test HSK 1 endpoint - should return Inertia response
    $response = $this->get('/chinese_words?level=HSK 1');
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->component('WarriorHSKWords')
             ->has('words_list', 1)
             ->where('current_level', 'HSK 1')
             ->where('words_list.0.word', '你好')
             ->where('words_list.0.pinyin', 'nǐ hǎo')
    );
    
    // Test HSK 2 endpoint
    $response = $this->get('/chinese_words?level=HSK 2');
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->component('WarriorHSKWords')
             ->has('words_list', 1)
             ->where('current_level', 'HSK 2')
             ->where('words_list.0.word', '喜欢')
             ->where('words_list.0.pinyin', 'xǐ huān')
    );
});

test('chinese words have proper structure', function () {
    $word = ChineseWord::factory()->create([
        'word' => '学习',
        'pinyin' => 'xué xí',
        'tag' => 'HSK 1',
        'part_of_speech' => 'verb',
        'meaning_eng' => 'Study',
        'meaning_thai' => 'เรียน',
        'example' => '我在学习汉语。',
        'example_pinyin' => 'Wǒ zài xuéxí Hànyǔ.',
        'example_eng' => 'I am studying Chinese.',
        'example_thai' => 'ฉันกำลังเรียนภาษาจีน'
    ]);

    $this->assertDatabaseHas('chinese_words', [
        'word' => '学习',
        'pinyin' => 'xué xí',
        'tag' => 'HSK 1',
        'part_of_speech' => 'verb'
    ]);

    expect($word->meaning_eng)->toBe('Study');
    expect($word->meaning_thai)->toBe('เรียน');
    expect($word->example)->not->toBeNull();
    expect($word->example_pinyin)->not->toBeNull();
});

test('chinese words can be searched and filtered', function () {
    ChineseWord::factory()->create([
        'word' => '老师',
        'pinyin' => 'lǎo shī',
        'tag' => 'HSK 1',
        'meaning_eng' => 'Teacher'
    ]);

    ChineseWord::factory()->create([
        'word' => '学生',
        'pinyin' => 'xué sheng',
        'tag' => 'HSK 1',
        'meaning_eng' => 'Student'
    ]);

    // Test default level (should show all HSK 1 words)
    $response = $this->get('/chinese_words');
    $response->assertStatus(200);
    
    $response->assertInertia(fn ($page) => 
        $page->component('WarriorHSKWords')
             ->has('words_list', 2)
             ->where('current_level', 'HSK 1')
    );
});

test('chinese words support multiple parts of speech', function () {
    $noun = ChineseWord::factory()->create([
        'word' => '书',
        'pinyin' => 'shū',
        'part_of_speech' => 'noun',
        'meaning_eng' => 'Book'
    ]);

    $verb = ChineseWord::factory()->create([
        'word' => '吃',
        'pinyin' => 'chī',
        'part_of_speech' => 'verb',
        'meaning_eng' => 'Eat'
    ]);

    $adjective = ChineseWord::factory()->create([
        'word' => '大',
        'pinyin' => 'dà',
        'part_of_speech' => 'adjective',
        'meaning_eng' => 'Big'
    ]);

    expect($noun->part_of_speech)->toBe('noun');
    expect($verb->part_of_speech)->toBe('verb');
    expect($adjective->part_of_speech)->toBe('adjective');
});

test('chinese words have proper relationships with tags', function () {
    $word = ChineseWord::factory()->create([
        'word' => '工作',
        'tag' => 'HSK 2'
    ]);

    $tag = Tag::factory()->create(['name' => 'HSK 2']);
    $word->tags()->attach($tag);

    expect($word->tags)->toHaveCount(1);
    expect($word->tags->first()->name)->toBe('HSK 2');
});

test('chinese words validation rules', function () {
    // Test invalid data
    $response = $this->post('/api/chinese-words', [
        'word' => '', // Empty word
        'pinyin' => 'invalid pinyin format',
        'meaning_eng' => ''
    ]);

    // Should validate required fields
    $this->assertDatabaseMissing('chinese_words', [
        'pinyin' => 'invalid pinyin format'
    ]);
});

test('chinese words support example sentences', function () {
    $word = ChineseWord::factory()->create([
        'word' => '去',
        'example' => '我去学校。',
        'example_pinyin' => 'Wǒ qù xuéxiào.',
        'example_eng' => 'I go to school.',
        'example_thai' => 'ฉันไปโรงเรียน'
    ]);

    expect($word->example)->not->toBeNull();
    expect($word->example)->toContain('去');
    expect($word->example_eng)->toContain('go');
});

test('chinese words can handle special characters and tones', function () {
    $word = ChineseWord::factory()->create([
        'word' => '女',
        'pinyin' => 'nǚ', // With tone mark
        'meaning_eng' => 'Woman/Female'
    ]);

    expect($word->pinyin)->toBe('nǚ');
    $this->assertDatabaseHas('chinese_words', [
        'word' => '女',
        'pinyin' => 'nǚ'
    ]);
});

test('chinese words performance with large datasets', function () {
    // Create 100 words
    ChineseWord::factory()->count(100)->create();

    $startTime = microtime(true);
    
    $response = $this->get('/chinese_words?level=HSK 1');
    
    $endTime = microtime(true);
    $executionTime = $endTime - $startTime;

    $response->assertStatus(200);
    
    // Response should be fast (< 1 second for 100 records)
    expect($executionTime)->toBeLessThan(1.0);
});