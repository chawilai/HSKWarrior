<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HanziInertialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categories
        DB::table('categories')->insert([
            ['title_thai' => 'อักษร HSK 1', 'title_english' => 'HSK 1'],
            ['title_thai' => 'อักษร HSK 2', 'title_english' => 'HSK 2'],
            ['title_thai' => 'อักษร HSK 3', 'title_english' => 'HSK 3'],
            ['title_thai' => 'อักษร HSK 4', 'title_english' => 'HSK 4'],
            ['title_thai' => 'อักษร HSK 5', 'title_english' => 'HSK 5'],
            ['title_thai' => 'อักษร HSK 6', 'title_english' => 'HSK 6'],
            ['title_thai' => 'อักษร ใช้บ่อย', 'title_english' => 'Frequently Used Hanzi'],
            ['title_thai' => 'อักษร สามัญ', 'title_english' => 'Common Hanzi'],
            ['title_thai' => 'อักษร ดั่งเดิม', 'title_english' => 'Traditional Hanzi'],
        ]);

        // Parts of Speech
        DB::table('parts_of_speech')->insert([
            ['title_thai' => 'Noun', 'abbreviation_thai' => 'n.', 'meaning_thai' => 'A word that names a person, place, thing, or idea.', 'title_english' => 'Noun', 'abbreviation_english' => 'n.', 'meaning_english' => 'A word that names a person, place, thing, or idea.'],
            ['title_thai' => 'Pronoun', 'abbreviation_thai' => 'pron.', 'meaning_thai' => 'A word that takes the place of a noun.', 'title_english' => 'Pronoun', 'abbreviation_english' => 'pron.', 'meaning_english' => 'A word that takes the place of a noun.'],
            ['title_thai' => 'Verb', 'abbreviation_thai' => 'v.', 'meaning_thai' => 'A word that expresses an action or state of being.', 'title_english' => 'Verb', 'abbreviation_english' => 'v.', 'meaning_english' => 'A word that expresses an action or state of being.'],
            ['title_thai' => 'Adjective', 'abbreviation_thai' => 'adj.', 'meaning_thai' => 'A word that modifies a noun or pronoun.', 'title_english' => 'Adjective', 'abbreviation_english' => 'adj.', 'meaning_english' => 'A word that modifies a noun or pronoun.'],
            ['title_thai' => 'Adverb', 'abbreviation_thai' => 'adv.', 'meaning_thai' => 'A word that modifies a verb, adjective, or another adverb.', 'title_english' => 'Adverb', 'abbreviation_english' => 'adv.', 'meaning_english' => 'A word that modifies a verb, adjective, or another adverb.'],
            ['title_thai' => 'Preposition', 'abbreviation_thai' => 'prep.', 'meaning_thai' => 'A word that shows the relationship between a noun or pronoun and other words in the sentence.', 'title_english' => 'Preposition', 'abbreviation_english' => 'prep.', 'meaning_english' => 'A word that shows the relationship between a noun or pronoun and other words in the sentence.'],
            ['title_thai' => 'Conjunction', 'abbreviation_thai' => 'conj.', 'meaning_thai' => 'A word that connects words, phrases, or clauses.', 'title_english' => 'Conjunction', 'abbreviation_english' => 'conj.', 'meaning_english' => 'A word that connects words, phrases, or clauses.'],
            ['title_thai' => 'Interjection', 'abbreviation_thai' => 'interj.', 'meaning_thai' => 'A word that expresses strong emotion and is often followed by an exclamation point.', 'title_english' => 'Interjection', 'abbreviation_english' => 'interj.', 'meaning_english' => 'A word that expresses strong emotion and is often followed by an exclamation point.'],
            ['title_thai' => 'Article', 'abbreviation_thai' => 'art.', 'meaning_thai' => 'A word used before a noun to indicate whether it refers to something specific (the) or something general (a, an). Sometimes included as a type of adjective.', 'title_english' => 'Article', 'abbreviation_english' => 'art.', 'meaning_english' => 'A word used before a noun to indicate whether it refers to something specific (the) or something general (a, an). Sometimes included as a type of adjective.'],
        ]);

        // Radical
        DB::table('radicals')->insert([
            ['radical' => '一', 'pinyin' => 'yī', 'pinyin_eng' => 'yi', 'meaning_thai' => 'หนึ่ง', 'meaning_english' => 'one'],
            ['radical' => '丨', 'pinyin' => 'gǔn', 'pinyin_eng' => 'gun', 'meaning_thai' => 'เส้น', 'meaning_english' => 'line'],
            ['radical' => '丶', 'pinyin' => 'zhǔ', 'pinyin_eng' => 'zhu', 'meaning_thai' => 'จุด', 'meaning_english' => 'dot'],
            ['radical' => '丿', 'pinyin' => 'piě', 'pinyin_eng' => 'pie', 'meaning_thai' => 'เส้นเฉียง', 'meaning_english' => 'slash'],
            ['radical' => '乙', 'pinyin' => 'yǐ', 'pinyin_eng' => 'yi', 'meaning_thai' => 'ตะขอ', 'meaning_english' => 'second'],
            ['radical' => '亅', 'pinyin' => 'jué', 'pinyin_eng' => 'jue', 'meaning_thai' => 'ตะขอ', 'meaning_english' => 'hook'],
            ['radical' => '二', 'pinyin' => 'èr', 'pinyin_eng' => 'er', 'meaning_thai' => 'สอง', 'meaning_english' => 'two'],
            ['radical' => '亠', 'pinyin' => 'tóu', 'pinyin_eng' => 'tou', 'meaning_thai' => 'ฝาครอบ', 'meaning_english' => 'lid'],
            ['radical' => '人 (亻)', 'pinyin' => 'rén', 'pinyin_eng' => 'ren', 'meaning_thai' => 'คน', 'meaning_english' => 'person'],
            ['radical' => '儿', 'pinyin' => 'ér', 'pinyin_eng' => 'er', 'meaning_thai' => 'ขา', 'meaning_english' => 'son, legs'],
            ['radical' => '入', 'pinyin' => 'rù', 'pinyin_eng' => 'ru', 'meaning_thai' => 'เข้า', 'meaning_english' => 'enter'],
            ['radical' => '八 (丷)', 'pinyin' => 'bā', 'pinyin_eng' => 'ba', 'meaning_thai' => 'แปด', 'meaning_english' => 'eight'],
            ['radical' => '冂', 'pinyin' => 'jiōng', 'pinyin_eng' => 'jiong', 'meaning_thai' => 'เส้นปิด', 'meaning_english' => 'down box'],
            ['radical' => '冖', 'pinyin' => 'mì', 'pinyin_eng' => 'mi', 'meaning_thai' => 'ผ้าคลุม', 'meaning_english' => 'cover'],
            ['radical' => '冫', 'pinyin' => 'bīng', 'pinyin_eng' => 'bing', 'meaning_thai' => 'น้ำแข็ง', 'meaning_english' => 'ice'],
            ['radical' => '几', 'pinyin' => 'jī', 'pinyin_eng' => 'ji', 'meaning_thai' => 'โต๊ะเล็ก', 'meaning_english' => 'table'],
            ['radical' => '凵', 'pinyin' => 'kǎn', 'pinyin_eng' => 'kan', 'meaning_thai' => 'เปิดปาก', 'meaning_english' => 'open box'],
            ['radical' => '刀 (刂)', 'pinyin' => 'dāo', 'pinyin_eng' => 'dao', 'meaning_thai' => 'มีด', 'meaning_english' => 'knife'],
            ['radical' => '力', 'pinyin' => 'lì', 'pinyin_eng' => 'li', 'meaning_thai' => 'แรง', 'meaning_english' => 'power'],
            ['radical' => '勹', 'pinyin' => 'bāo', 'pinyin_eng' => 'bao', 'meaning_thai' => 'กอด', 'meaning_english' => 'wrap'],
            ['radical' => '匕', 'pinyin' => 'bǐ', 'pinyin_eng' => 'bi', 'meaning_thai' => 'ช้อน', 'meaning_english' => 'spoon'],
            ['radical' => '匚', 'pinyin' => 'fāng', 'pinyin_eng' => 'fang', 'meaning_thai' => 'กล่องปิด', 'meaning_english' => 'box'],
            ['radical' => '匸', 'pinyin' => 'xǐ', 'pinyin_eng' => 'xi', 'meaning_thai' => 'ซ่อน', 'meaning_english' => 'hiding enclosure'],
            ['radical' => '十', 'pinyin' => 'shí', 'pinyin_eng' => 'shi', 'meaning_thai' => 'สิบ', 'meaning_english' => 'ten'],
            ['radical' => '卜', 'pinyin' => 'bǔ', 'pinyin_eng' => 'bu', 'meaning_thai' => 'ทำนาย', 'meaning_english' => 'divination'],
            ['radical' => '卩', 'pinyin' => 'jié', 'pinyin_eng' => 'jie', 'meaning_thai' => 'ตรา', 'meaning_english' => 'seal'],
            ['radical' => '厂', 'pinyin' => 'hàn', 'pinyin_eng' => 'han', 'meaning_thai' => 'หน้าผา', 'meaning_english' => 'cliff'],
            ['radical' => '厶', 'pinyin' => 'sī', 'pinyin_eng' => 'si', 'meaning_thai' => 'ส่วนตัว', 'meaning_english' => 'private'],
            ['radical' => '又', 'pinyin' => 'yòu', 'pinyin_eng' => 'you', 'meaning_thai' => 'มืออีกข้าง', 'meaning_english' => 'again'],
            ['radical' => '口', 'pinyin' => 'kǒu', 'pinyin_eng' => 'kou', 'meaning_thai' => 'ปาก', 'meaning_english' => 'mouth'],
            ['radical' => '囗', 'pinyin' => 'wéi', 'pinyin_eng' => 'wei', 'meaning_thai' => 'ล้อม', 'meaning_english' => 'enclosure'],
            ['radical' => '土', 'pinyin' => 'tǔ', 'pinyin_eng' => 'tu', 'meaning_thai' => 'ดิน', 'meaning_english' => 'earth'],
            ['radical' => '士', 'pinyin' => 'shì', 'pinyin_eng' => 'shi', 'meaning_thai' => 'นักรบ', 'meaning_english' => 'scholar, knight'],
            ['radical' => '夂', 'pinyin' => 'zhǐ', 'pinyin_eng' => 'zhi', 'meaning_thai' => 'เดิน', 'meaning_english' => 'go'],
            ['radical' => '夊', 'pinyin' => 'suī', 'pinyin_eng' => 'sui', 'meaning_thai' => 'เดินช้า', 'meaning_english' => 'go slowly'],
            ['radical' => '夕', 'pinyin' => 'xī', 'pinyin_eng' => 'xi', 'meaning_thai' => 'เย็น', 'meaning_english' => 'evening'],
            ['radical' => '大', 'pinyin' => 'dà', 'pinyin_eng' => 'da', 'meaning_thai' => 'ใหญ่', 'meaning_english' => 'big'],
            ['radical' => '女', 'pinyin' => 'nǚ', 'pinyin_eng' => 'nu', 'meaning_thai' => 'ผู้หญิง', 'meaning_english' => 'woman'],
            ['radical' => '子', 'pinyin' => 'zǐ', 'pinyin_eng' => 'zi', 'meaning_thai' => 'ลูกชาย', 'meaning_english' => 'child'],
            ['radical' => '宀', 'pinyin' => 'mián', 'pinyin_eng' => 'mian', 'meaning_thai' => 'หลังคา', 'meaning_english' => 'roof'],
            ['radical' => '寸', 'pinyin' => 'cùn', 'pinyin_eng' => 'cun', 'meaning_thai' => 'นิ้ว', 'meaning_english' => 'inch'],
            ['radical' => '小 (⺌, ⺍)', 'pinyin' => 'xiǎo', 'pinyin_eng' => 'xiao', 'meaning_thai' => 'เล็ก', 'meaning_english' => 'small'],
            ['radical' => '尢 (尣)', 'pinyin' => 'wāng', 'pinyin_eng' => 'wang', 'meaning_thai' => 'ขาพิการ', 'meaning_english' => 'lame'],
            ['radical' => '尸', 'pinyin' => 'shī', 'pinyin_eng' => 'shi', 'meaning_thai' => 'ศพ', 'meaning_english' => 'corpse'],
            ['radical' => '屮', 'pinyin' => 'chè', 'pinyin_eng' => 'che', 'meaning_thai' => 'หน่อ', 'meaning_english' => 'sprout'],
            ['radical' => '山', 'pinyin' => 'shān', 'pinyin_eng' => 'shan', 'meaning_thai' => 'ภูเขา', 'meaning_english' => 'mountain'],
            ['radical' => '巛 (川, 巜)', 'pinyin' => 'chuān', 'pinyin_eng' => 'chuan', 'meaning_thai' => 'แม่น้ำ', 'meaning_english' => 'river'],
            ['radical' => '工', 'pinyin' => 'gōng', 'pinyin_eng' => 'gong', 'meaning_thai' => 'งาน', 'meaning_english' => 'work'],
            ['radical' => '己', 'pinyin' => 'jǐ', 'pinyin_eng' => 'ji', 'meaning_thai' => 'ตัวเอง', 'meaning_english' => 'oneself'],
            ['radical' => '巾', 'pinyin' => 'jīn', 'pinyin_eng' => 'jin', 'meaning_thai' => 'ผ้า', 'meaning_english' => 'turban, scarf'],
            ['radical' => '干', 'pinyin' => 'gān', 'pinyin_eng' => 'gan', 'meaning_thai' => 'แห้ง', 'meaning_english' => 'dry'],
            ['radical' => '幺', 'pinyin' => 'yāo', 'pinyin_eng' => 'yao', 'meaning_thai' => 'เล็ก', 'meaning_english' => 'short, tiny'],
            ['radical' => '广', 'pinyin' => 'guǎng', 'pinyin_eng' => 'guang', 'meaning_thai' => 'ที่พัก', 'meaning_english' => 'shelter'],
            ['radical' => '廴', 'pinyin' => 'yǐn', 'pinyin_eng' => 'yin', 'meaning_thai' => 'เดิน', 'meaning_english' => 'stride'],
            ['radical' => '廾', 'pinyin' => 'gǒng', 'pinyin_eng' => 'gong', 'meaning_thai' => 'สองมือ', 'meaning_english' => 'two hands'],
            ['radical' => '弋', 'pinyin' => 'yì', 'pinyin_eng' => 'yi', 'meaning_thai' => 'ลูกศร', 'meaning_english' => 'shoot, arrow'],
            ['radical' => '弓', 'pinyin' => 'gōng', 'pinyin_eng' => 'gong', 'meaning_thai' => 'คันธนู', 'meaning_english' => 'bow'],
            ['radical' => '彐 (彑)', 'pinyin' => 'jì', 'pinyin_eng' => 'ji', 'meaning_thai' => 'หัวหมู', 'meaning_english' => 'pig snout'],
            ['radical' => '彡', 'pinyin' => 'shān', 'pinyin_eng' => 'shan', 'meaning_thai' => 'เส้นผม', 'meaning_english' => 'bristle'],
            ['radical' => '彳', 'pinyin' => 'chì', 'pinyin_eng' => 'chi', 'meaning_thai' => 'สองคน', 'meaning_english' => 'step'],
            ['radical' => '心 (忄, ⺗)', 'pinyin' => 'xīn', 'pinyin_eng' => 'xin', 'meaning_thai' => 'หัวใจ', 'meaning_english' => 'heart'],
            ['radical' => '戈', 'pinyin' => 'gē', 'pinyin_eng' => 'ge', 'meaning_thai' => 'หอก', 'meaning_english' => 'halberd'],
            ['radical' => '户 (戶)', 'pinyin' => 'hù', 'pinyin_eng' => 'hu', 'meaning_thai' => 'ประตู', 'meaning_english' => 'door'],
            ['radical' => '手 (扌, 龵)', 'pinyin' => 'shǒu', 'pinyin_eng' => 'shou', 'meaning_thai' => 'มือ', 'meaning_english' => 'hand'],
            ['radical' => '支', 'pinyin' => 'zhī', 'pinyin_eng' => 'zhi', 'meaning_thai' => 'กิ่ง', 'meaning_english' => 'branch'],
            ['radical' => '攴 (攵)', 'pinyin' => 'pū', 'pinyin_eng' => 'pu', 'meaning_thai' => 'ตี', 'meaning_english' => 'rap'],
            ['radical' => '文', 'pinyin' => 'wén', 'pinyin_eng' => 'wen', 'meaning_thai' => 'วัฒนธรรม', 'meaning_english' => 'culture'],
            ['radical' => '斗', 'pinyin' => 'dǒu', 'pinyin_eng' => 'dou', 'meaning_thai' => 'ตวงข้าว', 'meaning_english' => 'dipper'],
            ['radical' => '斤', 'pinyin' => 'jīn', 'pinyin_eng' => 'jin', 'meaning_thai' => 'ขวาน', 'meaning_english' => 'axe'],
            ['radical' => '方', 'pinyin' => 'fāng', 'pinyin_eng' => 'fang', 'meaning_thai' => 'ทิศทาง', 'meaning_english' => 'square'],
            ['radical' => '无 (旡)', 'pinyin' => 'wú', 'pinyin_eng' => 'wu', 'meaning_thai' => 'ไม่มี', 'meaning_english' => 'not'],
            ['radical' => '日', 'pinyin' => 'rì', 'pinyin_eng' => 'ri', 'meaning_thai' => 'ดวงอาทิตย์', 'meaning_english' => 'sun'],
            ['radical' => '曰', 'pinyin' => 'yuē', 'pinyin_eng' => 'yue', 'meaning_thai' => 'กล่าว', 'meaning_english' => 'say'],
            ['radical' => '月', 'pinyin' => 'yuè', 'pinyin_eng' => 'yue', 'meaning_thai' => 'ดวงจันทร์', 'meaning_english' => 'moon'],
            ['radical' => '木', 'pinyin' => 'mù', 'pinyin_eng' => 'mu', 'meaning_thai' => 'ไม้', 'meaning_english' => 'wood'],
            ['radical' => '欠', 'pinyin' => 'qiàn', 'pinyin_eng' => 'qian', 'meaning_thai' => 'ขาด', 'meaning_english' => 'lack'],
            ['radical' => '止', 'pinyin' => 'zhǐ', 'pinyin_eng' => 'zhi', 'meaning_thai' => 'หยุด', 'meaning_english' => 'stop'],
            ['radical' => '歹 (歺)', 'pinyin' => 'dǎi', 'pinyin_eng' => 'dai', 'meaning_thai' => 'ซากศพ', 'meaning_english' => 'death'],
            ['radical' => '殳', 'pinyin' => 'shū', 'pinyin_eng' => 'shu', 'meaning_thai' => 'อาวุธ', 'meaning_english' => 'weapon'],
            ['radical' => '毋 (母, ⺟)', 'pinyin' => 'wú, mǔ', 'pinyin_eng' => 'wu', 'meaning_thai' => 'แม่', 'meaning_english' => 'mother'],
            ['radical' => '比', 'pinyin' => 'bǐ', 'pinyin_eng' => 'mu', 'meaning_thai' => 'เปรียบเทียบ', 'meaning_english' => 'compare'],
            ['radical' => '毛', 'pinyin' => 'máo', 'pinyin_eng' => 'bi', 'meaning_thai' => 'ขน', 'meaning_english' => 'fur'],
            ['radical' => '氏', 'pinyin' => 'shì', 'pinyin_eng' => 'mao', 'meaning_thai' => 'ตระกูล', 'meaning_english' => 'clan'],
            ['radical' => '气', 'pinyin' => 'qì', 'pinyin_eng' => 'shi', 'meaning_thai' => 'อากาศ', 'meaning_english' => 'air'],
            ['radical' => '水 (氵,氺)', 'pinyin' => 'shuǐ', 'pinyin_eng' => 'qi', 'meaning_thai' => 'น้ำ', 'meaning_english' => 'water'],
            ['radical' => '火 (灬)', 'pinyin' => 'huǒ', 'pinyin_eng' => 'shui', 'meaning_thai' => 'ไฟ', 'meaning_english' => 'fire'],
            ['radical' => '爪 (爫)', 'pinyin' => 'zhǎo', 'pinyin_eng' => 'huo', 'meaning_thai' => 'เล็บ', 'meaning_english' => 'claw'],
            ['radical' => '父', 'pinyin' => 'fù', 'pinyin_eng' => 'zhao', 'meaning_thai' => 'พ่อ', 'meaning_english' => 'father'],
            ['radical' => '爻', 'pinyin' => 'yáo', 'pinyin_eng' => 'fu', 'meaning_thai' => 'ลวดลาย', 'meaning_english' => 'double x'],
            ['radical' => '爿', 'pinyin' => 'qiáng', 'pinyin_eng' => 'yao', 'meaning_thai' => 'ผ่าซีก', 'meaning_english' => 'half of a tree trunk'],
            ['radical' => '片', 'pinyin' => 'piàn', 'pinyin_eng' => 'qiang', 'meaning_thai' => 'ชิ้น', 'meaning_english' => 'slice'],
            ['radical' => '牙', 'pinyin' => 'yá', 'pinyin_eng' => 'pian', 'meaning_thai' => 'ฟัน', 'meaning_english' => 'tooth'],
            ['radical' => '牛 (牜, ⺧)', 'pinyin' => 'niú', 'pinyin_eng' => 'ya', 'meaning_thai' => 'วัว', 'meaning_english' => 'cow'],
            ['radical' => '犬 (犭)', 'pinyin' => 'quǎn', 'pinyin_eng' => 'niu', 'meaning_thai' => 'สุนัข', 'meaning_english' => 'dog'],
            ['radical' => '玄', 'pinyin' => 'xuán', 'pinyin_eng' => 'quan', 'meaning_thai' => 'ลึกซึ้ง', 'meaning_english' => 'profound'],
            ['radical' => '玉 (⺩)', 'pinyin' => 'yù', 'pinyin_eng' => 'xuan', 'meaning_thai' => 'หยก', 'meaning_english' => 'jade'],
            ['radical' => '瓜', 'pinyin' => 'guā', 'pinyin_eng' => 'yu', 'meaning_thai' => 'แตง', 'meaning_english' => 'melon'],
            ['radical' => '瓦', 'pinyin' => 'wǎ', 'pinyin_eng' => 'gua', 'meaning_thai' => 'กระเบื้อง', 'meaning_english' => 'tile'],
            ['radical' => '甘', 'pinyin' => 'gān', 'pinyin_eng' => 'wa', 'meaning_thai' => 'หวาน', 'meaning_english' => 'sweet'],
            ['radical' => '生', 'pinyin' => 'shēng', 'pinyin_eng' => 'gan', 'meaning_thai' => 'เกิด', 'meaning_english' => 'life, birth'],
            ['radical' => '用 (甩)', 'pinyin' => 'yòng', 'pinyin_eng' => 'sheng', 'meaning_thai' => 'ใช้', 'meaning_english' => 'use'],
            ['radical' => '田', 'pinyin' => 'tián', 'pinyin_eng' => 'yong', 'meaning_thai' => 'นา', 'meaning_english' => 'field'],
            ['radical' => '疋 (⺪)', 'pinyin' => 'pǐ', 'pinyin_eng' => 'tian', 'meaning_thai' => 'ขา, หน่วยผ้า', 'meaning_english' => 'bolt of cloth'],
            ['radical' => '疒', 'pinyin' => 'chuáng', 'pinyin_eng' => 'pi', 'meaning_thai' => 'ป่วย', 'meaning_english' => 'sickness'],
            ['radical' => '癶', 'pinyin' => 'bō', 'pinyin_eng' => 'chuang', 'meaning_thai' => 'ก้าว', 'meaning_english' => 'footsteps'],
            ['radical' => '白', 'pinyin' => 'bái', 'pinyin_eng' => 'bo', 'meaning_thai' => 'ขาว', 'meaning_english' => 'white'],
            ['radical' => '皮', 'pinyin' => 'pí', 'pinyin_eng' => 'bai', 'meaning_thai' => 'หนัง', 'meaning_english' => 'skin'],
            ['radical' => '皿', 'pinyin' => 'mǐn', 'pinyin_eng' => 'pi', 'meaning_thai' => 'จาน', 'meaning_english' => 'dish'],
            ['radical' => '目', 'pinyin' => 'mù', 'pinyin_eng' => 'min', 'meaning_thai' => 'ตา', 'meaning_english' => 'eye'],
            ['radical' => '矛', 'pinyin' => 'máo', 'pinyin_eng' => 'mu', 'meaning_thai' => 'หอก', 'meaning_english' => 'spear'],
            ['radical' => '矢', 'pinyin' => 'shǐ', 'pinyin_eng' => 'mao', 'meaning_thai' => 'ลูกศร', 'meaning_english' => 'arrow'],
            ['radical' => '石', 'pinyin' => 'shí', 'pinyin_eng' => 'shi', 'meaning_thai' => 'หิน', 'meaning_english' => 'stone'],
            ['radical' => '示 (礻)', 'pinyin' => 'shì', 'pinyin_eng' => 'shi', 'meaning_thai' => 'แสดง', 'meaning_english' => 'spirit'],
            ['radical' => '禸', 'pinyin' => 'róu', 'pinyin_eng' => 'shi', 'meaning_thai' => 'รอยเท้า', 'meaning_english' => 'track'],
            ['radical' => '禾', 'pinyin' => 'hé', 'pinyin_eng' => 'rou', 'meaning_thai' => 'ข้าว', 'meaning_english' => 'grain'],
            ['radical' => '穴', 'pinyin' => 'xué', 'pinyin_eng' => 'he', 'meaning_thai' => 'รู', 'meaning_english' => 'cave'],
            ['radical' => '立', 'pinyin' => 'lì', 'pinyin_eng' => 'xue', 'meaning_thai' => 'ยืน', 'meaning_english' => 'stand'],
            ['radical' => '竹 (⺮)', 'pinyin' => 'zhú', 'pinyin_eng' => 'li', 'meaning_thai' => 'ไผ่', 'meaning_english' => 'bamboo'],
            ['radical' => '米', 'pinyin' => 'mǐ', 'pinyin_eng' => 'zhu', 'meaning_thai' => 'ข้าว', 'meaning_english' => 'rice'],
            ['radical' => '糸 (糹, 纟)', 'pinyin' => 'sī', 'pinyin_eng' => 'mi', 'meaning_thai' => 'เส้นไหม', 'meaning_english' => 'silk'],
            ['radical' => '缶', 'pinyin' => 'fǒu', 'pinyin_eng' => 'si', 'meaning_thai' => 'กระปุก', 'meaning_english' => 'jar'],
            ['radical' => '网 (罒,⺲,罓,⺳)', 'pinyin' => 'wǎng', 'pinyin_eng' => 'fou', 'meaning_thai' => 'ตาข่าย', 'meaning_english' => 'net'],
            ['radical' => '羊 (⺶,⺷)', 'pinyin' => 'yáng', 'pinyin_eng' => 'wang', 'meaning_thai' => 'แกะ', 'meaning_english' => 'sheep'],
            ['radical' => '羽', 'pinyin' => 'yǔ', 'pinyin_eng' => 'yang', 'meaning_thai' => 'ปีก', 'meaning_english' => 'feather'],
            ['radical' => '老 (耂)', 'pinyin' => 'lǎo', 'pinyin_eng' => 'yu', 'meaning_thai' => 'แก่', 'meaning_english' => 'old'],
            ['radical' => '而', 'pinyin' => 'ér', 'pinyin_eng' => 'lao', 'meaning_thai' => 'เครา', 'meaning_english' => 'and'],
            ['radical' => '耒', 'pinyin' => 'lěi', 'pinyin_eng' => 'er', 'meaning_thai' => 'คันไถ', 'meaning_english' => 'plow'],
            ['radical' => '耳', 'pinyin' => 'ěr', 'pinyin_eng' => 'lei', 'meaning_thai' => 'หู', 'meaning_english' => 'ear'],
            ['radical' => '聿 (⺻)', 'pinyin' => 'yù', 'pinyin_eng' => 'er', 'meaning_thai' => 'พู่กัน', 'meaning_english' => 'brush'],
            ['radical' => '肉 (⺼,月)', 'pinyin' => 'ròu', 'pinyin_eng' => 'yu', 'meaning_thai' => 'เนื้อ', 'meaning_english' => 'meat'],
            ['radical' => '臣', 'pinyin' => 'chén', 'pinyin_eng' => 'rou', 'meaning_thai' => 'ข้าราชการ', 'meaning_english' => 'minister'],
            ['radical' => '自', 'pinyin' => 'zì', 'pinyin_eng' => 'chen', 'meaning_thai' => 'ตัวเอง', 'meaning_english' => 'self'],
            ['radical' => '至', 'pinyin' => 'zhì', 'pinyin_eng' => 'zi', 'meaning_thai' => 'ถึง', 'meaning_english' => 'arrive'],
            ['radical' => '臼', 'pinyin' => 'jiù', 'pinyin_eng' => 'zhi', 'meaning_thai' => 'ครก', 'meaning_english' => 'mortar'],
            ['radical' => '舌', 'pinyin' => 'shé', 'pinyin_eng' => 'jiu', 'meaning_thai' => 'ลิ้น', 'meaning_english' => 'tongue'],
            ['radical' => '舛', 'pinyin' => 'chuǎn', 'pinyin_eng' => 'she', 'meaning_thai' => 'ผิดทาง', 'meaning_english' => 'contrary'],
            ['radical' => '舟', 'pinyin' => 'zhōu', 'pinyin_eng' => 'chuan', 'meaning_thai' => 'เรือ', 'meaning_english' => 'boat'],
            ['radical' => '艮', 'pinyin' => 'gèn', 'pinyin_eng' => 'zhou', 'meaning_thai' => 'แข็ง', 'meaning_english' => 'stopping'],
            ['radical' => '色', 'pinyin' => 'sè', 'pinyin_eng' => 'gen', 'meaning_thai' => 'สี', 'meaning_english' => 'color'],
            ['radical' => '艸 (艹)', 'pinyin' => 'cǎo', 'pinyin_eng' => 'se', 'meaning_thai' => 'หญ้า', 'meaning_english' => 'grass'],
            ['radical' => '虍', 'pinyin' => 'hū', 'pinyin_eng' => 'cao', 'meaning_thai' => 'เสือ', 'meaning_english' => 'tiger'],
            ['radical' => '虫', 'pinyin' => 'huǐ', 'pinyin_eng' => 'hu', 'meaning_thai' => 'แมลง', 'meaning_english' => 'insect'],
            ['radical' => '血', 'pinyin' => 'xuè', 'pinyin_eng' => 'hui', 'meaning_thai' => 'เลือด', 'meaning_english' => 'blood'],
            ['radical' => '行', 'pinyin' => 'xíng', 'pinyin_eng' => 'xue', 'meaning_thai' => 'เดิน', 'meaning_english' => 'walk'],
            ['radical' => '衣 (衤)', 'pinyin' => 'yī', 'pinyin_eng' => 'xing', 'meaning_thai' => 'เสื้อผ้า', 'meaning_english' => 'clothes'],
            ['radical' => '襾 (覀)', 'pinyin' => 'yà', 'pinyin_eng' => 'yi', 'meaning_thai' => 'ปิด', 'meaning_english' => 'west'],
            ['radical' => '見', 'pinyin' => 'jiàn', 'pinyin_eng' => 'ya', 'meaning_thai' => 'เห็น', 'meaning_english' => 'see'],
            ['radical' => '角', 'pinyin' => 'jiǎo', 'pinyin_eng' => 'jian', 'meaning_thai' => 'เขา', 'meaning_english' => 'horn'],
            ['radical' => '言 (訁)', 'pinyin' => 'yán', 'pinyin_eng' => 'jiao', 'meaning_thai' => 'พูด', 'meaning_english' => 'speech'],
            ['radical' => '谷', 'pinyin' => 'gǔ', 'pinyin_eng' => 'yan', 'meaning_thai' => 'หุบเขา', 'meaning_english' => 'valley'],
            ['radical' => '豆', 'pinyin' => 'dòu', 'pinyin_eng' => 'gu', 'meaning_thai' => 'ถั่ว', 'meaning_english' => 'bean'],
            ['radical' => '豕', 'pinyin' => 'shǐ', 'pinyin_eng' => 'dou', 'meaning_thai' => 'หมู', 'meaning_english' => 'pig'],
            ['radical' => '豸', 'pinyin' => 'zhì', 'pinyin_eng' => 'shi', 'meaning_thai' => 'สัตว์', 'meaning_english' => 'badger'],
            ['radical' => '貝', 'pinyin' => 'bèi', 'pinyin_eng' => 'zhi', 'meaning_thai' => 'เปลือกหอย', 'meaning_english' => 'shell'],
            ['radical' => '赤', 'pinyin' => 'chì', 'pinyin_eng' => 'bei', 'meaning_thai' => 'แดง', 'meaning_english' => 'red'],
            ['radical' => '走 (赱)', 'pinyin' => 'zǒu', 'pinyin_eng' => 'chi', 'meaning_thai' => 'เดิน', 'meaning_english' => 'run'],
            ['radical' => '足 (⻊)', 'pinyin' => 'zú', 'pinyin_eng' => 'zou', 'meaning_thai' => 'เท้า', 'meaning_english' => 'foot'],
            ['radical' => '身', 'pinyin' => 'shēn', 'pinyin_eng' => 'zu', 'meaning_thai' => 'ร่างกาย', 'meaning_english' => 'body'],
            ['radical' => '車', 'pinyin' => 'chē', 'pinyin_eng' => 'shen', 'meaning_thai' => 'รถ', 'meaning_english' => 'cart'],
            ['radical' => '辛', 'pinyin' => 'xīn', 'pinyin_eng' => 'che', 'meaning_thai' => 'เผ็ด', 'meaning_english' => 'bitter'],
            ['radical' => '辰', 'pinyin' => 'chén', 'pinyin_eng' => 'xin', 'meaning_thai' => 'มังกร', 'meaning_english' => 'morning'],
            ['radical' => '辵 (辶,⻌,⻍)', 'pinyin' => 'chuò', 'pinyin_eng' => 'chen', 'meaning_thai' => 'เดิน', 'meaning_english' => 'walk'],
            ['radical' => '邑 (阝)', 'pinyin' => 'yì', 'pinyin_eng' => 'chuo', 'meaning_thai' => 'เมือง', 'meaning_english' => 'city'],
            ['radical' => '酉', 'pinyin' => 'yǒu', 'pinyin_eng' => 'yi', 'meaning_thai' => 'ไหเหล้า', 'meaning_english' => 'wine'],
            ['radical' => '釆', 'pinyin' => 'biàn', 'pinyin_eng' => 'you', 'meaning_thai' => 'แยกแยะ', 'meaning_english' => 'distinguish'],
            ['radical' => '里', 'pinyin' => 'lǐ', 'pinyin_eng' => 'bian', 'meaning_thai' => 'หมู่บ้าน', 'meaning_english' => 'village'],
            ['radical' => '金 (釒)', 'pinyin' => 'jīn', 'pinyin_eng' => 'li', 'meaning_thai' => 'ทอง', 'meaning_english' => 'gold'],
            ['radical' => '長 (镸)', 'pinyin' => 'cháng', 'pinyin_eng' => 'jin', 'meaning_thai' => 'ยาว', 'meaning_english' => 'long'],
            ['radical' => '門', 'pinyin' => 'mén', 'pinyin_eng' => 'chang', 'meaning_thai' => 'ประตู', 'meaning_english' => 'gate'],
            ['radical' => '阜 (阝)', 'pinyin' => 'fù', 'pinyin_eng' => 'men', 'meaning_thai' => 'เนิน', 'meaning_english' => 'mound'],
            ['radical' => '隶', 'pinyin' => 'dài', 'pinyin_eng' => 'fu', 'meaning_thai' => 'เชื่อม', 'meaning_english' => 'slave'],
            ['radical' => '隹', 'pinyin' => 'zhuī', 'pinyin_eng' => 'dai', 'meaning_thai' => 'นกสั้น', 'meaning_english' => 'short-tailed bird'],
            ['radical' => '雨', 'pinyin' => 'yǔ', 'pinyin_eng' => 'zhui', 'meaning_thai' => 'ฝน', 'meaning_english' => 'rain'],
            ['radical' => '青 (靑)', 'pinyin' => 'qīng', 'pinyin_eng' => 'yu', 'meaning_thai' => 'ฟ้า, เขียว', 'meaning_english' => 'blue, green'],
            ['radical' => '非', 'pinyin' => 'fēi', 'pinyin_eng' => 'qing', 'meaning_thai' => 'ไม่ใช่', 'meaning_english' => 'wrong'],
            ['radical' => '面 (靣)', 'pinyin' => 'miàn', 'pinyin_eng' => 'fei', 'meaning_thai' => 'หน้า', 'meaning_english' => 'face'],
            ['radical' => '革', 'pinyin' => 'gé', 'pinyin_eng' => 'mian', 'meaning_thai' => 'หนัง', 'meaning_english' => 'leather'],
            ['radical' => '韋', 'pinyin' => 'wéi', 'pinyin_eng' => 'ge', 'meaning_thai' => 'หนัง', 'meaning_english' => 'tanned leather'],
            ['radical' => '韭', 'pinyin' => 'jiǔ', 'pinyin_eng' => 'wei', 'meaning_thai' => 'ต้นหอม', 'meaning_english' => 'leek'],
            ['radical' => '音', 'pinyin' => 'yīn', 'pinyin_eng' => 'jiu', 'meaning_thai' => 'เสียง', 'meaning_english' => 'sound'],
            ['radical' => '頁', 'pinyin' => 'yè', 'pinyin_eng' => 'yin', 'meaning_thai' => 'หน้าหนังสือ', 'meaning_english' => 'page'],
            ['radical' => '風', 'pinyin' => 'fēng', 'pinyin_eng' => 'ye', 'meaning_thai' => 'ลม', 'meaning_english' => 'wind'],
            ['radical' => '飛', 'pinyin' => 'fēi', 'pinyin_eng' => 'feng', 'meaning_thai' => 'บิน', 'meaning_english' => 'fly'],
            ['radical' => '食 (飠,饣)', 'pinyin' => 'shí', 'pinyin_eng' => 'fei', 'meaning_thai' => 'กิน', 'meaning_english' => 'eat'],
            ['radical' => '首', 'pinyin' => 'shǒu', 'pinyin_eng' => 'shi', 'meaning_thai' => 'หัว', 'meaning_english' => 'head'],
            ['radical' => '香', 'pinyin' => 'xiāng', 'pinyin_eng' => 'shou', 'meaning_thai' => 'หอม', 'meaning_english' => 'fragrant'],
            ['radical' => '馬', 'pinyin' => 'mǎ', 'pinyin_eng' => 'xiang', 'meaning_thai' => 'ม้า', 'meaning_english' => 'horse'],
            ['radical' => '骨', 'pinyin' => 'gǔ', 'pinyin_eng' => 'ma', 'meaning_thai' => 'กระดูก', 'meaning_english' => 'bone'],
            ['radical' => '高 (髙)', 'pinyin' => 'gāo', 'pinyin_eng' => 'gu', 'meaning_thai' => 'สูง', 'meaning_english' => 'tall'],
            ['radical' => '髟', 'pinyin' => 'biāo', 'pinyin_eng' => 'gao', 'meaning_thai' => 'เส้นผมยาว', 'meaning_english' => 'long hair'],
            ['radical' => '鬥', 'pinyin' => 'dòu', 'pinyin_eng' => 'biao', 'meaning_thai' => 'สู้', 'meaning_english' => 'fight'],
            ['radical' => '鬯', 'pinyin' => 'chàng', 'pinyin_eng' => 'dou', 'meaning_thai' => 'ข้าวหมัก', 'meaning_english' => 'sacrificial wine'],
            ['radical' => '鬲', 'pinyin' => 'gé', 'pinyin_eng' => 'chang', 'meaning_thai' => 'หม้อสามขา', 'meaning_english' => 'cauldron'],
            ['radical' => '鬼', 'pinyin' => 'guǐ', 'pinyin_eng' => 'ge', 'meaning_thai' => 'ผี', 'meaning_english' => 'ghost'],
            ['radical' => '魚', 'pinyin' => 'yú', 'pinyin_eng' => 'gui', 'meaning_thai' => 'ปลา', 'meaning_english' => 'fish'],
            ['radical' => '鳥', 'pinyin' => 'niǎo', 'pinyin_eng' => 'yu', 'meaning_thai' => 'นก', 'meaning_english' => 'bird'],
            ['radical' => '鹿', 'pinyin' => 'lù', 'pinyin_eng' => 'niao', 'meaning_thai' => 'กวาง', 'meaning_english' => 'deer'],
            ['radical' => '麥', 'pinyin' => 'mài', 'pinyin_eng' => 'lu', 'meaning_thai' => 'ข้าวสาลี', 'meaning_english' => 'wheat'],
            ['radical' => '麻', 'pinyin' => 'má', 'pinyin_eng' => 'mai', 'meaning_thai' => 'กัญชา', 'meaning_english' => 'hemp'],
            ['radical' => '黃', 'pinyin' => 'huáng', 'pinyin_eng' => 'ma', 'meaning_thai' => 'เหลือง', 'meaning_english' => 'yellow'],
            ['radical' => '黍', 'pinyin' => 'shǔ', 'pinyin_eng' => 'huang', 'meaning_thai' => 'ข้าวเหนียว', 'meaning_english' => 'millet'],
            ['radical' => '黑', 'pinyin' => 'hēi', 'pinyin_eng' => 'shu', 'meaning_thai' => 'ดำ', 'meaning_english' => 'black'],
            ['radical' => '黹', 'pinyin' => 'zhǐ', 'pinyin_eng' => 'hei', 'meaning_thai' => 'เย็บปัก', 'meaning_english' => 'embroidery'],
            ['radical' => '黽', 'pinyin' => 'mǐn', 'pinyin_eng' => 'zhi', 'meaning_thai' => 'กบ', 'meaning_english' => 'frog'],
            ['radical' => '鼎', 'pinyin' => 'dǐng', 'pinyin_eng' => 'min', 'meaning_thai' => 'กระถางสามขา', 'meaning_english' => 'tripod'],
            ['radical' => '鼓', 'pinyin' => 'gǔ', 'pinyin_eng' => 'ding', 'meaning_thai' => 'กลอง', 'meaning_english' => 'drum'],
            ['radical' => '鼠', 'pinyin' => 'shǔ', 'pinyin_eng' => 'gu', 'meaning_thai' => 'หนู', 'meaning_english' => 'rat'],
            ['radical' => '鼻', 'pinyin' => 'bí', 'pinyin_eng' => 'shu', 'meaning_thai' => 'จมูก', 'meaning_english' => 'nose'],
            ['radical' => '齊', 'pinyin' => 'qí', 'pinyin_eng' => 'bi', 'meaning_thai' => 'พร้อมเพรียง', 'meaning_english' => 'even'],
            ['radical' => '齒', 'pinyin' => 'chǐ', 'pinyin_eng' => 'qi', 'meaning_thai' => 'ฟัน', 'meaning_english' => 'tooth'],
            ['radical' => '龍', 'pinyin' => 'lóng', 'pinyin_eng' => 'chi', 'meaning_thai' => 'มังกร', 'meaning_english' => 'dragon'],
            ['radical' => '龜', 'pinyin' => 'guī', 'pinyin_eng' => 'long', 'meaning_thai' => 'เต่า', 'meaning_english' => 'turtle'],
            ['radical' => '龠', 'pinyin' => 'yuè', 'pinyin_eng' => 'gui', 'meaning_thai' => 'ขลุ่ย', 'meaning_english' => 'flute'],
            ['radical' => '黎', 'pinyin' => 'lí', 'pinyin_eng' => 'yue', 'meaning_thai' => 'ข้าวหอม', 'meaning_english' => 'black millet']
        ]);
    }
}

