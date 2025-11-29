<?php

namespace Database\Factories;

use App\Models\ChineseWord;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChineseWordFactory extends Factory
{
    protected $model = ChineseWord::class;

    public function definition(): array
    {
        $words = [
            ['学', 'xué', 'study, learn', 'verb'],
            ['习', 'xí', 'practice, habit', 'noun'],
            ['工', 'gōng', 'work, labor', 'noun'],
            ['作', 'zuò', 'to do, to make', 'verb'],
            ['大', 'dà', 'big, large', 'adjective'],
            ['小', 'xiǎo', 'small, little', 'adjective'],
            ['好', 'hǎo', 'good, well', 'adjective'],
            ['人', 'rén', 'person, people', 'noun'],
            ['水', 'shuǐ', 'water', 'noun'],
            ['火', 'huǒ', 'fire', 'noun']
        ];
        
        $wordData = $this->faker->randomElement($words);
        $hskLevels = ['HSK 1', 'HSK 2', 'HSK 3', 'HSK 4', 'HSK 5'];
        
        return [
            'word' => $wordData[0],
            'pinyin' => $wordData[1],
            'tag' => $this->faker->randomElement($hskLevels),
            'part_of_speech' => $wordData[3],
            'meaning_eng' => $wordData[2],
            'meaning_thai' => $this->generateThaiMeaning($wordData[2]),
            'example' => '我喜欢' . $wordData[0] . '。',
            'example_pinyin' => 'Wǒ xǐhuan ' . $wordData[1] . '.',
            'example_eng' => 'I like to ' . $this->generateEnglishVerb($wordData[2]) . '.',
            'example_thai' => 'ฉันชอบ' . $this->generateThaiVerb($wordData[2])
        ];
    }

    private function generateThaiMeaning($english): string
    {
        $translations = [
            'study, learn' => 'เรียน',
            'practice, habit' => 'ฝึกหัด',
            'work, labor' => 'ทำงาน',
            'to do, to make' => 'ทำ',
            'big, large' => 'ใหญ่',
            'small, little' => 'เล็ก',
            'good, well' => 'ดี',
            'person, people' => 'คน',
            'water' => 'น้ำ',
            'fire' => 'ไฟ'
        ];
        
        return $translations[$english] ?? 'ไม่ทราบ';
    }

    private function generateEnglishVerb($meaning): string
    {
        return match($meaning) {
            'study, learn' => 'study',
            'practice, habit' => 'practice',
            'work, labor' => 'work',
            'to do, to make' => 'do',
            'big, large' => 'big',
            'small, little' => 'small',
            'good, well' => 'good',
            'person, people' => 'person',
            'water' => 'water',
            'fire' => 'fire',
            default => 'use'
        };
    }

    private function generateThaiVerb($meaning): string
    {
        return match($meaning) {
            'study, learn' => 'เรียน',
            'practice, habit' => 'ฝึกหัด',
            'work, labor' => 'ทำงาน',
            'to do, to make' => 'ทำ',
            'big, large' => 'ใหญ่',
            'small, little' => 'เล็ก',
            'good, well' => 'ดี',
            'person, people' => 'คน',
            'water' => 'น้ำ',
            'fire' => 'ไฟ',
            default => 'ใช้'
        };
    }
}