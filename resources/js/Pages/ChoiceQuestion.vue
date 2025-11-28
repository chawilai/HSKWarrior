<script setup lang="ts">
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed, onMounted, nextTick } from "vue";

// Reactive state
const quizQuestions = ref([
  {
    question: {
      hanzi: "我 ___ 中国人。",
      pinyin: "wǒ ___ Zhōngguó rén",
      translation: "ฉัน ___ คนจีน",
    },
    options: [
      { hanzi: "是", pinyin: "shì", translation: "คือ/เป็น" },
      { hanzi: "有", pinyin: "yǒu", translation: "มี" },
      { hanzi: "吃", pinyin: "chī", translation: "กิน" },
      { hanzi: "喝", pinyin: "hē", translation: "ดื่ม" },
    ],
    correctAnswer: 0,
  },
  {
    question: {
      hanzi: "你___什么名字？",
      pinyin: "nǐ ___ shénme míngzì?",
      translation: "คุณ___อะไร?",
    },
    options: [
      { hanzi: "叫", pinyin: "jiào", translation: "ชื่อว่า" },
      { hanzi: "看", pinyin: "kàn", translation: "ดู" },
      { hanzi: "去", pinyin: "qù", translation: "ไป" },
      { hanzi: "说", pinyin: "shuō", translation: "พูด" },
    ],
    correctAnswer: 0,
  },
  {
    question: {
      hanzi: "今天天气很___。",
      pinyin: "jīntiān tiānqì hěn ___。",
      translation: "วันนี้อากาศ___。",
    },
    options: [
      { hanzi: "好", pinyin: "hǎo", translation: "ดี" },
      { hanzi: "冷", pinyin: "lěng", translation: "หนาว" },
      { hanzi: "热", pinyin: "rè", translation: "ร้อน" },
      { hanzi: "忙", pinyin: "máng", translation: "ยุ่ง" },
    ],
    correctAnswer: 0,
  },
  {
    question: {
      hanzi: "他___去学校。",
      pinyin: "tā ___ qù xuéxiào。",
      translation: "เขา___ไปโรงเรียน",
    },
    options: [
      { hanzi: "想", pinyin: "xiǎng", translation: "อยากจะ" },
      { hanzi: "喜欢", pinyin: "xǐhuān", translation: "ชอบ" },
      { hanzi: "能", pinyin: "néng", translation: "สามารถ" },
      { hanzi: "会", pinyin: "huì", translation: "สามารถ/เป็น" },
    ],
    correctAnswer: 0,
  },
  {
    question: {
      hanzi: "我想___一杯茶。",
      pinyin: "wǒ xiǎng ___ yībēi chá。",
      translation: "ฉันอยาก___ชาหนึ่งถ้วย",
    },
    options: [
      { hanzi: "喝", pinyin: "hē", translation: "ดื่ม" },
      { hanzi: "买", pinyin: "mǎi", translation: "ซื้อ" },
      { hanzi: "做", pinyin: "zuò", translation: "ทำ" },
      { hanzi: "写", pinyin: "xiě", translation: "เขียน" },
    ],
    correctAnswer: 0,
  },
  {
    question: {
      hanzi: "这个多少___？",
      pinyin: "zhège duōshǎo ___?",
      translation: "อันนี้ราคาเท่าไหร่___?",
    },
    options: [
      { hanzi: "钱", pinyin: "qián", translation: "เงิน" },
      { hanzi: "个", pinyin: "gè", translation: "อัน (ลักษณนาม)" },
      { hanzi: "本", pinyin: "běn", translation: "เล่ม (ลักษณนาม)" },
      { hanzi: "块", pinyin: "kuài", translation: "หยวน (หน่วยเงิน)" },
    ],
    correctAnswer: 0,
  },
  {
    question: {
      hanzi: "___明天见。",
      pinyin: "___ míngtiān jiàn。",
      translation: "___พรุ่งนี้เจอกัน",
    },
    options: [
      { hanzi: "再见", pinyin: "zàijiàn", translation: "ลาก่อน" },
      { hanzi: "你好", pinyin: "nǐhǎo", translation: "สวัสดี" },
      { hanzi: "谢谢", pinyin: "xièxiè", translation: "ขอบคุณ" },
      { hanzi: "对不起", pinyin: "duìbùqǐ", translation: "ขอโทษ" },
    ],
    correctAnswer: 0,
  },
  {
    question: {
      hanzi: "我___学习汉语。",
      pinyin: "wǒ ___ xuéxí Hànyǔ。",
      translation: "ฉัน___เรียนภาษาจีน",
    },
    options: [
      { hanzi: "在", pinyin: "zài", translation: "กำลัง" },
      { hanzi: "很", pinyin: "hěn", translation: "มาก" },
      { hanzi: "不", pinyin: "bù", translation: "ไม่" },
      { hanzi: "都", pinyin: "dōu", translation: "ทั้งหมด" },
    ],
    correctAnswer: 0,
  },
  {
    question: {
      hanzi: "你的生日是___月几号？",
      pinyin: "nǐ de shēngrì shì ___ yuè jǐ hào?",
      translation: "วันเกิดของคุณคือวันที่เท่าไหร่ ___ เดือนอะไร?",
    },
    options: [
      { hanzi: "几", pinyin: "jǐ", translation: "กี่" },
      { hanzi: "什么", pinyin: "shénme", translation: "อะไร" },
      { hanzi: "哪", pinyin: "nǎ", translation: "ไหน" },
      { hanzi: "多", pinyin: "duō", translation: "มาก/เท่าไหร่" },
    ],
    correctAnswer: 0,
  },
  {
    question: { hanzi: "我爱你___", pinyin: "wǒ ài nǐ ___", translation: "ฉันรักคุณ___" },
    options: [
      { hanzi: "。", pinyin: "。", translation: "(เครื่องหมายจบประโยค)" },
      { hanzi: "吗", pinyin: "ma", translation: "ไหม?" },
      { hanzi: "呢", pinyin: "ne", translation: "ล่ะ?" },
      { hanzi: "吧", pinyin: "ba", translation: "เถอะ" },
    ],
    correctAnswer: 0,
  },
]);