// 1-200
// please give me this each character in table format number, character, pinyin, pinyin_eng, radical,  english translate, thai translate for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 乂 乜 亍 兀 弋 彳 丫 巳 孑 孓 幺 亓 韦 廿 卅 仄 厄 曰 壬 仃 仉 仂 兮 刈 爻 殳 卞 亢 闩 讣 尹 夬 爿 毋 邗 戋 卉 邛 艽 艿 札 叵 匝 丕 戊 匜 劢 卟 叱 叩 叻 冉 氕 仨 仕 仟 仡 仫 伋 仞 卮 氐 犰 卯 刍 庀 邝 邙 汀 汈 忉 宄 讦 讧 讪 讫 尻 弗 弘 阢 阡 尕 弁 驭 匡 耒 玎 玑 戎 圩 圬 圭 扦 圪 圳 圹 扪 圯 圮 芏 芊 芨 芄 芎 芑 芗 亘 厍 戌 夼 戍 尥 尧 乩 旯 曳 岌 屺 凼 囝 囡 钆 钇 缶 氘 氖 牝 伎 伛 伢 仳 佤 仵 伥 伧 伉 伫 囟 甪 汆 氽 刖 夙 旮 犴 刎 犷 犸 舛 邬 饧 冱 邡 汕 汔 汲 汐 汜 汝 汊 忖 忏 讴 讵 祁 讷 聿 艮 厾 阮 阪 丞 妁 妃 牟 纡 纣 纥 纨 纩 玕 玙 玚 抟 抔 坜 圻 坂 扺 坍 抃 抉 毐 芫 邯 芸 芾 芰 苈 苊

