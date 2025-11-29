<?php

use App\Models\ExperienceHistory;
use App\Models\User;
use App\Models\WordGuess;

uses()->group('user-progress');
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can gain experience points for correct answers', function () {
    $user = User::factory()->create(['experience' => 0]);
    
    // Add experience for correct answer
    $user->addExperience(10, 'Correct word guess');
    
    $this->assertEquals(10, $user->fresh()->experience);
    
    // Check experience history was recorded
    $this->assertDatabaseHas('experience_history', [
        'user_id' => $user->id,
        'change' => 10,
        'reason' => 'Correct word guess'
    ]);
});

test('user experience cannot go below zero', function () {
    $user = User::factory()->create(['experience' => 5]);
    
    // Try to subtract more than current experience
    $user->cutExperience(10, 'Incorrect answer penalty');
    
    $this->assertEquals(0, $user->fresh()->experience);
    
    // History should show the actual amount subtracted
    $this->assertDatabaseHas('experience_history', [
        'user_id' => $user->id,
        'change' => -5, // Only 5 was actually subtracted
        'reason' => 'Incorrect answer penalty'
    ]);
});

test('user experience history tracks all changes', function () {
    $user = User::factory()->create(['experience' => 0]);
    
    // Add multiple experience changes
    $user->addExperience(10, 'Daily login');
    $user->addExperience(5, 'Completed lesson');
    $user->cutExperience(3, 'Wrong answer');
    $user->addExperience(15, 'Perfect score');
    
    $history = ExperienceHistory::where('user_id', $user->id)
        ->orderBy('created_at')
        ->get();
    
    $this->assertCount(4, $history);
    $this->assertEquals(10, $history[0]->change);
    $this->assertEquals(5, $history[1]->change);
    $this->assertEquals(-3, $history[2]->change);
    $this->assertEquals(15, $history[3]->change);
    
    $this->assertEquals(27, $user->fresh()->experience); // 10 + 5 - 3 + 15
});

test('user progress tracking for hanzi learning', function () {
    $user = User::factory()->create();
    
    // Simulate learning progress
    $progressData = [
        'hanzi_learned' => 25,
        'hanzi_practiced' => 50,
        'practice_sessions' => 10,
        'average_accuracy' => 85.5,
        'streak_days' => 7
    ];
    
    // Update user progress
    $user->update(['learning_progress' => $progressData]);
    
    $this->assertDatabaseHas('users', [
        'id' => $user->id
    ]);
    
    // Verify progress data structure
    $progress = $user->fresh()->learning_progress;
    $this->assertEquals(25, $progress['hanzi_learned']);
    $this->assertEquals(85.5, $progress['average_accuracy']);
});

test('user streak tracking for daily practice', function () {
    $user = User::factory()->create([
        'streak_count' => 5,
        'last_practice_date' => now()->subDay()
    ]);
    
    // Simulate today's practice
    $user->update([
        'streak_count' => 6,
        'last_practice_date' => now()
    ]);
    
    $this->assertEquals(6, $user->fresh()->streak_count);
    
    // Simulate missing a day (streak should reset)
    $user->update([
        'streak_count' => 0,
        'last_practice_date' => now()->addDays(2)
    ]);
    
    $this->assertEquals(0, $user->fresh()->streak_count);
});

test('user level progression based on experience', function () {
    $user = User::factory()->create(['experience' => 0]);
    
    // Define level thresholds
    $levels = [
        1 => 0,
        2 => 100,
        3 => 250,
        4 => 500,
        5 => 1000
    ];
    
    // Test level progression
    $user->addExperience(50);
    $this->assertEquals(1, $this->calculateUserLevel($user->fresh()->experience, $levels));
    
    $user->addExperience(100);
    $this->assertEquals(2, $this->calculateUserLevel($user->fresh()->experience, $levels));
    
    $user->addExperience(300);
    $this->assertEquals(3, $this->calculateUserLevel($user->fresh()->experience, $levels));
});

test('user achievement system for milestones', function () {
    $user = User::factory()->create(['experience' => 0]);
    
    // Define achievements
    $achievements = [
        'first_word' => ['description' => 'Learn your first word', 'points' => 10],
        'streak_7_days' => ['description' => '7-day learning streak', 'points' => 50],
        'hsk1_complete' => ['description' => 'Complete HSK 1', 'points' => 100],
        'perfect_score' => ['description' => 'Get 100% on a quiz', 'points' => 25]
    ];
    
    // Award achievement
    $user->addExperience(10, 'Achievement: First word learned');
    $achievement = $user->achievements()->create([
        'achievement_type' => 'first_word',
        'points' => 10
    ]);
    
    $this->assertDatabaseHas('user_achievements', [
        'user_id' => $user->id,
        'achievement_type' => 'first_word',
        'points' => 10
    ]);
});

