<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";

interface Word {
  text: string;
  pinyin: string;
  thai: string;
}

// --- State Management ---
const sentences = [
  {
    id: 1,
    text: "我 是 学生",
    pinyin: "wǒ shì xuéshēng",
    thai: "ฉันเป็นนักเรียน",
    words: [
      { text: "我", pinyin: "wǒ", thai: "ฉัน" },
      { text: "是", pinyin: "shì", thai: "เป็น" },
      { text: "学生", pinyin: "xuéshēng", thai: "นักเรียน" },
    ],
  },
  {
    id: 2,
    text: "你 叫 什么 名字",
    pinyin: "nǐ jiào shénme míngzì",
    thai: "คุณชื่ออะไร",
    words: [
      { text: "你", pinyin: "nǐ", thai: "คุณ" },
      { text: "叫", pinyin: "jiào", thai: "ชื่อ" },
      { text: "什么", pinyin: "shénme", thai: "อะไร" },
      { text: "名字", pinyin: "míngzì", thai: "ชื่อ" },
    ],
  },
  {
    id: 3,
    text: "他 是 中国 人",
    pinyin: "tā shì zhōngguó rén",
    thai: "เขาเป็นคนจีน",
    words: [
      { text: "他", pinyin: "tā", thai: "เขา" },
      { text: "是", pinyin: "shì", thai: "เป็น" },
      { text: "中国", pinyin: "zhōngguó", thai: "ประเทศจีน" },
      { text: "人", pinyin: "rén", thai: "คน" },
    ],
  },
];

const puAlertImage = "http://[::1]:5173/resources/images/pu_alert.png";
const puAlertImage2 = "http://[::1]:5173/resources/images/pu_alert2.png";
const stu_finish = "http://[::1]:5173/resources/images/warrior_stu_finish.png";
const currentSentenceIndex = ref(0);
const sentence = computed(() => sentences[currentSentenceIndex.value]);

const originalWords = computed(() => sentence.value.words);
const shuffledWords = ref<Word[]>([]);
const droppedWords = ref<Word[]>([]);
const score = ref(0);
const gameOver = ref(false);
const hasCheckedAnswer = ref(false);

const isAnswerIncomplete = computed(
  () => droppedWords.value.length < originalWords.value.length
);

// Modal states
const showIncorrectAnswerModal = ref(false);
const showCorrectAnswerModal = ref(false);
const userAnswerText = ref<Word[]>([]);
const correctAnswerText = ref<Word[]>([]);
const attemptCount = ref(0);
const lastScore = ref(0);

// --- Core Game Logic ---

function shuffle(array: Word[]) {
  const newArray = array.slice();
  for (let i = newArray.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
  }
  return newArray;
}

function initialize() {
  shuffledWords.value = shuffle(originalWords.value);
  droppedWords.value = [];
  hasCheckedAnswer.value = false;
  showIncorrectAnswerModal.value = false;
  showCorrectAnswerModal.value = false;
  attemptCount.value = 0;
}

onMounted(initialize);

// --- Drag and Drop Logic ---

function onDragStart(
  event: DragEvent,
  word: Word,
  source: "shuffled" | "dropped",
  index: number = -1
) {
  if (event.dataTransfer) {
    event.dataTransfer.setData("text/plain", JSON.stringify({ word, source, index }));
  }
}

function getDragAfterElement(container: Element, x: number) {
  const draggableElements = Array.from(
    container.querySelectorAll('[draggable="true"]:not(.dragging)')
  );

  return draggableElements.reduce(
    (closest, child) => {
      const box = child.getBoundingClientRect();
      const offset = x - box.left - box.width / 2;
      if (offset < 0 && offset > closest.offset) {
        return { offset: offset, element: child };
      } else {
        return closest;
      }
    },
    { offset: Number.NEGATIVE_INFINITY, element: null as Element | null }
  ).element;
}

