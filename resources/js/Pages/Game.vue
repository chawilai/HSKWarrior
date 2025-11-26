<script setup lang="ts">
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { ref, reactive, computed, watch } from "vue";


type SkillId =
  | "sword"
  | "fireball"
  | "shield"
  | "quick"
  | "dragon"
  | "manaCharge"
  | "combo"
  | "counter"
  | "freeze"
  | "poison"
  | "spirit"
  | "ultimate"
  | "confuse"
  | "vocabBomb"
  | "grammarTrap"
  | "lightbeam"
  | "stealth"
  | "summonSeal"
  | "heal"
  | "bossChallenge";

/* -----------------------------
   PLAYER / BOSS STATE
------------------------------ */
const selectedHSK = ref<1 | 2 | 3 | 4>(1);



const player = reactive({
  hp: 100,
  maxHp: 100,
  mana: 100,
  maxMana: 100,
  consecutiveCorrect: 0,
});


const bossState = reactive({
  currentHSK: 1 as 1 | 2 | 3 | 4,
  bossHP: 200,
  bossMaxHP: 200,
  phase: 1,
});

/* -----------------------------
   WAVES BEFORE BOSS
------------------------------ */
const waves = ref([
  { id: 1, name: "ลูกกระจ๊อก 1", hp: 30, maxHp: 30, done: false },
  { id: 2, name: "ลูกกระจ๊อก 2", hp: 30, maxHp: 30, done: false },
  { id: 3, name: "ลูกกระน้องสุดท้าย", hp: 45, maxHp: 45, done: false },
]);

const currentWaveIndex = ref(0);
const inBossFight = computed(() => waves.value.every((w) => w.done));

/* -----------------------------
   SKILL SYSTEM
------------------------------ */
const activeSkill = ref<SkillId | null>(null);
const skillQuestion = ref<any>(null);
const message = ref<string | null>(null);

const skillAnswer = ref(""); // <— ตัวนี้หายไป ต้องเพิ่ม!
const tempOrder = ref<string[]>([]);


// Quick Slash timer
const quickTimer = ref<number | null>(null);
const quickTimeLeft = ref(0);

/* -----------------------------
   VOCAB BANK
------------------------------ */
const vocabBank = {
  1: [
    { han: "朋友", th: "เพื่อน" },
    { han: "我", th: "ฉัน/ผม" },
    { han: "爱", th: "รัก" },
    { han: "爸爸", th: "พ่อ" },
    { han: "不", th: "ไม่" },
    { han: "吃", th: "กิน" },
    { han: "大", th: "ใหญ่" },
    { han: "几", th: "เท่าไหร่" },
    { han: "老师", th: "ครู" },
    { han: "妈妈", th: "แม่" },
  ],
  2: [{ han: "学习", th: "เรียน/ศึกษา" },
    { han: "帮助", th: "ช่วยเหลือ" },
    { han: "不满", th: "ไม่พอใจ" },
    { han: "参加", th: "เข้าร่วม" },
    { han: "大家", th: "ทุกคน" },
    { han: "大人", th: "ผู้ใหญ่" },
    { han: "单位", th: "หน่วยงาน" },
    { han: "等于", th: "เท่ากับ" },
    { han: "分钟", th: "นาที" }
  ],
  3: [{ han: "爱好", th: "งานอดิเรก" },
    { han: "冬", th: "ฤดูหนาว" },
    { han: "动物", th: "สัตว์" },
    { han: "锻炼", th: "ออกกำลังกาย" },
    { han: "公斤", th: "กิโลกรัม" },
    { han: "关系", th: "ความสัมพันธ์" },
    { han: "关心", th: "กังวล" },
    { han: "国家", th: "ประเทศ" },
    { han: "过去", th: "อดีต" }],
  4: [{ han: "按时", th: "ตรงเวลา" },
    { han: "百分之", th: "เปอร์เซ็นต์" },
    { han: "保证", th: "รับประกัน" },
    { han: "标准", th: "มาตรฐาน" },
    { han: "表格", th: "แบบฟอร์ม" },
    { han: "表演", th: "ประสิทธิภาพ" },
    { han: "餐厅", th: "ร้านอาหาร" },
    { han: "厕所", th: "ห้องน้ำ" },
    { han: "出生", th: "เกิด" }],
};