// 201-400
// please give me this each character in table format number, character, pinyin, pinyin_eng, radical,  english translate, thai translate for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 苣 芷 芮 苋 芼 苌 苁 芩 芪 芴 芡 芟 苄 苎 苡 杌 杓 杧 杞 忑 孛 吾 邴 酉 邳 矶 奁 豕 忒 欤 轪 轫 迓 邶 忐 芈 卣 邺 旰 呋 呓 呔 呖 呃 旸 吡 町 虬 呙 呗 吣 吲 岍 帏 岐 岈 岘 岑 岚 兕 囵 囫 钊 钋 钌 迕 氙 氚 岙 佞 邱 佐 伾 攸 佚 佝 佟 佗 伽 彷 佘 佥 孚 豸 坌 肟 邸 奂 劬 狄 狃 狁 邹 饨 饩 饫 饬 亨 庑 庋 疔 疖 肓 闱 闳 闵 闶 羌 炀 沣 沅 沄 沔 沤 沌 沘 沏 沚 汩 汨 汭 沂 汾 沨 汴 汶 沆 沩 沁 泐 怃 忮 怄 忡 忤 忾 怅 忻 忪 怆 忭 忸 诂 诃 祀 祃 诋 诌 诎 诏 诐 诒 屃 孜 陇 阽 阼 陀 陂 陉 妍 妩 妪 妣 妊 妗 妫 妞 姒 妤 邵 劭 刭 甬 邰 矣 纭 纰 纴 纶 纻 纾 玮 玡 玠 玢 玥 玦 甙 盂 忝 匦 邽 坩 垅 抨 拤 拈 坫 垆

