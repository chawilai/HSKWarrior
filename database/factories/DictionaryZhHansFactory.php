<?php

namespace Database\Factories;

use App\Models\DictionaryZhHans;
use Illuminate\Database\Eloquent\Factories\Factory;

class DictionaryZhHansFactory extends Factory
{
    protected $model = DictionaryZhHans::class;

    public function definition(): array
    {
        $characters = [
            ['学', 'xué', 'study, learn', 'hsk1', '子', '⿱⿰冖子攵', 'U+5B66'],
            ['习', 'xí', 'practice, habit', 'hsk1', '丿', '⿺丿日', 'U+4E60'],
            ['工', 'gōng', 'work, labor', 'hsk1', '工', '⿻一丨', 'U+5DE5'],
            ['作', 'zuò', 'to do, to make', 'hsk1', '亻', '⿰亻乍', 'U+4F5C'],
            ['大', 'dà', 'big, large', 'hsk1', '大', '⿻人丶', 'U+5927'],
            ['小', 'xiǎo', 'small, little', 'hsk1', '小', '⿻八丶', 'U+5C0F'],
            ['好', 'hǎo', 'good, well', 'hsk1', '女', '⿰女子', 'U+597D'],
            ['人', 'rén', 'person, people', 'hsk1', '人', '⿰丿㇏', 'U+4EBA'],
            ['水', 'shuǐ', 'water', 'hsk1', '水', '⿱亅㇇', 'U+6C34'],
            ['火', 'huǒ', 'fire', 'hsk1', '火', '⿰丷人', 'U+706B']
        ];
        
        $charData = $this->faker->randomElement($characters);
        
        return [
            'character' => $charData[0],
            'pinyin' => $charData[1],
            'pinyin_english' => $this->convertPinyinToEnglish($charData[1]),
            'definition' => $charData[2],
            'meaning_thai' => $this->generateThaiMeaning($charData[2]),
            'set' => $charData[3],
            'radical' => $charData[4],
            'decomposition' => $charData[5],
            'acjk' => $charData[6]
        ];
    }

    private function convertPinyinToEnglish($pinyin): string
    {
        // Simple conversion - in real app would use proper pinyin library
        return str_replace(['ā', 'á', 'ǎ', 'à', 'ē', 'é', 'ě', 'è', 'ī', 'í', 'ǐ', 'ì', 'ō', 'ó', 'ǒ', 'ò', 'ū', 'ú', 'ǔ', 'ù', 'ǖ', 'ǘ', 'ǚ', 'ǜ'], 
                          ['a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'v', 'v', 'v', 'v'], 
                          $pinyin);
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
}