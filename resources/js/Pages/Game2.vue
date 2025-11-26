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

function checkWinCondition() {
    // 1. ตรวจสอบว่า Boss ถูกกำจัดแล้ว
    if (inBossFight.value && bossState.bossHP <= 0) {
        message.value = `คุณเอาชนะ BOSS HSK ${bossState.currentHSK} ได้สำเร็จ! 🎉\nคุณต้องการเล่น HSK ระดับต่อไปหรือไม่?`;
        activeSkill.value = 'bossChallenge'; // ตั้งสถานะให้รู้ว่าชนะแล้ว
        return true;
    }

    // 2. ตรวจสอบว่า Wave จบแล้ว
    if (!inBossFight.value && waves.value.every(w => w.done)) {
        message.value = `เคลียร์ลูกกระจ๊อกครบแล้ว! เตรียมตัวสู้ BOSS HSK ${bossState.currentHSK} !!!`;
        return true;
    }
    
    return false;
}


function applyDamageToCurrentTarget(dmg: number) {
  if (!inBossFight.value) {
    const w = waves.value[currentWaveIndex.value];
    if (!w) return;
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
    checkWinCondition();
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

  // ใช้ pieces ที่สลับแล้วสำหรับปุ่มตัวเลือก
  const piecesToChoose = [...q.pieces].sort(() => Math.random() - 0.5);

  skillQuestion.value = {
    type: "reorder",
    promptParts: piecesToChoose, // ใช้ pieces ที่สลับแล้ว
    correct: q.correct
  };

  skillAnswer.value = "";
  tempOrder.value = [];
}



function submitFireballAnswer(order: string[]) {
  const q = skillQuestion.value;

  // เปรียบเทียบ array ที่ join ด้วยช่องว่าง
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

// 3) Shield – find wrong word (MCQ)
function startShield(sentence = "我 明天 去 中国了") {
  activeSkill.value = "shield";

  const words = sentence.split(" ");

  skillQuestion.value = {
    type: "find-wrong",
    prompt: "คำไหนผิดไวยากรณ์?",
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

// 4) Quick Slash – timer (MCQ)
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
      prompt: "ลักษณนามของ 張 ใช้กับอะไร?",
      choices: ["แผ่น/กระดาษ/โต๊ะ", "สัตว์", "คน", "สิ่งของ"],
      correctIndex: 0,
    }
  ],
  hsk3: [
    {
      prompt: "ลักษณนามของ 條 ใช้กับอะไร?",
      choices: ["สัตว์", "เส้น/ปลา/สิ่งยาวๆ", "คน", "หนังสือ"],
      correctIndex: 1,
    },
    {
      prompt: "ลักษณนามของ 輛 ใช้กับอะไร?",
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

// 5) Dragon Roar – Pinyin (MCQ)

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
    player.consecutiveCorrect++;
    message.value = "🐉 มังกรคำราม -20 HP";
  } else {
    enemyAttack(20);
    message.value = "เสียงผิด -20 HP";
    player.consecutiveCorrect = 0;
  }
  clearSkillState();
}

// 6) Mana Charge (MCQ)
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
    player.consecutiveCorrect++;
  } else {
    enemyAttack(5);
    message.value = "ผิด -5 HP";
    player.consecutiveCorrect = 0;
  }
  clearSkillState();
}

// 7) Combo (Placeholder Buttons for demonstration)
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
      player.consecutiveCorrect++; // คอมโบสำเร็จนับเป็น 1 Correct
      clearSkillState();
    }
  } else {
    enemyAttack(10);
    message.value = "พลาด -10 HP";
    clearSkillState();
    player.consecutiveCorrect = 0;
  }
}

// 8) Ultimate (No question type, instant action)
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

// 9) Heal (Input Box)

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
  if (!consumeMana(0)) return; // No mana cost for heal, but check if game is active

  let level = String(bossState.currentHSK);  // แปลงเป็น string ก่อน

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
    player.consecutiveCorrect++;
  } else {
    player.mana = Math.min(player.maxMana, player.mana + 10);
    message.value = "ผิด +10 Mana!";
    player.consecutiveCorrect = 0;
  }
  clearSkillState();
}