/* -----------------------------
   UTILITY
------------------------------ */
function applyDamageToCurrentTarget(dmg: number) {
  if (!inBossFight.value) {
    const w = waves.value[currentWaveIndex.value];
    w.hp = Math.max(0, w.hp - dmg);
    if (w.hp === 0) {
      w.done = true;
      message.value = `เอาชนะ ${w.name} แล้ว!`;
      const next = waves.value.findIndex((x) => !x.done);
      if (next >= 0) currentWaveIndex.value = next;
    }
  } else {
    bossState.bossHP = Math.max(0, bossState.bossHP - dmg);
    message.value = `ศัตรูโดน ${dmg} หน่วย!`;
  }
}

function enemyAttack(dmg: number) {
  player.hp = Math.max(0, player.hp - dmg);
  if (player.hp === 0) message.value = "คุณแพ้แล้ว... รีสตาร์ทเพื่อเล่นใหม่!";
}

function consumeMana(cost: number) {
  if (player.mana < cost) {
    message.value = "มานาไม่พอ!";
    return false;
  }
  player.mana -= cost;
  return true;
}

function clearSkillState() {
  activeSkill.value = null;
  skillQuestion.value = null;
  skillAnswer.value = "";
  tempOrder.value = [];

  if (quickTimer.value) {
    clearInterval(quickTimer.value);
    quickTimer.value = null;
  }
  quickTimeLeft.value = 0;
}

/* -----------------------------
   SKILLS
------------------------------ */

// 1) Sword Attack
function startSwordAttack() {
  if (!consumeMana(5)) return;
  activeSkill.value = "sword";

  const bank = vocabBank[bossState.currentHSK];
  const q = bank[Math.floor(Math.random() * bank.length)];
  skillQuestion.value = {
    type: "vocab-translate",
    prompt: `แปลคำ: ${q.han}`,
    answer: q.th,
  };
}

function submitSwordAnswer(ans: string) {
  const q = skillQuestion.value;
  if (!q) return;

  if (ans.trim() === q.answer) {
    applyDamageToCurrentTarget(10);
    player.consecutiveCorrect++;
    message.value = "ตอบถูก! -10 HP";
  } else {
    enemyAttack(5);
    player.consecutiveCorrect = 0;
    message.value = "ตอบผิด -5 HP";
  }
  clearSkillState();
}

// 2) Fireball – reorder
const fireballQuestionsHSK = {
  hsk1: [
    {
      pieces: ["我", "是", "老师"],
      correct: ["我", "是", "老师"]
    },
    {
      pieces: ["你", "喝", "水"],
      correct: ["你", "喝", "水"]
    },
    {
      pieces: ["她", "很", "漂亮"],
      correct: ["她", "很", "漂亮"]
    },
    {
      pieces: ["我们", "在", "中国"],
      correct: ["我们", "在", "中国"]
    }
  ],

  hsk2: [
    {
      pieces: ["我", "正在", "学习", "中文"],
      correct: ["我", "正在", "学习", "中文"]
    },
    {
      pieces: ["他们", "常常", "去", "图书馆"],
      correct: ["他们", "常常", "去", "图书馆"]
    },
    {
      pieces: ["你", "为什么", "不", "来"],
      correct: ["你", "为什么", "不", "来"]
    }
  ],

  hsk3: [
    {
      pieces: ["我", "觉得", "这个", "故事", "很", "有趣"],
      correct: ["我", "觉得", "这个", "故事", "很", "有趣"]
    },
    {
      pieces: ["你", "应该", "多", "锻炼"],
      correct: ["你", "应该", "多", "锻炼"]
    },
    {
      pieces: ["他", "每天", "骑", "自行车", "上班"],
      correct: ["他", "每天", "骑", "自行车", "上班"]
    }
  ]
};



