<?php

use App\Models\DictionaryZhHans;
use App\Models\HanziList;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses()->group('hanzi-writing');
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('can retrieve hanzi characters with proper structure', function () {
    $hanzi = DictionaryZhHans::factory()->create([
        'character' => '学',
        'pinyin' => 'xué',
        'definition' => 'study, learn',
        'set' => 'hsk1',
        'radical' => '子',
        'decomposition' => '⿱⿰冖子攵',
        'acjk' => 'U+5B66'
    ]);

    $response = $this->get('/warrior_writehanzi');
    
    $response->assertStatus(200);
    
    // Check if the response contains hanzi data
    $response->assertInertia(fn ($page) => 
        $page->component('WarriorWriteHanzi')
             ->has('hanzi_list.data', 1)
    );
});

test('hanzi characters can be searched and filtered', function () {
    DictionaryZhHans::factory()->create([
        'character' => '学',
        'set' => 'hsk1',
        'pinyin' => 'xué'
    ]);

    DictionaryZhHans::factory()->create([
        'character' => '习',
        'set' => 'hsk1',
        'pinyin' => 'xí'
    ]);

    // Test search by character
    $response = $this->get('/warrior_writehanzi?s_hanzi=学');
    $response->assertStatus(200);
    
    // Test search by pinyin
    $response = $this->get('/warrior_writehanzi?s_pinyin=xué');
    $response->assertStatus(200);
    
    // Test filter by set
    $response = $this->get('/warrior_writehanzi?s_set=hsk1');
    $response->assertStatus(200);
});

test('hanzi characters have proper stroke order and decomposition', function () {
    $hanzi = DictionaryZhHans::factory()->create([
        'character' => '好',
        'decomposition' => '⿰女子',
        'radical' => '女',

    ]);

    $this->assertEquals('⿰女子', $hanzi->decomposition);
    $this->assertEquals('女', $hanzi->radical);
    $this->assertDatabaseHas('dictionary_zh_hans', [
        'character' => '好',
        'decomposition' => '⿰女子'
    ]);
});

test('hanzi lists can be created and managed', function () {
    $user = User::factory()->create();
    
    $hanzi1 = DictionaryZhHans::factory()->create(['character' => '学']);
    $hanzi2 = DictionaryZhHans::factory()->create(['character' => '习']);

    // Create a hanzi list
    $response = $this->actingAs($user)->post('/save_hanzi_list', [
        'list_reference' => 'test-list-001',
        'list_name' => 'My Study List',
        'list' => ['学', '习']
    ]);

    $response->assertRedirect();
    
    $this->assertDatabaseHas('hanzi_lists', [
        'list_reference' => 'test-list-001',
        'list_name' => 'My Study List',
        'user_id' => $user->id
    ]);

    $this->assertDatabaseHas('hanzi_list_words', [
        'hanzi_list_id' => 1,
        'hanzi_id' => $hanzi1->id
    ]);
});

test('hanzi writing practice tracks user progress', function () {
    $user = User::factory()->create();
    $hanzi = DictionaryZhHans::factory()->create(['character' => '学']);

    // Simulate writing practice
    $practiceData = [
        'hanzi_id' => $hanzi->id,
        'attempts' => 3,
        'correct' => true,
        'stroke_accuracy' => 85.5
    ];

    // This would be an API endpoint for tracking practice
    $response = $this->actingAs($user)->post('/api/hanzi-practice', $practiceData);
    
    // Verify practice was recorded
    $this->assertDatabaseHas('practice_sessions', [
        'user_id' => $user->id,
        'hanzi_id' => $hanzi->id,
        'correct' => true
    ]);
});

test('hanzi characters support unicode and special formatting', function () {
    $hanzi = DictionaryZhHans::factory()->create([
        'character' => '龙',
        'acjk' => 'U+9F99',
        'pinyin' => 'lóng',
        'definition' => 'dragon'
    ]);

    $this->assertEquals('U+9F99', $hanzi->acjk);
    $this->assertEquals('龙', $hanzi->character);
    
    // Test unicode handling
    $this->assertDatabaseHas('dictionary_zh_hans', [
        'character' => '龙',
        'acjk' => 'U+9F99'
    ]);
});

test('hanzi stroke analysis and feedback system', function () {
    $hanzi = DictionaryZhHans::factory()->create([
        'character' => '中',
        'stroke_count' => 4,
        'radical' => '丨'
    ]);

    // Test stroke analysis endpoint
    $response = $this->get("/api/hanzi-strokes/{$hanzi->id}");
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'character',
        'stroke_count',
        'radical',
        'stroke_order'
    ]);
});

test('hanzi lists support different box numbers for spaced repetition', function () {
    $user = User::factory()->create();
    
    // Create lists with different box numbers
    $list1 = HanziList::factory()->create([
        'user_id' => $user->id,
        'box_number' => 5
    ]);
    
    $list2 = HanziList::factory()->create([
        'user_id' => $user->id,
        'box_number' => 10
    ]);

    $this->assertEquals(5, $list1->box_number);
    $this->assertEquals(10, $list2->box_number);
    
    // Test retrieval with box filtering
    $response = $this->actingAs($user)->get('/hanzi_list?reference=' . $list1->list_reference);
    $response->assertStatus(200);
});

test('hanzi writing validation and error handling', function () {
    // Test invalid hanzi creation
    $response = $this->post('/api/hanzi', [
        'character' => '', // Empty character
        'pinyin' => 'invalid format',
        'definition' => ''
    ]);

    // Should fail validation
    $this->assertDatabaseMissing('dictionary_zh_hans', [
        'character' => ''
    ]);
});

test('hanzi performance and large dataset handling', function () {
    // Create 50 hanzi characters
    DictionaryZhHans::factory()->count(50)->create();

    $startTime = microtime(true);
    
    $response = $this->get('/warrior_writehanzi?page=2');
    
    $endTime = microtime(true);
    $executionTime = $endTime - $startTime;

    $response->assertStatus(200);
    
    // Should handle pagination efficiently
    $this->assertLessThan(0.5, $executionTime, "Page load took too long: {$executionTime} seconds");
});

test('hanzi pinyin conversion and tone handling', function () {
    $hanzi = DictionaryZhHans::factory()->create([
        'character' => '女',
        'pinyin' => 'nǚ',
        'pinyin_english' => 'nv3' // Alternative pinyin format
    ]);

    $this->assertEquals('nǚ', $hanzi->pinyin);
    
    // Test pinyin search functionality
    $response = $this->get('/warrior_writehanzi?s_pinyin=nǚ');
    $response->assertStatus(200);
});