test('user daily goals and target tracking', function () {
    $user = User::factory()->create();
    
    // Set daily goals
    $dailyGoals = [
        'hanzi_learned' => 5,
        'practice_time' => 30, // minutes
        'quizzes_completed' => 3,
        'streak_maintained' => true
    ];
    
    // Track progress
    $todayProgress = [
        'hanzi_learned' => 3,
        'practice_time' => 20,
        'quizzes_completed' => 2,
        'streak_maintained' => true
    ];
    
    // Calculate completion percentage
    $completionRate = $this->calculateGoalCompletion($dailyGoals, $todayProgress);
    
    $this->assertEquals(60, $completionRate); // 60% completion
    
    // Check if goals are stored
    $user->update(['daily_goals' => $dailyGoals]);
    $this->assertEquals($dailyGoals, $user->fresh()->daily_goals);
});

test('user learning analytics and insights', function () {
    $user = User::factory()->create();
    
    // Create sample learning data
    WordGuess::factory()->count(20)->create([
        'user_id' => $user->id,
        'is_correct' => true
    ]);
    
    WordGuess::factory()->count(10)->create([
        'user_id' => $user->id,
        'is_correct' => false
    ]);
    
    // Calculate analytics
    $totalAttempts = 30;
    $correctAttempts = 20;
    $accuracyRate = ($correctAttempts / $totalAttempts) * 100;
    $averageScore = 75.5;
    
    // Generate insights
    $insights = [
        'accuracy_rate' => $accuracyRate,
        'strongest_categories' => ['HSK 1', 'Basic Greetings'],
        'weakest_categories' => ['HSK 5', 'Business Terms'],
        'learning_velocity' => 'improving',
        'recommended_focus' => 'HSK 5 vocabulary'
    ];
    
    $this->assertEquals(66.67, round($accuracyRate, 2));
    $this->assertEquals('improving', $insights['learning_velocity']);
});

test('user preference settings and customization', function () {
    $user = User::factory()->create();
    
    // Set user preferences
    $preferences = [
        'difficulty_level' => 'intermediate',
        'preferred_voice' => 'zh-CN-XiaoxiaoNeural',
        'auto_play_audio' => true,
        'show_pinyin' => true,
        'practice_reminders' => true,
        'daily_goal' => 30,
        'theme' => 'dark'
    ];
    
    $user->update(['preferences' => $preferences]);
    
    $savedPreferences = $user->fresh()->preferences;
    
    $this->assertEquals('intermediate', $savedPreferences['difficulty_level']);
    $this->assertTrue($savedPreferences['auto_play_audio']);
    $this->assertEquals('dark', $savedPreferences['theme']);
});

test('user social features and leaderboard integration', function () {
    $user1 = User::factory()->create(['experience' => 1000]);
    $user2 = User::factory()->create(['experience' => 750]);
    $user3 = User::factory()->create(['experience' => 500]);
    
    // Get leaderboard data
    $leaderboard = User::orderBy('experience', 'desc')
        ->limit(10)
        ->get()
        ->map(function ($user, $index) {
            return [
                'rank' => $index + 1,
                'name' => $user->name,
                'experience' => $user->experience
            ];
        });
    
    $this->assertEquals(1, $leaderboard[0]['rank']);
    $this->assertEquals($user1->name, $leaderboard[0]['name']);
    $this->assertEquals(1000, $leaderboard[0]['experience']);
    
    $this->assertEquals(2, $leaderboard[1]['rank']);
    $this->assertEquals($user2->name, $leaderboard[1]['name']);
    $this->assertEquals(750, $leaderboard[1]['experience']);
});

test('user data export and portability', function () {
    $user = User::factory()->create();
    
    // Create sample data
    ExperienceHistory::factory()->count(10)->create([
        'user_id' => $user->id
    ]);
    
    // Generate export data
    $exportData = [
        'user_info' => [
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at
        ],
        'learning_progress' => $user->learning_progress,
        'experience_history' => $user->experienceHistory()->get(),
        'achievements' => $user->achievements()->get(),
        'total_experience' => $user->experience
    ];
    
    // Verify export structure
    $this->assertArrayHasKey('user_info', $exportData);
    $this->assertArrayHasKey('learning_progress', $exportData);
    $this->assertArrayHasKey('experience_history', $exportData);
    $this->assertEquals($user->experience, $exportData['total_experience']);
});

// Helper function for level calculation
function calculateUserLevel($experience, $levels) {
    $currentLevel = 1;
    foreach ($levels as $level => $threshold) {
        if ($experience >= $threshold) {
            $currentLevel = $level;
        }
    }
    return $currentLevel;
}

// Helper function for goal completion
function calculateGoalCompletion($goals, $progress) {
    $totalGoals = count($goals);
    $completedGoals = 0;
    
    foreach ($goals as $key => $target) {
        if (isset($progress[$key])) {
            if (is_bool($target)) {
                if ($progress[$key] === $target) $completedGoals++;
            } else {
                if ($progress[$key] >= $target) $completedGoals++;
            }
        }
    }
    
    return ($completedGoals / $totalGoals) * 100;
}