function getRandomHSKQuestion(level: number) {
  const key = `hsk${level}` as keyof typeof fireballQuestionsHSK;
  const list = fireballQuestionsHSK[key];
  if (!list || list.length === 0) return null;
  const idx = Math.floor(Math.random() * list.length);
  return list[idx];
}



function startFireball() {
  if (!consumeMana(10)) return;

  activeSkill.value = "fireball";

  const q = getRandomHSKQuestion(Number(selectedHSK.value));
  if (!q) {
    message.value = "ไม่มีคำถามสำหรับระดับนี้!";
    return;
  }

  skillQuestion.value = {
    type: "reorder",
    promptParts: [...q.pieces].sort(() => Math.random() - 0.5),
    correct: q.correct
  };

  skillAnswer.value = "";
  tempOrder.value = [];
}



function submitFireballAnswer(order: string[]) {
  const q = skillQuestion.value;

  if (order.join(" ") === q.correct.join(" ")) {
    applyDamageToCurrentTarget(15);
    player.consecutiveCorrect++;
    message.value = "🔥 ไฟกระแทก -15 HP";
  } else {
    enemyAttack(15);
    player.consecutiveCorrect = 0;
    message.value = "ไฟย้อนกลับ -15 HP";
  }
  clearSkillState();
}

// 3) Shield – find wrong word
function startShield(sentence = "我 明天 去 中国了") {
  activeSkill.value = "shield";

  const words = sentence.split(" ");

  skillQuestion.value = {
    type: "find-wrong",
    prompt: sentence,
    choices: words,
    wrongIndex: 3,
  };
}

function submitShieldAnswer(idx: number) {
  const q = skillQuestion.value;
  if (idx === q.wrongIndex) {
    (player as any).hasShield = true;
    message.value = "Shield Block! ลดดาเมจ 50%!";
  } else {
    enemyAttack(20);
    message.value = "เลือกผิด -20 HP";
  }
  clearSkillState();
}

// 4) Quick Slash – timer
const quickSlashQuestionsHSK = {
  hsk1: [
    {
      prompt: "ลักษณนามของ 只 คืออะไร?",
      choices: ["คำช่วยสัตว์", "ลักษณนามทั่วไป", "ลักษณนามคู่", "คำถาม"],
      correctIndex: 0,
    },
    {
      prompt: "ลักษณนามของ 个 คืออะไร?",
      choices: ["คำช่วยสัตว์", "ลักษณนามทั่วไป", "ลักษณนามคู่", "คำถาม"],
      correctIndex: 1,
    }
  ],
  hsk2: [
    {
      prompt: "ลักษณนามของ 本 ใช้กับอะไร?",
      choices: ["หนังสือ", "คน", "สัตว์", "สิ่งของทั่วไป"],
      correctIndex: 0,
    },
    {
      prompt: "ลักษณนามของ 张 ใช้กับอะไร?",
      choices: ["แผ่น/กระดาษ/โต๊ะ", "สัตว์", "คน", "สิ่งของ"],
      correctIndex: 0,
    }
  ],
  hsk3: [
    {
      prompt: "ลักษณนามของ 条 ใช้กับอะไร?",
      choices: ["สัตว์", "เส้น/ปลา/สิ่งยาวๆ", "คน", "หนังสือ"],
      correctIndex: 1,
    },
    {
      prompt: "ลักษณนามของ 辆 ใช้กับอะไร?",
      choices: ["ยานพาหนะ", "สัตว์", "หนังสือ", "เสื้อผ้า"],
      correctIndex: 0,
    }
  ]
};

function getRandomQuickQuestion(level: number) {
  const key = `hsk${level}` as keyof typeof quickSlashQuestionsHSK;
  const list = quickSlashQuestionsHSK[key];
  if (!list || list.length === 0) return null;
  const idx = Math.floor(Math.random() * list.length);
  return list[idx];
}