/* -----------------------------
   RESET
------------------------------ */
function resetGame(nextLevel: boolean = false) {
    if (nextLevel && bossState.currentHSK < 4) {
        selectedHSK.value = (bossState.currentHSK + 1) as 1 | 2 | 3 | 4;
    }
    
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
  1: 1,    // HSK 1 ง่ายสุด
  2: 1.5,  // HSK 2
  3: 2,    // HSK 3
  4: 3,    // HSK 4 โหดสุด
};

const currentEnemyName = computed(() => inBossFight.value ? 'BOSS HSK ' + bossState.currentHSK : waves.value[currentWaveIndex.value]?.name || 'ศัตรู');

// Helper function to calculate health bar color
function getHpBarColor(currentHp: number, maxHp: number) {
  const percentage = (currentHp / maxHp) * 100;
  if (percentage <= 30) return 'bg-red-600';
  if (percentage <= 60) return 'bg-yellow-500';
  return 'bg-green-600';
}
</script>

<template>
  <OrganicLayout>
    <div class="max-w-2xl mx-auto my-4 border-4 border-gray-600 shadow-xl bg-gray-100 font-sans text-sm">

      <div class="relative w-full h-80 border-b-2 border-gray-600 bg-green-500/30 overflow-hidden">
          
          <div class="absolute inset-0 bg-gradient-to-t from-lime-300 via-lime-500 to-sky-300"></div>

          <div class="absolute top-12 right-16 w-24 h-24 bg-gray-500/30 rounded-full border-4 border-green-700 shadow-xl flex items-center justify-center animate-bounce">
              <span class="text-xs text-white font-bold">{{ inBossFight ? 'BOSS' : 'WAVE' }}</span>
          </div>

          <div class="absolute bottom-4 left-4 w-32 h-32 bg-gray-500/30 rounded-full border-4 border-green-700 shadow-xl flex items-center justify-center">
              <span class="text-xs text-white font-bold">PLAYER</span>
          </div>
          
          <div class="absolute top-4 left-4 w-1/3 p-1.5 bg-gray-200 border-2 border-gray-700 shadow-md">
            <div class="relative h-12 bg-white border-b-2 border-gray-400 p-0.5">
              <div class="text-[11px] font-extrabold text-gray-800">
                  {{ currentEnemyName }}
              </div>
              <div class="text-[10px] font-extrabold text-gray-800">
                  LV. {{ bossState.currentHSK }}
              </div>
              
              <div class="mt-1 h-2 bg-yellow-400 border border-gray-500 overflow-hidden">
                <div 
                  class="h-full transition-all"
                  :class="getHpBarColor(inBossFight ? bossState.bossHP : waves[currentWaveIndex]?.hp, inBossFight ? bossState.bossMaxHP : waves[currentWaveIndex]?.maxHp)"
                  :style="{ width: (inBossFight ? (bossState.bossHP / bossState.bossMaxHP) : (waves[currentWaveIndex]?.hp / waves[currentWaveIndex]?.maxHp || 0)) * 100 + '%' }"
                ></div>
              </div>
            </div>
          </div>
          
          <div class="absolute bottom-4 right-4 w-2/5 p-1.5 bg-gray-200 border-2 border-gray-700 shadow-md">
            <div class="relative h-16 bg-white border-b-2 border-gray-400 p-0.5">
              <div class="text-[11px] font-extrabold text-gray-800">
                  PLAYER
              </div>
              <div class="text-[10px] font-extrabold text-gray-800">
                  LV. {{ selectedHSK }}
              </div>
              
              <div class="mt-1 h-2 bg-yellow-400 border border-gray-500 overflow-hidden">
                <div 
                  class="h-full transition-all"
                  :class="getHpBarColor(player.hp, player.maxHp)"
                  :style="{ width: (player.hp / player.maxHp * 100) + '%' }"
                ></div>
              </div>
              
              <div class="mt-0.5 h-1.5 bg-gray-400 border border-gray-500 overflow-hidden">
                <div 
                  class="bg-blue-600 h-full transition-all"
                  :style="{ width: (player.mana / player.maxMana * 100) + '%' }"
                ></div>
              </div>
              
              <div class="absolute bottom-1 right-1 text-[10px] font-bold text-gray-700">
                  HP: {{ player.hp }}/{{ player.maxHp }} | MP: {{ player.mana }}/{{ player.maxMana }}
              </div>
            </div>
          </div>
          
      </div>

      <div class="w-full bg-white border-t-2 border-gray-400 grid grid-cols-2">
          
          <div class="p-3 border-r-2 border-gray-400 bg-white text-gray-800 h-28 flex items-center">
            
            <div v-if="activeSkill === 'bossChallenge'" class="font-bold text-green-600 text-lg">
                {{ message }}
                <div class="mt-2 grid grid-cols-2 gap-2">
                    <button v-if="bossState.currentHSK < 4" @click="resetGame(true)" class="p-1 border-2 border-green-600 text-xs bg-green-200">
                        NEXT LEVEL (HSK {{ bossState.currentHSK + 1 }})
                    </button>
                    <button @click="resetGame(false)" class="p-1 border-2 border-gray-600 text-xs bg-gray-300">
                        RESTART HSK {{ selectedHSK }}
                    </button>
                </div>
            </div>
            <div v-else-if="activeSkill" class="font-bold text-red-600 animate-pulse text-lg">
                {{ activeSkill.toUpperCase() }}! {{ skillQuestion.prompt }}
            </div>
            <div v-else-if="message" class="font-bold text-gray-700 text-lg">
                {{ message }}
            </div>
            <div v-else class="font-bold text-lg">
                What will Player do? 
                <span class="text-blue-500 text-base"> (Combo: {{ player.consecutiveCorrect }})</span>
                <div class="mt-1 flex items-center gap-2">
                  <select v-model="selectedHSK" class="border p-0.5 text-xs">
                    <option :value="1">HSK 1</option>
                    <option :value="2">HSK 2</option>
                    <option :value="3">HSK 3</option>
                    <option :value="4">HSK 4</option>
                  </select>
                  <button @click="resetGame(false)" class="p-1 bg-gray-300 border border-gray-600 text-xs font-bold rounded-none">
                      RESET/START
                  </button>
                </div>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-0.5 p-1.5 bg-gray-200">
              
              <template v-if="activeSkill !== null && activeSkill !== 'bossChallenge'">
                  
                  <div class="col-span-2 p-1 bg-white border border-gray-500 mb-1">
                       <div v-if="skillQuestion?.type === 'reorder'">
                          <p class="text-[10px] text-gray-700">Order: {{ tempOrder.join(' ') }}</p>
                          <div class="flex flex-wrap gap-1 mt-1">
                              <button v-for="(p, i) in skillQuestion.promptParts" :key="i"
                                  class="px-1 bg-yellow-300 text-black text-[10px] rounded-none border border-black"
                                  @click="tempOrder.push(p)">{{ p }}</button>
                          </div>
                       </div>
                       <div v-else-if="skillQuestion?.type === 'quick-mcq' && quickTimer">
                           <p class="text-red-500 text-sm font-bold">TIME LEFT: {{ quickTimeLeft }}s</p>
                       </div>
                       <div v-else-if="skillQuestion?.type === 'combo'">
                            <p class="text-green-600 font-bold">HITS LEFT: {{ skillQuestion.remaining }}</p>
                       </div>
                       <input v-else-if="skillQuestion?.type === 'vocab-translate' || skillQuestion?.type === 'translation'"
                          v-model="skillAnswer"
                          class="w-full p-0.5 border border-gray-400 bg-white text-gray-800 rounded-none text-xs"
                          placeholder="Type Answer Here..."
                      />
                       <p v-else class="text-gray-500 text-xs">...</p>
                  </div>
                  
                  <template v-if="skillQuestion?.type === 'quick-mcq' || skillQuestion?.type === 'pinyin-mcq' || skillQuestion?.type === 'fill-word' || skillQuestion?.type === 'find-wrong'">
                      <button v-for="(c, idx) in skillQuestion.choices" :key="idx"
                          @click="
                              skillQuestion.type === 'quick-mcq' ? submitQuickAnswer(idx) : 
                              skillQuestion.type === 'pinyin-mcq' ? submitDragon(c) : 
                              skillQuestion.type === 'fill-word' ? submitManaCharge(c) :
                              submitShieldAnswer(idx)
                          "
                          class="p-2 border-2 border-black font-bold text-xs bg-blue-500 text-white rounded-none hover:bg-blue-600 col-span-1">
                          {{ c }}
                      </button>
                  </template>
                  
                  <template v-else>
                      <button @click="
                          activeSkill === 'sword' ? submitSwordAnswer(skillAnswer) :
                          activeSkill === 'heal' ? submitHeal(skillAnswer) :
                          activeSkill === 'fireball' ? submitFireballAnswer(tempOrder) :
                          activeSkill === 'combo' ? submitComboAnswer(true) :
                          activeSkill === 'ultimate' ? tryUltimate() :
                          clearSkillState()
                          "
                          class="p-2 border-2 border-black font-bold text-xs bg-red-500 text-white rounded-none hover:bg-red-600">
                          FIGHT / CONFIRM (A)
                      </button>
                      
                      <button @click="activeSkill === 'fireball' ? tempOrder = [] : clearSkillState()"
                          class="p-2 border-2 border-black font-bold text-xs bg-gray-400 text-gray-800 rounded-none hover:bg-gray-500">
                          {{ activeSkill === 'fireball' ? 'CLEAR' : 'CANCEL (B)' }}
                      </button>
                  </template>
                  
              </template>

              <template v-else>
                  <button @click="startSwordAttack" :disabled="player.mana < 5"
                      class="p-2 border-2 border-black font-bold text-xs rounded-none bg-red-500 text-white shadow-sm hover:bg-red-600 disabled:bg-gray-400">
                      SWORD (5M)
                  </button>
                  <button @click="startFireball" :disabled="player.mana < 10"
                      class="p-2 border-2 border-black font-bold text-xs rounded-none bg-orange-400 text-white shadow-sm hover:bg-orange-500 disabled:bg-gray-400">
                      FIREBALL (10M)
                  </button>
                  
                  <button @click="startHeal"
                      class="p-2 border-2 border-black font-bold text-xs rounded-none bg-lime-500 text-gray-800 shadow-sm hover:bg-lime-600 disabled:bg-gray-400">
                      HEAL
                  </button>
                   <button @click="startManaCharge"
                      class="p-2 border-2 border-black font-bold text-xs rounded-none bg-yellow-500 text-gray-800 shadow-sm hover:bg-yellow-600 disabled:bg-gray-400">
                      MANA CHARGE
                  </button>

                  <button @click="startQuickSlash" :disabled="player.mana < 15"
                      class="p-2 border-2 border-black font-bold text-xs rounded-none bg-blue-500 text-white shadow-sm hover:bg-blue-600 disabled:bg-gray-400">
                      QUICK (15M)
                  </button>
                  <button @click="startDragon" :disabled="player.mana < 15"
                      class="p-2 border-2 border-black font-bold text-xs rounded-none bg-pink-500 text-white shadow-sm hover:bg-pink-600 disabled:bg-gray-400">
                      DRAGON (15M)
                  </button>
                  <button @click="startCombo" :disabled="player.mana < 20 || player.consecutiveCorrect < 4"
                      class="p-2 border-2 border-black font-bold text-xs rounded-none bg-gray-500 text-white shadow-sm hover:bg-gray-600 disabled:bg-gray-400">
                      COMBO (20M)
                  </button>
                  <button @click="tryUltimate" :disabled="player.mana < 30 || player.consecutiveCorrect < 5"
                      class="p-2 border-2 border-black font-bold text-xs rounded-none bg-purple-500 text-white shadow-sm hover:bg-purple-600 disabled:bg-gray-400">
                      ULTIMATE (30M)
                  </button>
              </template>
              
          </div>
      </div>
      
    </div>
  </OrganicLayout>
</template>