// 401-600
// please give me this each character in table format number, character, pinyin, pinyin_eng, radical,  english translate, thai translate for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 抻 拃 拊 坼 拎 坻 坨 坭 抿 拚 坳 耵 耶 苷 苯 苤 茏 苴 苜 苒 苘 茌 苻 苓 茚 茆 茑 苑 茓 茔 茕 苠 茀 苕 枥 枇 杪 杳 枘 枧 杵 枨 枞 枋 杻 杷 杼 矸 矻 矽 砀 刳 瓯 殁 郏 轭 郅 鸢 盱 昊 杲 昃 咂 呸 昕 昀 旻 昉 炅 咔 畀 虮 迪 呷 黾 呱 呤 咚 咛 咄 呶 呦 咝 岵 岢 岿 岬 岫 帙 岣 峁 刿 峂 迥 岷 剀 帔 峄 沓 囹 罔 钍 钎 钏 钐 钒 钔 钕 钗 邾 迭 迮 牦 竺 迤 佶 佬 佴 侑 佰 侉 臾 岱 侗 侏 侩 佻 佾 侪 佼 佯 侬 帛 阜 侔 郈 徂 郐 郄 怂 籴 戗 肼 朊 肽 肱 肫 肭 肷 剁 迩 郇 狉 狙 狎 狝 狍 狒 咎 炙 枭 饯 饳 饴 冽 冼 庖 疠 疝 疡 兖 庚 妾 於 劾 炜 炖 炝 炔 泔 沭 泷 泸 泱 泅 泗 泠 泜 泺 泃 泖 泫 泮 沱 泯 泓 泾 怙 怵 怦