function startQuickSlash() {
  if (!consumeMana(15)) return;
  activeSkill.value = "quick";

  const q = getRandomQuickQuestion(Number(selectedHSK.value));
  if (!q) {
    message.value = "ไม่มีคำถามสำหรับระดับนี้!";
    return;
  }

  skillQuestion.value = {
    type: "quick-mcq",
    prompt: q.prompt,
    choices: q.choices,
    correctIndex: q.correctIndex,
  };

  quickTimeLeft.value = 3;

  quickTimer.value = setInterval(() => {
    quickTimeLeft.value--;
    if (quickTimeLeft.value <= 0) {
      clearInterval(quickTimer.value!);
      enemyAttack(10);
      message.value = "ช้าไป -10 HP";
      clearSkillState();
    }
  }, 1000) as unknown as number;
}


function submitQuickAnswer(idx: number) {
  const q = skillQuestion.value;
  if (quickTimer.value) clearInterval(quickTimer.value);

  if (idx === q.correctIndex) {
    applyDamageToCurrentTarget(30);
    player.consecutiveCorrect++;
    message.value = "⚡ เร็วสุด! -30 HP";
  } else {
    enemyAttack(10);
    message.value = "ผิด -10 HP";
    player.consecutiveCorrect = 0;
  }
  clearSkillState();
}

// 5) Dragon Roar – Pinyin

const dragonQuestions = {
  hsk1: [
    {
      prompt: "เสียงอ่าน 学习",
      choices: ["xuéxí", "xuéjí", "juéxí"],
      correct: "xuéxí",
    },
    {
      prompt: "เสียงอ่าน 中国",
      choices: ["zhōngguó", "zhǒngguō", "zhōnggǔo"],
      correct: "zhōngguó",
    },
    {
      prompt: "เสียงอ่าน 老师",
      choices: ["lǎoshī", "láoshì", "lǎoshǐ"],
      correct: "lǎoshī",
    },
  ],

  hsk2: [
    {
      prompt: "เสียงอ่าน 认识",
      choices: ["rènshí", "rénshì", "rènshì"],
      correct: "rènshí",
    },
    {
      prompt: "เสียงอ่าน 请问",
      choices: ["qǐngwèn", "qíngwén", "qíngwèn"],
      correct: "qǐngwèn",
    },
  ],

  hsk3: [
    {
      prompt: "เสียงอ่าน 重要",
      choices: ["zhòngyào", "zhōngyào", "zhòngyāo"],
      correct: "zhòngyào",
    },
    {
      prompt: "เสียงอ่าน 机会",
      choices: ["jīhuì", "jíhuì", "jīhuí"],
      correct: "jīhuì",
    },
  ],
};


function getDragonQuestion(hskKey) {
  const list = dragonQuestions[hskKey];
  const idx = Math.floor(Math.random() * list.length);
  return list[idx];
}

function startDragon() {
  if (!consumeMana(15)) return;
  activeSkill.value = "dragon";

  // ดึงค่าจาก selectedHSK แล้วแปลงให้เป็น hsk1, hsk2, hsk3
  let raw = String(selectedHSK.value).replace(/\s+/g, "").toLowerCase();

  // รองรับกรณี selectedHSK เป็นตัวเลข เช่น 1 → hsk1
  if (/^[0-9]+$/.test(raw)) {
    raw = "hsk" + raw;
  }

  // ถ้า key ไม่มี → fallback เป็น hsk1
  const hskKey = dragonQuestions[raw] ? raw : "hsk1";

  skillQuestion.value = {
    type: "pinyin-mcq",
    ...getDragonQuestion(hskKey),
  };
}



function submitDragon(choice: string) {
  const q = skillQuestion.value;

  if (choice === q.correct) {
    applyDamageToCurrentTarget(20);
    message.value = "🐉 มังกรคำราม -20 HP";
  } else {
    enemyAttack(20);
    message.value = "เสียงผิด -20 HP";
  }
  clearSkillState();
}