const currentQuestionIndex = ref(0);
const score = ref(0);
const quizFinished = ref(false);
const selectedAnswer = ref<number | null>(null);
const feedback = ref<boolean | null>(null);
const difficulty = ref<string | null>(null);
const selectingDifficulty = ref(true);
const scrollOpened = ref(false);
const contentReady = ref(false); // New ref to control content visibility

onMounted(() => {
  setTimeout(() => {
    // This is for the scroll-opening animation
    scrollOpened.value = true;
    // After scroll animation starts, wait for it to finish then show content
    setTimeout(() => {
      contentReady.value = true;
    }, 2000); // 2 seconds for the scroll animation
  }, 100); // Initial delay before scroll animation begins
});

// Functions
async function selectDifficulty(level: string) {
  // 1. Set the difficulty
  difficulty.value = level;

  // 2. Hide the difficulty selector and content IMMEDIATELY
  selectingDifficulty.value = false;
  contentReady.value = false;

  // 3. Animate the scroll closing
  scrollOpened.value = false;

  // Wait for the closing animation (2s) to finish
  await new Promise((resolve) => setTimeout(resolve, 2000));

  // 4. Animate the scroll opening again to reveal the questions
  await nextTick();
  setTimeout(() => {
    // This is for the scroll-opening animation
    scrollOpened.value = true;
    // After scroll animation starts, wait for it to finish then show content
    setTimeout(() => {
      contentReady.value = true;
    }, 2000); // 2 seconds for the scroll animation
  }, 100); // Delay before scroll animation begins
}
function selectAnswer(index: number) {
  if (selectedAnswer.value !== null) return;

  selectedAnswer.value = index;
  feedback.value = index === currentQuestion.value.correctAnswer;
  if (feedback.value) {
    score.value++;
  }

  setTimeout(() => {
    if (currentQuestionIndex.value < quizQuestions.value.length - 1) {
      currentQuestionIndex.value++;
    } else {
      quizFinished.value = true;
    }
    selectedAnswer.value = null;
    feedback.value = null;
  }, 1500);
}

async function restartQuiz() {
  currentQuestionIndex.value = 0;
  score.value = 0;
  quizFinished.value = false;
  selectedAnswer.value = null;
  feedback.value = null;
  selectingDifficulty.value = true;
  difficulty.value = null;
  contentReady.value = false; // Hide content before scroll closes

  scrollOpened.value = false;
  await nextTick();
  setTimeout(() => {
    // This is for the scroll-opening animation
    scrollOpened.value = true;
    // After scroll animation starts, wait for it to finish then show content
    setTimeout(() => {
      contentReady.value = true;
    }, 2000); // 2 seconds for the scroll animation
  }, 100); // Delay before scroll animation begins
}

// Computed Properties
const currentQuestion = computed(() => quizQuestions.value[currentQuestionIndex.value]);

const displayedText = (part: "hanzi" | "pinyin" | "translation") => {
  const question = currentQuestion.value.question;
  if (selectedAnswer.value !== null) {
    const answer = currentQuestion.value.options[selectedAnswer.value];
    return question[part].replace(
      "___",
      `<span class="text-green-500 font-bold">${answer[part]}</span>`
    );
  }
  return question[part];
};