// 601-800
// please give me this each character in table format number, character, pinyin, pinyin_eng, radical,  english translate, thai translate for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 怛 怏 怍 怩 怫 怊 怿 怡 宕 穸 穹 宓 诓 诔 诖 诘 戾 诙 戽 郓 祆 祎 祉 诛 诜 诟 诠 诣 诤 诨 诩 鸤 戕 孢 亟 陔 卺 妲 妯 姗 妮 帑 弩 孥 驽 迦 迢 迨 绀 绁 绂 驵 驷 驸 绉 驺 绋 绌 驿 骀 甾 砉 耔 珏 珐 珂 珑 玳 珀 顸 珉 珈 韨 拮 垭 挝 垣 垯 挞 垤 赳 贲 垱 垌 哉 垲 挢 埏 郝 垍 垧 垓 垟 垞 挦 垠 拶 茜 荙 荑 贳 荛 荜 茈 茼 莒 茱 莛 茯 荏 荇 荃 荟 荀 茗 茭 茨 垩 茳 荥 荦 荨 茛 荩 荪 荫 茹 荬 荭 柰 栉 柯 柘 栊 柩 枰 栌 柙 枵 柚 枳 柞 柝 栀 柃 柢 栎 枸 柈 柁 柽 剌 郚 剅 酊 郦 砗 砑 砘 砒 斫 砭 砜 奎 耷 虺 殂 殇 殄 殆 轱 轲 轳 轵 轶 轷 轸 轹 轺 虿 毖 觇 尜 哐 眄 眍 郢 眇 眊 昽 眈 咭 禺 哂 咴 曷 昴 昱 咦 哓