// 6) Mana Charge
const manaChargeQuestions = {
  hsk1: [
    {
      prompt: "我明天__北京。",
      choices: ["去", "在", "到", "是"],
      correct: "去",
    },
    {
      prompt: "他__医生。",
      choices: ["是", "在", "有", "到"],
      correct: "是",
    },
  ],

  hsk2: [
    {
      prompt: "你__喝茶还是咖啡？",
      choices: ["要", "在", "是", "会"],
      correct: "要",
    },
    {
      prompt: "我__过中国。",
      choices: ["去", "来", "到", "去过"],
      correct: "去过",
    },
  ],

  hsk3: [
    {
      prompt: "我对中文很__兴趣。",
      choices: ["有", "是", "在", "到"],
      correct: "有",
    },
    {
      prompt: "他__了一个新工作。",
      choices: ["找到", "到", "去", "是"],
      correct: "找到",
    },
  ],
};

function getManaChargeQuestion(hskKey) {
  const list = manaChargeQuestions[hskKey];
  const idx = Math.floor(Math.random() * list.length);
  return list[idx];
}

function startManaCharge() {
  activeSkill.value = "manaCharge";

  // selectedHSK.value = 1,2,3,4 → แปลงเป็น "hsk1", "hsk2", ...
  const hskLevel = "hsk" + (selectedHSK.value || 1);

  // ถ้าเลือก HSK ที่ไม่มีจริง ให้ fallback ไป hsk1
  const safeLevel = manaChargeQuestions[hskLevel] ? hskLevel : "hsk1";

  skillQuestion.value = {
    type: "fill-word",
    ...getManaChargeQuestion(safeLevel),
  };
}



function submitManaCharge(choice: string) {
  const q = skillQuestion.value;

  if (choice === q.correct) {
    player.mana = Math.min(player.maxMana, player.mana + 10);
    message.value = "+10 Mana!";
  } else {
    enemyAttack(5);
    message.value = "ผิด -5 HP";
  }
  clearSkillState();
}

// 7) Combo
function startCombo() {
  // ต้องถูกติดกันอย่างน้อย 4 ครั้งก่อนถึงจะใช้ได้
  if (player.consecutiveCorrect < 4) {
    message.value = "ต้องตอบถูกติดกัน 4 ครั้งก่อนถึงจะใช้คอมโบได้!";
    return;
  }

  if (!consumeMana(20)) return;

  activeSkill.value = "combo";
  skillQuestion.value = { type: "combo", remaining: 4 };
}


function submitComboAnswer(isCorrect: boolean) {
  const q = skillQuestion.value;

  if (isCorrect) {
    q.remaining--;
    if (q.remaining === 0) {
      applyDamageToCurrentTarget(40);
      message.value = "连击! -40 HP";
      clearSkillState();
    }
  } else {
    enemyAttack(10);
    message.value = "พลาด -10 HP";
    clearSkillState();
  }
}

// 8) Ultimate
function tryUltimate() {
  if (player.consecutiveCorrect >= 5 && consumeMana(30)) {
    applyDamageToCurrentTarget(50);
    message.value = "🔥 ULTIMATE! -50 HP";
    player.consecutiveCorrect = 0;
    clearSkillState();
  } else {
    message.value = "Ultimate ต้องถูกติดกัน 5 ครั้ง!";
  }
}

// 9) Heal

const healQuestions = {
  HSK1: [
    { th: "ฉันหิว", cn: "我饿了" },
    { th: "ขอบคุณ", cn: "谢谢" },
    { th: "เขาคือใคร", cn: "他是谁" },
  ],
  HSK2: [
    { th: "ฉันอยากไปจีนพรุ่งนี้", cn: "我明天想去中国" },
    { th: "ฉันกำลังเรียนภาษาจีน", cn: "我在学中文" },
  ],
  HSK3: [
    { th: "วันนี้อากาศดีมาก", cn: "今天天气很好" },
    { th: "ฉันอยากซื้อโทรศัพท์ใหม่", cn: "我想买新手机" },
  ],
  HSK4: [
    { th: "เขาทำงานหนักทุกวัน", cn: "他每天都很努力工作" },
    { th: "ฉันคิดว่าเรื่องนี้สำคัญมาก", cn: "我觉得这件事很重要" },
  ],
  HSK5: [
    { th: "เขาเชี่ยวชาญด้านคอมพิวเตอร์", cn: "他很擅长电脑方面" },
  ],
  HSK6: [
    { th: "เราไม่สามารถหลีกเลี่ยงปัญหานี้ได้", cn: "我们不能避免这个问题" },
  ],
};