const getButtonClass = (index: number) => {
  if (selectedAnswer.value === null) return "answer-btn-default";
  const isCorrect = index === currentQuestion.value.correctAnswer;
  const isSelected = index === selectedAnswer.value;
  if (isSelected && isCorrect) return "answer-btn-correct";
  if (isSelected && !isCorrect) return "answer-btn-incorrect";
  if (isCorrect) return "answer-btn-correct"; // Highlight correct answer if user was wrong
  return "answer-btn-disabled";
};
</script>

<template>
  <Head title="HSK Question" />
  <OrganicLayout>
    <div
      class="hsk-container background-question-image min-h-screen w-full p-4 sm:p-8 flex justify-center items-start"
    >
      <!-- Left decorative image -->
      <div class="hidden lg:flex flex-1 flex-col justify-start items-end pr-8 pt-20">
        <img
          class="w-full max-h-screen object-contain drop-shadow-2xl animate-up-down"
          src="http://[::1]:5173/resources/images/coin1.png"
          alt="Coin"
        />
      </div>

      <!-- Main quiz content -->
      <div class="quiz-wrapper">
        <div class="scroll-container" :class="{ opened: scrollOpened }">
          <!-- Scroll Content -->
          <div class="scroll-content">
            <div v-if="contentReady" class="content-wrapper">
              <!-- Difficulty Selection -->
              <div v-if="selectingDifficulty" class="difficulty-selector">
                <h2 class="title">เลือกระดับความยาก</h2>
                <div class="button-group">
                  <button @click="selectDifficulty('easy')" class="difficulty-btn">
                    ง่าย
                  </button>
                  <button @click="selectDifficulty('hard')" class="difficulty-btn">
                    ปานกลาง
                  </button>
                  <button @click="selectDifficulty('very-hard')" class="difficulty-btn">
                    ยาก
                  </button>
                </div>
              </div>

              <!-- Quiz View -->
              <div v-else-if="!quizFinished" class="quiz-view">
                <div class="progress-bar">
                  ข้อที่ {{ currentQuestionIndex + 1 }} / {{ quizQuestions.length }}
                </div>
                <div class="question-area">
                  <div class="hanzi" v-html="displayedText('hanzi')"></div>
                  <div
                    class="pinyin"
                    v-if="difficulty !== 'very-hard'"
                    v-html="displayedText('pinyin')"
                  ></div>
                  <div
                    class="translation"
                    v-if="difficulty === 'easy'"
                    v-html="displayedText('translation')"
                  ></div>
                </div>
                <div class="answer-grid">
                  <button
                    v-for="(option, index) in currentQuestion.options"
                    :key="index"
                    class="answer-btn"
                    :class="getButtonClass(index)"
                    @click="selectAnswer(index)"
                    :disabled="selectedAnswer !== null"
                  >
                    <span class="hanzi-sm">{{ option.hanzi }}</span>
                    <span class="pinyin-sm" v-if="difficulty !== 'very-hard'">{{
                      option.pinyin
                    }}</span>
                    <span class="translation-sm" v-if="difficulty === 'easy'">{{
                      option.translation
                    }}</span>
                  </button>
                </div>
              </div>

              <!-- Quiz Finished View -->
              <div v-else class="result-view">
                <h2 class="title">ทำแบบทดสอบเสร็จแล้ว!</h2>
                <p class="score-label">คุณได้คะแนน</p>
                <p class="score-final">{{ score }} / {{ quizQuestions.length }}</p>
                <button @click="restartQuiz" class="restart-btn">
                  เริ่มใหม่อีกครั้ง
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right decorative image -->
      <div class="hidden lg:flex flex-1 flex-col justify-start items-start pl-8 pt-20">
        <img
          class="w-full max-w-xs object-contain drop-shadow-2xl animate-up-down"
          src="http://[::1]:5173/resources/images/warrior_student2.png"
          alt="Warrior"
        />
      </div>
    </div>
  </OrganicLayout>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=ZCOOL+XiaoWei&family=Noto+Sans+TC:wght@400;700&display=swap");

.hsk-container {
  /* min-height, display, align-items, justify-content, and padding are now handled by Tailwind classes in the template */
  font-family: "Noto Sans TC", sans-serif;
}
.background-question-image {
  background-image: url("http://[::1]:5173/resources/images/bg_word6.png");

  background-repeat: repeat;

  background-size: auto;

  background-color: #f7f7f7;
}

.max-w-4xl.w-full.bg-white {
  background-color: rgba(255, 255, 255, 0.9);
}

.quiz-wrapper {
  width: 100%;
  max-width: 900px;
  margin: auto;
}