// 801-1000
// please give me this each character in table format number, character, pinyin, pinyin_eng, radical,  english translate, thai translate for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 哔 畎 毗 呲 胄 畋 畈 虼 虻 咣 哕 剐 郧 咻 囿 咿 哌 哙 哚 咯 咩 咤 哝 哏 哞 峙 峣 罘 帧 峒 峤 峋 峥 峧 帡 贶 贻 钘 钚 钛 钡 钣 钤 钨 钪 钫 钬 钭 钯 矧 氡 氟 牯 郜 秭 竽 笈 笃 俦 俨 俅 俪 叟 垡 牮 俣 俚 俜 皈 禹 俑 俟 逅 徇 徉 舢 舣 俞 弇 郗 俎 卻 爰 郛 瓴 胨 胩 胪 胛 胂 胙 胍 胗 胝 朐 胫 鸨 匍 狨 狯 飐 飑 狩 狲 訇 訄 逄 昝 饷 饸 饹 饻 胤 孪 娈 庤 弈 庥 疬 疣 疥 疭 疢 庠 竑 彦 闼 闾 闿 羑 籼 酋 兹 炳 炻 炟 炽 炯 烀 炷 烃 洱 洹 洧 洌 浃 泚 浈 浉 洇 洄 洙 洑 洎 洫 浍 洮 洵 洚 洺 洨 浐 洴 洣 浒 浔 浕 洳 恸 恹 恫 恺 恻 恂 恪 恽 宥 宬 窀 扃 袆 衲 衽 衿 袂 祛 祜 祓 祚 诮 祗 祢 诰 诳 鸩 昶 郡 咫 弭 牁