function startHeal() {
  activeSkill.value = "heal";

  let level = String(bossState.currentHSK);  // แปลงเป็น string ก่อน

  // ถ้าไม่ได้ขึ้นต้นด้วย HSK → ใส่ HSK ให้เอง
  if (!level.startsWith("HSK")) {
    level = "HSK" + level;
  }

  const bank = healQuestions[level];

  if (!bank) {
    console.error("ไม่พบคำถามสำหรับระดับ:", level);
    return;
  }

  const q = bank[Math.floor(Math.random() * bank.length)];

  skillQuestion.value = {
    type: "translation",
    prompt: q.th,
    answer: q.cn,
  };
}



function submitHeal(ans: string) {
  const q = skillQuestion.value;

  if (ans.trim() === q.answer) {
    player.hp = Math.min(player.maxHp, player.hp + 10);
    message.value = "+10 HP!";
  } else {
    player.mana = Math.min(player.maxMana, player.mana + 10);
    message.value = "ผิด +10 Mana!";
  }
  clearSkillState();
}

/* -----------------------------
   RESET
------------------------------ */
function resetGame() {
  const scale = difficultyScale[selectedHSK.value];

  player.hp = player.maxHp;
  player.mana = player.maxMana;
  player.consecutiveCorrect = 0;

  // กำหนด HSK สำหรับเกม
  bossState.currentHSK = selectedHSK.value;

  // บอส HP ขึ้นกับระดับ!
  bossState.bossMaxHP = 200 * scale;
  bossState.bossHP = bossState.bossMaxHP;

  // ลูกกระจ๊อก HP ก็ขึ้นด้วย
  waves.value = [
    { id: 1, name: "ลูกกระจ๊อก 1", hp: 30 * scale, maxHp: 30 * scale, done: false },
    { id: 2, name: "ลูกกระจ๊อก 2", hp: 30 * scale, maxHp: 30 * scale, done: false },
    { id: 3, name: "ลูกกระน้องสุดท้าย", hp: 45 * scale, maxHp: 45 * scale, done: false },
  ];

  currentWaveIndex.value = 0;

  clearSkillState();
  message.value = `เริ่มใหม่! (ระดับ HSK ${selectedHSK.value})`;
}



const difficultyScale = {
  1: 1,    // HSK 1 ง่ายสุด
  2: 1.5,  // HSK 2
  3: 2,    // HSK 3
  4: 3,    // HSK 4 โหดสุด
};

</script>

<template>
  <OrganicLayout>
    <div class="max-w-6xl mx-auto p-6 text-gray-900">
    <div class="mb-4 flex gap-3 items-center">
  <span class="font-semibold">เลือกระดับ HSK:</span>

  <select
    v-model="selectedHSK"
    class="border px-3 py-1 rounded"
  >
    <option :value="1">HSK 1</option>
    <option :value="2">HSK 2</option>
    <option :value="3">HSK 3</option>
    <option :value="4">HSK 4</option>
  </select>

  <button
    class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700"
    @click="resetGame"
  >
    ▶️ เริ่มเล่น
  </button>