.scroll-container {
  background-color: #fdf6e3; /* Papyrus color */
  border: 2px solid #8b4513;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), inset 0 0 15px rgba(0, 0, 0, 0.1);
  transition: transform 2s cubic-bezier(0.68, -0.55, 0.27, 1.55), opacity 2s ease;
  transform: scaleY(0);
  opacity: 0;
  position: relative;
  padding: 0 45px; 
  
}

.scroll-container::before,
.scroll-container::after {
  content: "";
  position: absolute;
  width: 100%;
  height: 40px;
  background-color: #a0522d; /* Sienna color for rollers */
  background-image: linear-gradient(
      45deg,
      #8b4513 25%,
      transparent 25%,
      transparent 75%,
      #8b4513 75%
    ),
    linear-gradient(45deg, #8b4513 25%, transparent 25%, transparent 75%, #8b4513 75%);
  background-size: 10px 10px;
  background-position: 0 0, 5px 5px;
  border: 3px solid #5c2f10;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.4);
  left: 0;
  z-index: 2;
}

.scroll-container::before {
  top: -42px;
  border-radius: 10px 10px 0 0;
}

.scroll-container::after {
  bottom: -42px;
  border-radius: 0 0 10px 10px;
}

.scroll-container.opened {
  transform: scaleY(1);
  opacity: 1;
}

.scroll-content {
  transition: all 2s ease; /* Keep transition for possible future use */
  padding: 2rem;
  overflow: hidden;
  min-height: 250px; /* Keep min-height for layout */
  display: flex; 
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.content-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%; /* Ensure it takes full width */
  height: 100%; /* Ensure it takes full height */
}

.scroll-container.opened .scroll-content {
  min-height: 60vh;
}

.title {
  font-family: "ZCOOL XiaoWei", serif;
  font-size: clamp(2rem, 5vw, 3rem);
  color: #8b0000; /* Dark Red */
  margin-bottom: 2rem;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.difficulty-selector,
.quiz-view,
.result-view {
  width: 100%;
  text-align: center;
}

.button-group {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1rem;
}

.difficulty-btn,
.restart-btn {
  background: linear-gradient(145deg, #e6a23c, #c87621);
  color: white;
  padding: 0.75rem 2rem;
  border-radius: 8px;
  font-size: clamp(1rem, 3vw, 1.25rem);
  border: 2px solid #a55d1b;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

.difficulty-btn:hover,
.restart-btn:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.25);
}

/* Quiz View */
.progress-bar {
  font-size: 1.25rem;
  color: #a0522d;
  font-weight: bold;
  margin-bottom: 1rem;
}

.question-area {
  margin-bottom: 2rem;
  background-color: rgba(253, 246, 227, 0.7); /* Lighter papyrus, semi-transparent */
  border: 1px solid #d3c0a5; /* A soft brown border */
  border-radius: 12px;
  padding: clamp(1rem, 4vw, 1.5rem);
  box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
  position: relative;
  border-left: 5px solid #c87621; /* Accent border */
  border-right: 5px solid #c87621; /* Accent border */
}

.hanzi {
  font-size: clamp(2.5rem, 6vw, 4rem);
  font-weight: bold;
  color: #333;
}
.pinyin {
  font-size: clamp(1.2rem, 3vw, 1.75rem);
  color: #777;
}
.translation {
  font-size: clamp(1rem, 2.5vw, 1.25rem);
  color: #999;
}

.answer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  width: 100%;
}

.answer-btn {
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
  transition: all 0.2s ease;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
  min-height: 100px;
}

.hanzi-sm {
  font-size: 1.75rem;
  font-weight: bold;
}
.pinyin-sm {
  font-size: 1rem;
}
.translation-sm {
  font-size: 0.9rem;
}

.answer-btn-default {
  background-color: #fff;
  color: #333;
  border: 2px solid #dcdcdc;
}
.answer-btn-default:hover {
  border-color: #c87621;
  transform: translateY(-2px);
}
.answer-btn-correct {
  background-color: #2ecc71;
  color: white;
  border-color: #27ae60;
  transform: scale(1.05);
}
.answer-btn-incorrect {
  background-color: #e74c3c;
  color: white;
  border-color: #c0392b;
}
.answer-btn-disabled {
  background-color: #bdc3c7;
  color: #7f8c8d;
  border-color: #95a5a6;
  opacity: 0.7;
}
.answer-btn:disabled {
  cursor: not-allowed;
}

/* Result View */
.score-label {
  font-size: 1.5rem;
  color: #555;
}
.score-final {
  font-family: "ZCOOL XiaoWei", serif;
  font-size: clamp(4rem, 10vw, 6rem);
  font-weight: bold;
  color: #e74c3c;
  margin: 1rem 0;
}
</style>