function onDrop(event: DragEvent, targetContainer: "shuffled" | "dropped") {
  event.preventDefault();
  const data = event.dataTransfer?.getData("text/plain");
  if (!data) return;

  const { word, source, index: sourceIndex } = JSON.parse(data);

  // Reset answer checking state
  if (hasCheckedAnswer.value) {
    hasCheckedAnswer.value = false;
  }
  showIncorrectAnswerModal.value = false;
  showCorrectAnswerModal.value = false;

  // --- Handle moving a word from the answer area back to the choice area ---
  if (source === "dropped" && targetContainer === "shuffled") {
    const [movedWord] = droppedWords.value.splice(sourceIndex, 1);
    shuffledWords.value.push(movedWord);
    return;
  }

  // --- Handle dropping a word into the answer area ---
  if (targetContainer === "dropped") {
    const targetElement = event.currentTarget as HTMLElement;
    const afterElement = getDragAfterElement(targetElement, event.clientX);

    // Find the index of the element we're dropping before
    // Note: The children are the DOM nodes, not the `droppedWords` array itself yet.
    const newIndex = afterElement
      ? Array.from(targetElement.children).indexOf(afterElement)
      : droppedWords.value.length;

    // Reordering a word already in the answer area
    if (source === "dropped") {
      const [movedWord] = droppedWords.value.splice(sourceIndex, 1);

      // When moving within the same list, the target index might need adjustment
      // if the item is moved from an earlier position to a later one.
      const adjustedIndex = sourceIndex < newIndex ? newIndex - 1 : newIndex;
      droppedWords.value.splice(adjustedIndex, 0, movedWord);

      // Moving a new word from the choices to the answer area
    } else if (source === "shuffled") {
      const [movedWord] = shuffledWords.value.splice(sourceIndex, 1);
      droppedWords.value.splice(newIndex, 0, movedWord);
    }
  }
}

function allowDrop(event: DragEvent) {
  event.preventDefault();
}

// --- Answer and Navigation Logic ---

function checkAnswer() {
  const userAnswerString = droppedWords.value.map((w) => w.text).join(" ");
  const correctSentenceString = sentence.value.text;

  if (userAnswerString === correctSentenceString) {
    const baseScore = 10;
    lastScore.value = Math.max(0, baseScore - attemptCount.value);
    score.value += lastScore.value;
    hasCheckedAnswer.value = true;
    showCorrectAnswerModal.value = true;
  } else {
    attemptCount.value++;
    userAnswerText.value =
      droppedWords.value.length > 0
        ? droppedWords.value
        : [{ text: "(คุณยังไม่ได้เรียงคำ)", pinyin: "", thai: "" }];
    correctAnswerText.value = originalWords.value;
    showIncorrectAnswerModal.value = true;
    hasCheckedAnswer.value = false;
  }
}

function nextSentence() {
  if (currentSentenceIndex.value < sentences.length - 1) {
    currentSentenceIndex.value++;
    initialize();
  } else {
    gameOver.value = true;
  }
  hasCheckedAnswer.value = false;
}

function reset() {
  initialize();
}

function playAgain() {
  currentSentenceIndex.value = 0;
  score.value = 0;
  gameOver.value = false;
  initialize();
}

function closeIncorrectModal() {
  showIncorrectAnswerModal.value = false;
}

function closeCorrectModal() {
  showCorrectAnswerModal.value = false;
  nextSentence(); // Automatically go to next sentence after closing correct modal
}

// --- Audio Playback ---
function playAudio(text: string) {
  if ("speechSynthesis" in window) {
    // Cancel any ongoing speech to prevent overlap
    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = "zh-CN";
    utterance.rate = 0.8;
    window.speechSynthesis.speak(utterance);
  } else {
    alert("ขออภัย เบราว์เซอร์ของคุณไม่รองรับการอ่านออกเสียง");
  }
}
</script>