// 1001-1200
// please give me this each character in table format number, character, pinyin, pinyin_eng, radical,  english translate, thai translate for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 胥陛陟陧姞娅娆姝姽姣姘姹怼羿炱癸矜绔骁骅绗绚彖绛骈耖挈恝珥珙顼珰珽珩珧珣珞琤珲敖恚埔埕埘埚埙挹耆耄捋埒贽垸捃盍莰茝莆莳莴莪莠莓莜莅荼莶莩荽莸荻莘莎莞莨莙鸪莼栲栳郴桓桡桎桢桄桤梃栝桕桁桧栒栟桉栩逑逋彧鬲豇酐酎酏逦厝孬砝砹砺砧砷砟砼砥砣硁恧剞砻轼轾辀辁辂鸫趸剕龀鸬虔逍眬唛晟眩眙唝哧哳哽唔晔晁晏晖鸮趵趿畛蚨蚍蚋蚬蚝蚧唢圄唣唏盎唑帱崂崃罡罟峨峪觊赅赆钰钲钴钵钷钹钺钼钽钿铀铂铄铈铉铊铋铌铍铎眚氩氤氦毪舐秣盉

// number 1201-1400
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 笄笕笊笫笏俸倩俵倻偌俳俶倬倏恁倭倪俾倜隼隽倞倓倌倥臬皋郫倨衄颀徕舨舫瓞釜奚鬯衾鸰胱胴胭脍脎胲胼朕脒胺鸱玺鱽鸲狴狷猁狳猃狺逖狻桀袅眢饽馀凇栾挛勍亳疳疴疽疸痄痈疱疰痃痂痉衮凋颃恣旆旄旃阃阄阆恙桊敉粑朔郸烜烨烩烊剡郯烬浡涑浯涞涟娑涅涠浞涓涢浥涔浜浠浼浣涘浚悖悚悭悝悃悒悌悢悛宸窅窈剜诹扅诼冢袪袗袢袯祯祧冥诿谀谂谄谇屐屙陬勐奘疍牂蚩陲陴烝姬娠娌娉娟娲娥娴娣娓婀砮哿畚逡剟绠骊绡骋绤绥绦骍绨骎邕鸶彗耜焘舂琎琏琇

// number 1401-1600
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 掭揶埴掎埼埯捯焉掳掴埸堌赧捭晢逵埝堋堍掬鸷掊堉捩掮悫埭埽掇掼聃聆聍菁菝萁菥菘堇萘萋勩菽菖萜萸萑菂棻菔菟萏萃菼菏菹菪菅菀萦菰菡梽梵梾梏觋桴桷梓棁桫棂郾匮敕豉鄄酞酚厣戛硎硭硒硖硗硐硚硇硌鸸瓠匏厩龚殒殓殍赉雩辄堑龁眭唪眦啧晡眺眵眸圊啪喏喵啉勖晞晗啭趼趺啮跄蚶蛄蛎蚰蚺蛊圉蚱蛏蚴鄂啁啕唿啐唼唷啴啖啵啶啷唳啜帻崦帼崮帷崟崤崞崆崛赇赈铑铒铕铗铘铙铚铞铟铠铢铤铥铧铨铩铪铫铬铮铯铰铱铳铴铵铷氪牾鸹稆秾逶笺筇笸笪笮笱

// number 1601-1800
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 笠笥笳笾笞偾鸺偃偕偈偲偬偻皑皎鸻徜舸舻舳舴鸼龛瓻豚脶脞脬脘脲脧匐鱾猗猡猊猞猄猝斛觖猕馗馃馄鸾孰庹庼庾庳痔痍疵翊旌旎袤阇阈阉阊阋阌阍阏羚羝羟粝粕焐烯焓烽焖烷烺焌渍渚淇淅淞渎涿淖挲淏淠涸渑淦淝淬涪淙涫渌淄惬悻悱惝惘悸惟惆惚惇寅逭窕谌谏扈皲谑袼裈裉祲谔谕谖谗谙谛谝敝逯艴隋郿隈粜隍隗婧婊婞婳婕娼婢婵胬袈翌恿欸绫骐绮绯骒绲骓绶绹绺绻绾骖缁耠琫琵琶琪瑛琦琥琨靓琰琮琯琬琛琚辇鼋揳堞搽塃揸揠堙趄揾颉塄揿堠耋揄蛰蛩

// number 1801-2000
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 絷塆揞揎摒揆掾葜聒葑葚靰靸葳蒇蒈葺蒉葸萼蓇萩葆葩葶蒌葓蒎萱葖戟葭楮棼椟棹椤棰椑鹀赍椋椁棬楗棣椐鹁覃酤酢酡酦鹂觌硪硷厥殚殛雯辊辋椠辌辍辎斐黹牚睐睑睇睃戢喋嗒喃喱喹晷喈跖跗跞跚跎跏跆蛱蛲蛭蛳蛐蛞蛴蛟蛘蛑畯喁喟斝啾嗖喤喑嗟喽嗞喀喔喙嵘嵖崴遄詈嵎崽嵚嵬嵛翙嵯嵝嵫幄嵋赑铹铻铼铽铿锃锂锆锇锊锎锏锑锒锓锔锕掣矬氰毳毽犊犄犋鹄犍颋嵇稃稂筘筚筜筅筵筌傣傈舄傥傧遑皓皖傩遁徨舾畲弑颌翕釉鹆舜貂腈腓腆腴腑腙腚腱腒鱿鲀鲂鲃