</div>

      <h2 class="text-3xl font-bold mb-6">⚔️ HSK {{ bossState.currentHSK }} — Battle</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- LEFT SIDE -->
        <div class="space-y-4">

          <!-- PLAYER STATUS -->
          <div class="bg-white/80 shadow rounded-xl p-4">
            <h5 class="font-semibold text-lg mb-2">👤 ผู้เล่น</h5>

            <!-- HP BAR -->
            <div>
              <div class="text-sm mb-1">HP: {{ player.hp }} / {{ player.maxHp }}</div>
              <div class="w-full bg-red-200 rounded-full h-3">
                <div
                  class="bg-red-500 h-3 rounded-full transition-all"
                  :style="{ width: (player.hp / player.maxHp * 100) + '%' }"
                ></div>
              </div>
            </div>

            <!-- MANA BAR -->
            <div class="mt-3">
              <div class="text-sm mb-1">Mana: {{ player.mana }} / {{ player.maxMana }}</div>
              <div class="w-full bg-blue-200 rounded-full h-3">
                <div
                  class="bg-blue-500 h-3 rounded-full transition-all"
                  :style="{ width: (player.mana / player.maxMana * 100) + '%' }"
                ></div>
              </div>
            </div>

            <div class="mt-3 text-sm">
              คอมโบต่อเนื่อง:
              <span class="font-semibold">{{ player.consecutiveCorrect }}</span>
            </div>
          </div>

          <!-- SKILL BUTTONS -->
          <div class="bg-white/80 shadow rounded-xl p-4">
            <h5 class="font-semibold text-lg mb-3">🎯 Skills</h5>

            <div class="flex flex-wrap gap-2">
              <button class="px-3 py-1.5 text-sm rounded bg-blue-600 text-white hover:bg-blue-700"
                @click="startSwordAttack"
              >
                Sword (-5)
              </button>

              <button class="px-3 py-1.5 text-sm rounded bg-yellow-500 text-white hover:bg-yellow-600"
                @click="startFireball"
              >
                Fireball (-10)
              </button>

              <button class="px-3 py-1.5 text-sm rounded bg-red-600 text-white hover:bg-red-700"
                @click="startQuickSlash"
              >
                Quick (-15)
              </button>

              <button class="px-3 py-1.5 text-sm rounded bg-purple-700 text-white hover:bg-purple-800"
                @click="startDragon"
              >
                Dragon (-15)
              </button>

              <button class="px-3 py-1.5 text-sm rounded bg-green-600 text-white hover:bg-green-700"
                @click="startManaCharge"
              >
                Mana Charge
              </button>

              <button class="px-3 py-1.5 text-sm rounded bg-gray-700 text-white hover:bg-gray-800"
                @click="startCombo"
              >
                Combo (-20)
              </button>

              <button class="px-3 py-1.5 text-sm rounded border border-blue-500 text-blue-600 hover:bg-blue-100"
                @click="tryUltimate"
              >
                Ultimate (-30)
              </button>

              <button class="px-3 py-1.5 text-sm rounded border border-green-500 text-green-600 hover:bg-green-100"
                @click="startHeal"
              >
                Heal
              </button>
            </div>
          </div>

          <!-- SKILL PANEL -->
          <div class="bg-white/90 shadow-inner rounded-xl p-4 min-h-[180px]">
            <h5 class="font-semibold text-lg mb-2">📜 Skill Panel</h5>

            <!-- vocab -->
            <div v-if="skillQuestion?.type === 'vocab-translate'">
              <div class="text-gray-700">{{ skillQuestion.prompt }}</div>
              <input
                v-model="skillAnswer"
                class="w-full mt-3 p-2 border rounded"
                placeholder="คำแปลภาษาไทย"
              />
              <button
                class="mt-3 px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700"
                @click="submitSwordAnswer(skillAnswer)"
              >
                ส่ง
              </button>
            </div>

            <!-- reorder -->
            <div v-if="skillQuestion?.type === 'reorder'">
              <div>จัดประโยค:</div>

              <div class="flex flex-wrap gap-2 mt-3">
                <button
                  v-for="(p, i) in skillQuestion.promptParts"
                  :key="i"
                  class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300"
                  @click="tempOrder.push(p)"
                >
                  {{ p }}
                </button>
              </div>

              <div class="mt-3 text-sm">
                เลือกแล้ว:
                <span class="font-medium text-blue-600">{{ tempOrder.join(' / ') }}</span>
              </div>

              <div class="mt-3 flex gap-2">
                <button
                  class="px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700"
                  @click="submitFireballAnswer(tempOrder)"
                >
                  ส่ง
                </button>

                <button
                  class="px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700"
                  @click="tempOrder = []"
                >
                  ล้าง
                </button>
              </div>
            </div>

            <!-- quick -->
            <div v-if="skillQuestion?.type === 'quick-mcq'">
              <div>{{ skillQuestion.prompt }}</div>
              <div class="mt-1 text-red-600 font-bold">⏳ {{ quickTimeLeft }} วินาที</div>

              <div class="mt-3 flex flex-wrap gap-2">
                <button
                  v-for="(c, idx) in skillQuestion.choices"
                  :key="idx"
                  @click="submitQuickAnswer(idx)"
                  class="px-3 py-1.5 rounded bg-blue-200 hover:bg-blue-300"
                >
                  {{ c }}
                </button>
              </div>
            </div>

            <!-- pinyin -->
            <div v-if="skillQuestion?.type === 'pinyin-mcq'">
              <div>{{ skillQuestion.prompt }}</div>
              <div class="mt-3 flex flex-wrap gap-2">
                <button
                  v-for="c in skillQuestion.choices"
                  :key="c"
                  class="px-3 py-1.5 rounded bg-purple-200 hover:bg-purple-300"
                  @click="submitDragon(c)"
                >
                  {{ c }}
                </button>
              </div>
            </div>

            <!-- fill-word -->
            <div v-if="skillQuestion?.type === 'fill-word'">
              <div>{{ skillQuestion.prompt }}</div>
              <div class="mt-3 flex gap-2">
                <button
                  v-for="c in skillQuestion.choices"
                  :key="c"
                  class="px-3 py-1.5 rounded bg-gray-200 hover:bg-gray-300"
                  @click="submitManaCharge(c)"
                >
                  {{ c }}
                </button>
              </div>
            </div>

            <!-- combo -->
            <div v-if="skillQuestion?.type === 'combo'">
              <div>ต้องตอบอีก {{ skillQuestion.remaining }} ข้อ</div>

              <div class="mt-3 flex gap-2">
                <button
                  class="px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700"
                  @click="submitComboAnswer(true)"
                >
                  ตอบถูก
                </button>

                <button
                  class="px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700"
                  @click="submitComboAnswer(false)"
                >
                  ตอบผิด
                </button>
              </div>
            </div>

            <!-- heal -->
            <div v-if="skillQuestion?.type === 'translation'">
              <div>{{ skillQuestion.prompt }}</div>

              <input
                v-model="skillAnswer"
                class="w-full mt-3 p-2 border rounded"
                placeholder="พิมพ์ภาษาจีน"
              />

              <button
                class="mt-3 px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700"
                @click="submitHeal(skillAnswer)"
              >
                ส่ง
              </button>
            </div>
          </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="space-y-4">

          <!-- BOSS BAR -->
          <div class="bg-white/80 shadow rounded-xl p-4">
            <h5 class="font-semibold text-lg mb-2">👹 Boss</h5>

            <div class="text-sm mb-1">HP: {{ bossState.bossHP }} / {{ bossState.bossMaxHP }}</div>
            <div class="w-full bg-purple-200 rounded-full h-3">
              <div
                class="bg-purple-600 h-3 rounded-full transition-all"
                :style="{ width: (bossState.bossHP / bossState.bossMaxHP * 100) + '%' }"
              ></div>
            </div>
          </div>

          <!-- WAVES -->
          <div class="bg-white/80 shadow rounded-xl p-4">
            <h5 class="font-semibold text-lg mb-2">👾 Waves</h5>

            <div v-for="w in waves" :key="w.id" class="text-sm">
              <span :class="w.done ? 'text-gray-400' : 'text-gray-800'">
                {{ w.name }} — {{ w.hp }} / {{ w.maxHp }}
              </span>
            </div>
          </div>

          <button
            class="px-4 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700"
            @click="resetGame"
          >
            🔄 เริ่มใหม่
          </button>
        </div>
      </div>

      <!-- MESSAGE -->
      <div v-if="message" class="mt-6 p-4 bg-blue-100 border border-blue-300 text-blue-700 rounded-lg shadow">
        {{ message }}
      </div>
    </div>
  </OrganicLayout>
</template>