<template>
  <Head title="Word Order Game" />
  <OrganicLayout>
    <div class="min-h-screen p-8 flex justify-center items-start background-word-image">
      <!-- Left decorative image -->
      <div class="hidden lg:flex flex-1 flex-col justify-between items-end pr-8">
        <img
          class="w-64 max-h-screen object-contain drop-shadow-2xl animate-up-down"
          src="http://[::1]:5173/resources/images/img/object/lantern_1.png"
          alt="Lantern Top Left"
        />
      </div>

      <!-- Main Game Content -->
      <div class="max-w-4xl w-full bg-color-#fdf6e3 p-8 rounded-lg shadow-lg relative">
        <div v-if="!gameOver">
          <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold text-gray-800">เรียงคำให้ถูกต้อง</h1>
            <div class="text-2xl font-bold text-gray-800">คะแนน: {{ score }}</div>
          </div>
          <p class="text-gray-600 mb-6">จัดเรียงคำเพื่อสร้างประโยคที่ถูกต้อง</p>

          <!-- Sentence to build -->
          <div class="mb-4 p-4 bg-gray-50 rounded-md">
            <p class="text-lg text-gray-700">ประโยคที่ต้องสร้าง:</p>
            <div class="flex items-center gap-2">
              <p class="text-2xl font-semibold text-blue-600">{{ sentence.text }}</p>
              <button
                @click="playAudio(sentence.text)"
                class="text-gray-500 hover:text-blue-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.858 5.858a12 12 0 0116.972 0M5 12h.01M12 12h.01M19 12h.01M5 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"
                  />
                </svg>
              </button>
            </div>
            <p class="text-lg text-gray-500">{{ sentence.pinyin }}</p>
            <p class="text-lg text-gray-500">{{ sentence.thai }}</p>
          </div>

          <!-- Drop Zone -->
          <div
            @drop="onDrop($event, 'dropped')"
            @dragover="allowDrop"
            class="border-dashed border-2 border-gray-400 p-4 min-h-[120px] mb-6 flex flex-wrap gap-3 items-start bg-gray-50 rounded-lg transition-colors duration-300"
          >
            <div
              v-for="(word, index) in droppedWords"
              :key="`dropped-${index}`"
              draggable="true"
              @dragstart="onDragStart($event, word, 'dropped', index)"
              class="cursor-move bg-blue-500 text-white p-3 rounded-md shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 flex items-center gap-2"
            >
              <div>
                <div class="font-bold text-lg">{{ word.text }}</div>
                <div class="text-sm">{{ word.pinyin }}</div>
                <div class="text-sm">{{ word.thai }}</div>
              </div>
              <button
                @click="playAudio(word.text)"
                class="text-blue-100 hover:text-white"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15.536 8.464a5 5 0 010 7.072M12 12h.01"
                  />
                </svg>
              </button>
            </div>
          </div>

          <!-- Shuffled Words Source -->
          <div
            @drop="onDrop($event, 'shuffled')"
            @dragover="allowDrop"
            class="bg-gray-100 p-4 min-h-[120px] flex flex-wrap gap-3 items-start rounded-lg"
          >
            <div
              v-for="(word, index) in shuffledWords"
              :key="`shuffled-${index}`"
              draggable="true"
              @dragstart="onDragStart($event, word, 'shuffled', index)"
              class="cursor-move bg-white p-3 rounded-md shadow hover:shadow-md transform hover:-translate-y-1 transition-all duration-200 flex items-center gap-2"
            >
              <div>
                <div class="font-bold text-lg">{{ word.text }}</div>
                <div class="text-sm text-gray-600">{{ word.pinyin }}</div>
                <div class="text-sm text-gray-600">{{ word.thai }}</div>
              </div>
              <button
                @click="playAudio(word.text)"
                class="text-gray-500 hover:text-blue-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15.536 8.464a5 5 0 010 7.072M12 12h.01"
                  />
                </svg>
              </button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="mt-6 flex gap-4">
            <button
              @click="checkAnswer"
              :disabled="hasCheckedAnswer || isAnswerIncomplete"
              class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
              :class="{
                'opacity-50 cursor-not-allowed': hasCheckedAnswer || isAnswerIncomplete,
              }"
            >
              ตรวจคำตอบ
            </button>
            <button
              @click="reset"
              class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
            >
              รีเซ็ต
            </button>
            <button
              @click="nextSentence"
              class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
            >
              ประโยคถัดไป
            </button>
          </div>
        </div>

        <!-- Game Over Screen -->
        <div v-else class="text-center">
          <img :src="stu_finish" alt="Correct" class="mx-auto" />
          <h2 class="text-4xl font-bold text-green-500 mb-4">จบเกม!</h2>
          <p class="text-2xl text-gray-700 mb-6">
            คะแนนสุดท้ายของคุณคือ:
            <span class="font-bold text-indigo-600">{{ score }}</span>
          </p>
          <button
            @click="playAgain"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
          >
            เล่นอีกครั้ง?
          </button>
        </div>
      </div>

      <!-- Right decorative image -->
      <div class="hidden lg:flex flex-1 flex-col justify-between items-start pl-8">
        <img
          class="w-full max-h-screen object-contain drop-shadow-2xl animate-up-down"
          src="http://[::1]:5173/resources/images/warrior_pu.png"
          alt="Warrior Master Top Right"
        />
      </div>
    </div>

    <!-- Correct Answer Modal -->
    <Modal :show="showCorrectAnswerModal" @close="closeCorrectModal">
      <div class="p-6 text-center">
        <img :src="puAlertImage2" alt="Correct" />
        <h2 class="text-2xl font-bold text-green-600 mb-4">ถูกต้อง!</h2>
        <p class="text-lg text-gray-700 mb-6">คุณได้ {{ lastScore }} คะแนน</p>
        <button
          @click="closeCorrectModal"
          class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg"
        >
          ข้อต่อไป
        </button>
      </div>
    </Modal>

    <!-- Incorrect Answer Modal -->
    <Modal :show="showIncorrectAnswerModal" @close="closeIncorrectModal">
      <div class="p-6">
        <div class="flex flex-col items-center">
          <img :src="puAlertImage" alt="Incorrect" />
          <h2 class="text-2xl font-bold text-red-600 mb-4">ยังไม่ถูกต้อง!</h2>
        </div>
        <div class="space-y-4">
          <div>
            <h3 class="font-semibold text-gray-700">ประโยคที่คุณเรียง:</h3>
            <div class="text-gray-600 bg-red-50 p-2 rounded-md flex flex-wrap gap-2">
              <div
                v-for="(word, index) in userAnswerText"
                :key="`user-${index}`"
                class="flex flex-col items-center"
              >
                <span class="font-bold text-lg">{{ word.text }}</span>
                <span class="text-sm">{{ word.pinyin }}</span>
                <span class="text-sm">{{ word.thai }}</span>
              </div>
            </div>
          </div>
          <div>
            <h3 class="font-semibold text-gray-700">ประโยคที่ถูกต้อง:</h3>
            <div class="text-gray-800 bg-green-50 p-2 rounded-md flex flex-wrap gap-2">
              <div
                v-for="(word, index) in correctAnswerText"
                :key="`correct-${index}`"
                class="flex flex-col items-center"
              >
                <span class="font-bold text-lg">{{ word.text }}</span>
                <span class="text-sm">{{ word.pinyin }}</span>
                <span class="text-sm">{{ word.thai }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-6 flex justify-end">
          <button
            @click="closeIncorrectModal"
            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg"
          >
            ลองอีกครั้ง
          </button>
        </div>
      </div>
    </Modal>
  </OrganicLayout>
</template>
<style scoped>
.background-word-image {
  background-image: url("/resources/images/bg_word6.png");
  background-repeat: repeat;
  background-size: auto;
  background-color: #f7f7f7;
}

.max-w-4xl.w-full.bg-color-white {
  background-color: rgba(255, 255, 255, 0.9);
}

/* More detailed speaker icon */
.h-6.w-6 {
  width: 1.5rem;
  height: 1.5rem;
}
.h-5.w-5 {
  width: 1.25rem;
  height: 1.25rem;
}
</style>