// number 2001-2200
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 颍猢猹猥飓觞觚猸猱飧馇馉馊亵脔裒廋斌痣痨痦痞痤痫痧鄌赓竦瓿啻颏鹇阑阒阕粞遒孳焯焜焱鹈湛渫湮湎湝湨湜渭湍湫溲湟溆渝湲溠溇湔湉渲渥湄滁愠惺愦惴愀愎愔喾寐谟扉棨扊裢裎裣裥祾祺祼谠禅禄幂谡谥谧塈遐孱弼巽骘媪媛婷巯毵翚皴婺骛缂缃缄彘缇缈缌缏缑缒缗骙飨耢瑚瑁瑀瑜瑗瑄瑕遨骜瑙遘韫髡塥塬鄢趔趑摅摁赪塮蜇搋搒搐搛搠摈彀毂搌搦搡蓁戡蓍鄞靳蓐蓦鹋蒽蓓蓊蒯蓟蓑蒺蓠蒟蒡蒹蒴蒗蓂蓥颐蓣楠楂楝楫榀楸椴槌楯榇榈槎榉楦楹椽裘剽甄酮

// number 2201-2400
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 酰酯酩蜃碛碓碚碇碜鹌辏辒龃龅龆觜訾粲虞睚嗪睫韪嗷嗉睨睢雎睥嘟嗑嗫嗬嗔嗝戥嗄煦暅遢暌跬跶跸跐跣跹跻跤蛸蜎蜊蜍蜉蜣畹嗣嗥嗲嗳嗌嗍嗵罨嵊嵩嵴骰锖锗锘锛锜锝锞锟锢锧锪锫锩锬锱雉氲犏歃稞稗稔筠筢筮筻筲筼筱牒煲鹎敫僇徭愆艄觎毹貊貅貉颔腠腩腼腽腭腧塍媵詹鲅鲆鲇鲈鲉鲊稣鲋鲌鲍鲏鲐鹐飔飕觥遛馌馐鹑亶廒瘃痱痼痿瘐瘁瘅瘆鄘麂鄣歆旒雍阖阗阘阙羧豢粳猷煳煜煨煅煊煸煺滟溱溘滠漭滢滇溥溧溽裟溻溷溦滗滫溴滏滃滦溏滂溟滘滍滪愫慑慥

// number 2401-2600
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 慊鲎骞窦窠窣裱褚裼裨裾裰禊谩谪谫媾嫫媲嫒嫔媸缙缜缛辔骝缟缡缢缣骟耥璈瑶瑭瑢獒觏慝嫠韬墈摽墁撂摞撄翥踅銎摭墉墒榖撖摺綦蔷靺靼鞅靽鞁靿蔌甍蔸蓰蔹蔡蔟蔺戬蕖蔻蓿斡鹕嘏蓼榧槚槛榻榫槜榭槔槁槟槠榷榍僰酽酾酲酶酴酹厮碶碡碣碲碹碥劂臧豨殡霆霁辗蜚裴翡龇龈睿夥瞅瞍睽嘞嘌嘎暝踌踉跽蜞蜥蜮蜾蝈蜴蜱蜩蜷蜿螂蜢嘘嘡鹗嘣嘤嘚嗾嘧罴罱嶂幛赙罂骷骶鹘锴锶锷锸锽锾锵锿镁镂镃镄镅犒箐箦箧箸箨箬箅箪箔箜箢箓毓僖儆僳僭僬劁僦僮魃魆睾艋

// number 2601-2800
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 鄱膈膑鲑鲔鲙鲚鲛鲟獐獍飗觫雒夤馑銮塾麽廙瘌瘗瘊瘥瘘瘙廖韶旖膂阚鄯鲞粼粽糁槊鹚熘煽熥潢潆漤漕滹漯漶潋潴漪漉漳澉潍慵搴窬窨窭寤肇綮谮褡褙褓褛褊禚谯谰谲暨屣鹛嫣嫱嫖嫦嫚嫘嫜嫪鼐翟瞀鹜骠缥缦缧骢缪缫耦耧瑾璜璀璎璁璋璇璆奭髯髫撷撅赭墦撸鋆撙撺墀聩觐鞑蕙鞒蕈蕨蕤蕞蕺瞢劐蕃蕲蕰赜鼒槿樯槭樗樘槲鹝醌醅靥魇餍磊磔磙磉殣慭霄霈辘龉龊觑瞌瞑嘻嘭噎噶颙暹踔踝踟踬踮踣踯踺踞蝽蝾蝻蝰蝮螋蝓蝣蝼蝤噗嘬颚噍噢噙噜噌噀噔颛幞幡嶓嶙

// number 2801-3000
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 嶝骺骼骸镆镈镉镋镌镍镎镏镑镒镓镔稷箴篑篁篌篆牖儇儋徵磐虢鹞鹟滕鲠鲡鲢鲣鲥鲦鲧鲩鲪鲬橥獗獠觯鹠馓馔麾廛瘛瘼瘢瘠齑鹡羯羰糇遴糌糍糈糅翦鹣熜熵熠澍澌潵潸鲨潲鋈潟潼潽潺潏憬憧寮窳谳褴褫禤谵屦勰戮蝥缬缮缯骣畿耩耨耪璞璟靛璠璘聱螯髻髭髹擀熹甏擐擞磬鄹颞蕻鞘黇颟薤薨檠薏蕹薮薜薅樾橛橇樵檎橹橦樽樨橼墼橐翮醛醐醍醚醑觱磺磲赝飙殪霖霏霓錾辚臻遽氅瞟瞠瞰嚄嚆噤暾曈蹀蹅踶踹踵踽蹉蹁螨蟒螈螅螭螗螠噱噬噫噻噼幪罹圜镖镗镘镚镛

// number 3001-3200
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 镝镞镠氇氆憩穑穄篝篚篥簉篦篪盥劓翱魉魈徼歙盦膪螣膦膙鲭鲮鲯鲰鲱鲲鲳鲴鲵鲷鲺鲹鲻獴獭獬邂憝亸鹧廨赟癀瘭瘰廪瘿瘵瘴癃瘳斓麇麈嬴壅羲糗瞥甑燠燔燧燊燏濑濉潞澧澴澹澥澶濂澼憷黉褰寰窸褶禧嬖犟隰嬗鹨翯颡缱缲缳璨璩璐璪螫擤觳罄擢藉薹鞡薷薰藓藁檑檄懋醢翳繄礅磴鹩龋龌豳壑黻瞵嚅蹑蹒蹊蹓蹐螬螵疃螳蟑嚓羁罽罾嶷黜黝髁髀镡镢镤镥镦镧镨镩镪镫罅黏簧簌篾簃篼簏簖簋鼢黛鹪鼾皤魍艚龠繇貘邈貔臌膻臁臆臃鲼鲽鲾鳀鳁鳂鳃鳅鳆鳇鳈鳉鳊獯

// number 3201-3462
// please give me this each character in table format (number, character, pinyin, pinyin_eng, radical,  english translate, thai translate) for these Chinese characters which pinyin_eng mean pinyin that have no tone sign

// 螽燮鹫襄縻膺癍麋馘懑濡濮濞濠濯蹇謇邃襕襁檗甓擘孺隳嬷蟊鹬鍪鏊鳌鬹鬈鬃瞽鞯鞨鞫鞧鞣藜藠藩鹲檫檵醪蹙礞礓礌燹餮蹩瞿曛颢曜躇鹭蹢蹜蟛蟪蟠蟮嚚鹮黠黟髅髂镬镭镯镱馥簠簟簪簦鼫鼬鼩雠艟臑鳎鳏鳐鳑鹱癔癜癖糨冁瀍瀌鎏懵彝邋鬏攉鞲鞴藿蘧蘅麓醭醮醯礤酃霪霭黼嚯蹰蹶蹽蹼蹯蹴蹾蹿蠖蠓蠋蟾蠊巅黢髋髌镲籀籁鳘齁魑艨鼗鳓鳔鳕鳗鳙鳚麒鏖蠃羸瀚瀣瀛襦谶襞骥缵瓒馨蘩蘖蘘醵醴霰颥酆矍曦躅鼍巉黩黥镳镴黧纂鼯犨臜鳜鳝鳟獾瀹瀵孀骧耰瓘鼙醺礴礳颦曩黯鼱鳡鳢癫麝赣夔爝灏禳鐾羼蠡耲耱懿韂鹳糵蘼霾氍饕躔躐髑镵穰鳤饔鬻鬟趱攫攥颧躜鼹鼷癯麟蠲蠹醾躞衢鑫灞襻纛鬣攮囔馕戆蠼爨齉
