<script setup lang="ts">
// import GuestLayout from "@/Layouts/GuestLayout.vue";
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, nextTick } from 'vue'


/* -----------------------------
    STATE
------------------------------ */

import cardBack from '@/../images/card-back-wood.png'
import chinese1 from '@/../images/chinese1.png'
import chinese2 from '@/../images/chinese2.png'
import chinese3 from '@/../images/chinese3.png'
import chinese4 from '@/../images/chinese4.png'
import chinese5 from '@/../images/chinese5.png'

import sound from '@/../sound/correct.mp3'

const expPopup = ref({
    show: false,
    amount: 0
})

const isShuffling = ref(false)

const selectedHSK = ref(1)  // เริ่มต้น HSK1

let expGainedDuringGame = 0

const timeCount = ref(0)
let timer: any = null

const isPlaying = ref(false)
const isPreviewing = ref(false)

const matched = ref(0)
const exp = ref(0)
const level = ref(1)
const expMax = ref(100)
const expBoost = ref(false)  // x2 EXP

let flippedCards: any[] = []

const expPercent = ref(0)

// Popup จบเกม
const gameOver = ref(false)
const finalTime = ref(0)
const finalExp = ref(0)


/* -----------------------------
   SOUNDS (NEW)
------------------------------ */
// 🎯 NEW: สร้าง Audio object สำหรับเสียงถูกและผิด
const soundSuccess = new Audio(sound); 
/*const soundFail = new Audio('/sounds/match_fail.mp3'); */
soundSuccess.volume = 1; // ปรับระดับเสียง
/*soundFail.volume = 0.5;*/

// ... โค้ด STATE และอื่นๆ ...

// ฟังก์ชันสำหรับเล่นเสียง (เพิ่มในส่วน Utility)
function playSound(type: 'success') {
    if (type === 'success') {
        soundSuccess.currentTime = 0; // เริ่มต้นใหม่ทุกครั้ง
        soundSuccess.play().catch(e => console.error("Sound play failed:", e));
    } /*else {
        soundFail.currentTime = 0; // เริ่มต้นใหม่ทุกครั้ง
        soundFail.play().catch(e => console.error("Sound play failed:", e));
    }*/
}

/* -----------------------------
    CARDS
------------------------------ */
/*const cards = ref([
    { id: 1, word: "朋友", lang: "cn", matched: false, flipped: false, pair: 1, animation: '' },
    { id: 2, word: "เพื่อน", lang: "th", matched: false, flipped: false, pair: 1, animation: '' },
    { id: 3, word: "妈妈", lang: "cn", matched: false, flipped: false, pair: 2, animation: '' },
    { id: 4, word: "แม่", lang: "th", matched: false, flipped: false, pair: 2, animation: '' },
    { id: 5, word: "水", lang: "cn", matched: false, flipped: false, pair: 3, animation: '' },
    { id: 6, word: "น้ำ", lang: "th", matched: false, flipped: false, pair: 3, animation: '' },
    { id: 7, word: "谢谢", lang: "cn", matched: false, flipped: false, pair: 4, animation: '' },
    { id: 8, word: "ขอบคุณ", lang: "th", matched: false, flipped: false, pair: 4, animation: '' }
])*/

const cards = ref<any[]>([])  // ว่างตอนเริ่ม

const allCards = {
  1: [
    { id: 1, word: "朋友", lang: "cn", pair: 1 },
    { id: 2, word: "เพื่อน", lang: "th", pair: 1 },
    { id: 3, word: "妈妈", lang: "cn", pair: 2 },
    { id: 4, word: "แม่", lang: "th", pair: 2 },
    { id: 5, word: "水", lang: "cn", pair: 3 },
    { id: 6, word: "น้ำ", lang: "th", pair: 3 },
    { id: 7, word: "谢谢", lang: "cn", pair: 4 },
    { id: 8, word: "ขอบคุณ", lang: "th", pair: 4 }
  ],
  2: [
    { id: 1, word: "学校", lang: "cn", pair: 1 },
    { id: 2, word: "โรงเรียน", lang: "th", pair: 1 },
    { id: 3, word: "老师", lang: "cn", pair: 2 },
    { id: 4, word: "ครู", lang: "th", pair: 2 },
    { id: 5, word: "吃", lang: "cn", pair: 3 },
    { id: 6, word: "กิน", lang: "th", pair: 3 },
    { id: 7, word: "大", lang: "cn", pair: 4 },
    { id: 8, word: "ใหญ่", lang: "th", pair: 4 }
  ],
  3: [
    { id: 1, word: "城市", lang: "cn", pair: 1 },
    { id: 2, word: "เมือง", lang: "th", pair: 1 },
    { id: 3, word: "出现", lang: "cn", pair: 2 },
    { id: 4, word: "ปรากฏ", lang: "th", pair: 2 },
    { id: 5, word: "饿", lang: "cn", pair: 3 },
    { id: 6, word: "หิว", lang: "th", pair: 3 },
    { id: 7, word: "刚才", lang: "cn", pair: 4 },
    { id: 8, word: "เมื่อสักครู่", lang: "th", pair: 4 },
    { id: 9, word: "保护", lang: "cn", pair: 5 },
    { id: 10, word: "ปกป้อง", lang: "th", pair: 5 },
    { id: 11, word: "表格", lang: "cn", pair: 6 },
    { id: 12, word: "ตาราง", lang: "th", pair: 6 }
  ],
   4: [
    { id: 1, word: "感兴趣", lang: "cn", pair: 1 },
    { id: 2, word: "สนใจ", lang: "th", pair: 1 },
    { id: 3, word: "居住", lang: "cn", pair: 2 },
    { id: 4, word: "อยู่อาศัย", lang: "th", pair: 2 },
    { id: 5, word: "妻子", lang: "cn", pair: 3 },
    { id: 6, word: "ภรรยา", lang: "th", pair: 3 },
    { id: 7, word: "确认", lang: "cn", pair: 4 },
    { id: 8, word: "ยืนยัน", lang: "th", pair: 4 },
    { id: 9, word: "有趣", lang: "cn", pair: 5 },
    { id: 10, word: "น่าสนใจ", lang: "th", pair: 5 },
    { id: 11, word: "月底", lang: "cn", pair: 6 },
    { id: 12, word: "สิ้นเดือน", lang: "th", pair: 6 }
  ],
}

loadHSKCards(selectedHSK.value)


async function shuffleAnimation(duration = 1500) {
  isShuffling.value = true

  const interval = 150    // เวลาแต่ละรอบ ms
  const steps = Math.floor(duration / interval)

  for (let i = 0; i < steps; i++) {
    // สลับการ์ด
    cards.value = cards.value
      .map(c => ({ ...c }))
      .sort(() => Math.random() - 0.5)

    // ใส่ class "flipped" สลับหน้ากลับเพื่อให้ดูเหมือนสลับไพ่
    cards.value.forEach(c => c.flipped = Math.random() > 0.5)

    await new Promise(r => setTimeout(r, interval))
  }

  // ปิด flipped ทั้งหมดก่อนเริ่มเกมจริง
  cards.value.forEach(c => c.flipped = false)
  isShuffling.value = false
}




function showExpPopup(amount: number) {
    expPopup.value.amount = amount
    expPopup.value.show = true

    // ซ่อนหลัง 1.2 วิ
    setTimeout(() => {
        expPopup.value.show = false
    }, 1200)
}

const levelUpPopup = ref({
    show: false,
    level: 1
})

function showLevelUpPopup(newLevel: number) {
  nextTick(() => {
        const wrapper = document.querySelector('.main-game-wrapper');
        if (wrapper) {
            wrapper.classList.add('shake-screen');
            setTimeout(() => {
                wrapper.classList.remove('shake-screen');
            }, 500); // ระยะเวลาต้องตรงกับ animation-duration (0.5s)
        }
    });
    levelUpPopup.value.level = newLevel
    levelUpPopup.value.show = true

    setTimeout(() => {
        levelUpPopup.value.show = false
    }, 1500)
}


/* -----------------------------
    TIME FORMAT
------------------------------ */
function formatTime(sec: number) {
    const m = Math.floor(sec / 60)
    const s = sec % 60
    return `${m}:${s.toString().padStart(2, '0')}`
}


/* -----------------------------
    TIMER
------------------------------ */
function startTimerCount() {
    if (timer) clearInterval(timer)
    timeCount.value = 0
    timer = setInterval(() => {
        timeCount.value++
    }, 1000)
}

/* -----------------------------
    SHUFFLE / CLOSE
------------------------------ */
function shuffleCards() {
    cards.value = cards.value
        .map(c => ({ ...c }))
        .sort(() => Math.random() - 0.5)
}

function closeAllCards() {
    cards.value.forEach(c => {
        c.flipped = false
        c.matched = false
        c.animation = ''
    })
}

/* -----------------------------
    START GAME
------------------------------ */

const isGameStarted = ref(false)

async function startGameNormal(options: { load?: boolean } = { load: true }) {
  // options.load = true (default) จะโหลด deck ใหม่จาก selectedHSK
  if (isGameStarted.value) return
  isGameStarted.value = true
  isPreviewing.value = false

  if (options.load) {
    // โหลดชุดการ์ดใหม่เพียงเมื่อเราต้องการจริง ๆ
    loadHSKCards(selectedHSK.value)
  }

  // เล่นแอนิเมชันสับไพ่
  await shuffleAnimation(1500)

  // เริ่มสถานะเกมจริง
  isPlaying.value = true
  matched.value = 0
  expGainedDuringGame = 0
  expBoost.value = true // กดปุ่มเปิด x2
  closeAllCards()
  shuffleCards()
  startTimerCount()
}


async function startGameWithPreview() {

  if (isGameStarted.value) return
  isGameStarted.value = true

  loadHSKCards(selectedHSK.value) // โหลด deck ก่อน preview
  expBoost.value = false
  isPlaying.value = false
  isPreviewing.value = true
  expGainedDuringGame = 0
  matched.value = 0
  closeAllCards() 
  shuffleCards()
  cards.value.forEach(c => c.flipped = true)
  startTimerCount()
  setTimeout(() => {
    cards.value.forEach(c => {
      if (!c.matched) c.flipped = false
    })
    isPreviewing.value = false
    isPlaying.value = true
  }, 10000)
}


async function startGameWithShuffle() {
  if (isPlaying.value || isShuffling.value) return


  // ถ้าไม่ต้องการ reload เด็คก่อน shuffle ให้ใช้ load = false
  // แต่โดยปกติเราต้องการโหลด deck ตาม selectedHSK ก่อน shuffle
  await startGameNormal({ load: true })
}





/* -----------------------------
    EXP / LEVEL
------------------------------ */
function addExp(amount: number) {
    let gained = amount
    if (expBoost.value) gained  *= 2

    showExpPopup(gained )
    
    expGainedDuringGame += gained
    exp.value += gained 
    

    while (exp.value >= expMax.value) {
        exp.value -= expMax.value
        level.value++

        showLevelUpPopup(level.value)  
        expMax.value = 100 + (level.value - 1) * 50

    }

    expPercent.value = (exp.value / expMax.value) * 100
    
}

/* -----------------------------
    FLIP / CHECK MATCH
------------------------------ */




function flipCard(card: any) {
    if (!isPlaying.value) return
    if (isPreviewing.value) return
    if (card.flipped || card.matched) return
    if (flippedCards.length === 2) return

    card.flipped = true
    flippedCards.push(card)

    if (flippedCards.length === 2) {
        setTimeout(checkMatch, 500)
    }
}

function checkMatch() {
    const [a, b] = flippedCards

    if (a.pair === b.pair) {
        a.matched = true
        b.matched = true
        matched.value++
        addExp(10)

        playSound('success');

        nextTick(() => {
            a.animation = 'card-matched'
            b.animation = 'card-matched'
            setTimeout(() => {
                a.animation = ''
                b.animation = ''
            }, 300)
        })
    } else {
        nextTick(() => {
            a.animation = 'card-wrong'
            b.animation = 'card-wrong'
            setTimeout(() => {
                a.flipped = false
                b.flipped = false
                a.animation = ''
                b.animation = ''
            }, 600)
        })
    }

    flippedCards = []

    if (matched.value === cards.value.length / 2) {
        if (timer) clearInterval(timer)
        isPlaying.value = false
        isGameStarted.value = false  
        finalTime.value = timeCount.value
        finalExp.value = expGainedDuringGame
        gameOver.value = true
        setTimeout(() => {
        closeAllCards()
    }, 5000)
    }
}

/* -----------------------------
    STATE
------------------------------ */
// ... โค้ดที่มีอยู่ ...

const showDisabledAlert = ref(false)


/* -----------------------------
    UTILITY
------------------------------ */

function showGameInProgressAlert() {
  // 1. แสดงข้อความแจ้งเตือน
  showDisabledAlert.value = true

  // 2. ซ่อนข้อความแจ้งเตือนหลังจาก 2 วินาที
  setTimeout(() => {
    showDisabledAlert.value = false
  }, 2000)
}
/* -----------------------------
    CLOSE POPUP
------------------------------ */
function closePopup() {
    gameOver.value = false
}

function loadHSKCards(level: number) {
  
  const selected = allCards[level] || []
  cards.value = selected.map(c => ({
    ...c,
    flipped: false,
    matched: false,
    animation: ''
  }))
}

</script>

<template>
    <Head title="Question" />
    <OrganicLayout>

    <div class="floating-container">
            <img :src="chinese1" class="floating-flower image-1"alt="Chinese Flower 1"/>
            <img :src="chinese2" class="floating-flower image-2"alt="Chinese Flower 2"/>
            <img :src="chinese3" class="floating-flower image-3"alt="Chinese Flower 3"/>
            <img :src="chinese4" class="floating-flower image-4"alt="Chinese Flower 4"/>
            <img :src="chinese5" class="floating-flower image-5"alt="Chinese Flower 5"/>
            <img :src="chinese1" class="floating-flower image-6"alt="Chinese Flower 6"/>
            <img :src="chinese2" class="floating-flower image-7"alt="Chinese Flower 7"/>
            <img :src="chinese3" class="floating-flower image-8"alt="Chinese Flower 8"/>
            <img :src="chinese4" class="floating-flower image-9"alt="Chinese Flower 9"/>
            <img :src="chinese5" class="floating-flower image-10"alt="Chinese Flower 10"/>
            <img :src="chinese5" class="floating-flower image-11"alt="Chinese Flower 11"/>
            <img :src="chinese4" class="floating-flower image-12"alt="Chinese Flower 12"/>
            <img :src="chinese3" class="floating-flower image-13"alt="Chinese Flower 13"/>
            <img :src="chinese2" class="floating-flower image-14"alt="Chinese Flower 14"/>
            <img :src="chinese1" class="floating-flower image-15"alt="Chinese Flower 15"/>
        </div>

<div class="main-game-wrapper">
        <div class="text-center mt-4">
      <h1 class="text-3xl font-bold mb-1">ประลองจับคู่คำศัพท์ HSK 1</h1>
      <p class="text-gray-700">จับคู่คำศัพท์ภาษาจีนกับความหมายภาษาไทยให้ถูกต้อง</p>
    </div>

<!-- HSK Level Selector -->
<div class="text-center mt-6 flex justify-center gap-4 flex-wrap">
  <span class="font-semibold text-lg">เลือกระดับ HSK:</span>
  <button 
  v-for="level in [1, 2, 3, 4]" 
  :key="level"
  @click="if (!isPlaying) { selectedHSK = level; loadHSKCards(level) }"
  :class="['btn-hsk', selectedHSK === level ? 'active' : '', isPlaying ? 'opacity-40 pointer-events-none' : '']">
  HSK {{ level }}
</button>



</div>

<div class="mt-5 flex justify-center gap-6 relative"> <div class="side-panel">
      <div class="info-box">
        <div class="value">{{ formatTime(timeCount) }}</div>
        <div class="label">⏱️ เวลา</div>
      </div>
  
      <div class="info-box">
        <div class="value">{{ matched }}/{{ cards.length / 2 }}</div>
        <div class="label">✅ คู่ที่จับได้</div>
      </div>

      <div v-if="!isShuffling" class="mascot-container" :class="{ 'waving': isPlaying, 'hidden-mobile': true }">
        <div class="mascot-face" :class="{'preview': isPreviewing}">
            {{ isPreviewing ? '👀' : (isPlaying ? '😊' : '💖') }}
        </div>
        <div class="mascot-text">
            {{ isPreviewing ? 'จำไว้ 10 วิ!' : (isPlaying ? 'สู้ๆ นะ!' : 'พร้อมเริ่มแล้ว!') }}
        </div>
    </div>
    </div>
  
  
  
    <div class="p-6 rounded-2xl card-board" :class="{'board-shuffling': isShuffling}" style="width: 650px;">
    <div 
  v-if="!isGameStarted && !isShuffling" 
  class="start-game-overlay"
>
  <p class="text-xl font-bold mb-4">⬇️ กดปุ่ม "เริ่มเกม" ด้านล่างเพื่อเริ่มเล่น</p>
  <p class="text-lg text-gray-700">เลือกระดับ HSK ที่ต้องการก่อนเริ่มเกม</p>
</div>
      <div class="grid grid-cols-4 gap-6">
          <div
            v-for="card in cards"
            :key="card.id"
            class="cursor-pointer card-item"
            :class="[card.flipped || card.matched ? 'flipped' : '', card.animation, isShuffling ? 'shuffling' : '']"
            @click="flipCard(card)"
          >
              <div class="card-inner">
                  <div class="card-front" :class="card.lang">
                      {{ card.word }}
                  </div>
                  <div
                    class="card-back"
                    :style="{ backgroundImage: `url('${cardBack}')` }"
                  ></div>
              </div>
          </div>
      </div>
    </div>
  
  
    <div class="side-panel">
      <div class="info-box">
        <div class="value">{{ exp }}</div>
        <div class="label">⭐ EXP</div>
        <div class="mt-4 flex justify-center">
          <div class="exp-container">
            <div class="exp-bar" :style="{ width: expPercent + '%' }"></div>
            <div class="exp-text">{{ exp }} / {{ expMax }}</div>
            <div v-if="expPopup.show" class="exp-shine"></div>
          </div>
        </div>
      </div>
  
      <div class="info-box">
        <div class="value">{{ level }}</div>
        <div class="label">👑 เลเวล</div>
      </div>

      <div v-if="!isShuffling" class="mascot-container" :class="{ 'jumping': gameOver, 'hidden-mobile': true }">
        <div class="mascot-face">
            {{ gameOver ? '🥳' : '🧐' }}
        </div>
        <div class="mascot-text">
            {{ gameOver ? 'ทำได้เยี่ยม!' : 'กำลังคิด...' }}
        </div>
    </div>
    </div>
</div>




    <!-- CARD GRID -->
    <!--<div class="mt-10 flex justify-center">
      <div class="p-6 rounded-2xl card-board" style="width: 820px;">
        <div class="grid grid-cols-4 gap-6">
          <div
            v-for="card in cards"
            :key="card.id"
            class="cursor-pointer card-item"
            :class="[card.flipped || card.matched ? 'flipped' : '', card.animation]"
            @click="flipCard(card)"
          >
            <div v-if="card.flipped || card.matched" class="card-front" :class="card.lang">
              {{ card.word }}
            </div>
            <div
              v-else
              class="card-back"
              :style="{ backgroundImage: `url('${cardBack}')` }"
            ></div>
          </div>
        </div>
      </div>
    </div>
    -->
    <!-- BUTTONS -->
    <div class="text-center mt-5 flex justify-center gap-4">
      <button 
  @click="isGameStarted ? showGameInProgressAlert() : startGameWithPreview()" 
  class="btn-start"
  :class="{ 'disabled-btn': isGameStarted }"
>
  สลับการ์ดและเริ่มเกม (ดูไพ่ 10 วิ)
</button>

<button 
  @click="isGameStarted ? showGameInProgressAlert() : startGameWithShuffle()" 
  class="btn-random"
  :class="{ 'disabled-btn': isGameStarted }"
>
  สุ่มและเริ่มเกม
</button>


      <div class="flex items-center exp-boost" @click="expBoost = !expBoost">
        x2 EXP
      </div>
    </div>

    <!-- POPUP -->
    <div v-if="gameOver" class="popup-overlay">
  <div class="popup-box">
    <h2>🎉 จับคู่ครบแล้ว! 🎉</h2>

    <div class="result-row">
      <span>⏱ เวลา:</span>
      <span>{{ formatTime(finalTime) }}</span>
    </div>

    <div class="result-row">
      <span>⭐ EXP รอบนี้:</span>
      <span>+{{ finalExp }}</span>
    </div>

    <div v-if="levelUpPopup.show" class="levelup-floating">
      🎉 Level {{ levelUpPopup.level }} Up! 🎉
    </div>
        <button @click="closePopup">ปิด</button>
      </div>
    </div>

    <div v-if="expPopup.show" class="exp-floating">
  +{{ expPopup.amount }} EXP!
</div>

<div v-if="levelUpPopup.show" class="levelup-floating">
  🎉 Level {{ levelUpPopup.level }} Up! 🎉
</div>

</div>
<div v-if="showDisabledAlert" class="disabled-alert-floating">
  🛑 กรุณาเล่นเกมปัจจุบันให้จบก่อน! 🛑
</div>
    </OrganicLayout>
</template>

<style scoped>
/* Background */
:deep(body) {
   /* สีพื้นหลังโทนแดงอมชมพูอ่อนๆ สไตล์เทศกาล */  
  background: linear-gradient(to bottom, #fde8e8, #f5d0d0); 
  font-family: "Kanit", sans-serif;
  position: relative;
  overflow: hidden; /* ซ่อนสิ่งที่ล้นจอ */
}

.main-game-wrapper {
    position: relative;
    z-index: 10;
}

.floating-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none; /* ทำให้คลิกทะลุได้ */
    z-index: 1; /* กำหนดให้อยู่ด้านหลังเนื้อหาเกมทั้งหมด */
    filter: blur(0.5px); /* เพิ่มความนุ่มนวลเล็กน้อย */
}

/* 🎯 NEW: สไตล์หลักสำหรับรูปภาพดอกไม้ที่ลอย (floating-flower) */
.floating-flower {
    position: absolute;
    opacity: 0; /* 🎯 เริ่มต้นให้จางหายไป */
    animation: float-petal 25s linear infinite; 
    object-fit: contain; 
    pointer-events: none; 
    /* 🎯 ปรับขนาดพื้นฐานให้ใหญ่ขึ้น (หรือกำหนดในแต่ละ image-X ก็ได้) */
    width: 250px; /* ขนาดเริ่มต้นที่ใหญ่ขึ้น */
    height: 250px;
}

/* ⚠️ ลบโค้ดนี้ออก หรือ คอมเมนต์ทิ้ง เพราะไม่ใช้จุดวงกลมแล้ว 
.floating-container::before,
.floating-container::after,
:deep(.element-3),
:deep(.element-4) {
    display: none !important; 
} 
*/

/* 🎯 NEW: กำหนดขนาดและตำแหน่งของรูปภาพดอกไม้แต่ละรูป */
.floating-flower.image-1 {
    width: 250px; height: 300px; top: 100%; left: 10%; /* เริ่มจากด้านล่าง */
    animation-duration: 30s; animation-delay: 1s;
}
.floating-flower.image-2 {
    width: 150px; height: 100px; top: 110%; right: 5%; /* เริ่มจากด้านล่าง */
    animation-duration: 22s; animation-delay: 4s;
}
.floating-flower.image-3 {
    width: 280px; height: 280px; top: 105%; left: 20%; /* เริ่มจากด้านล่าง */
    animation-duration: 35s; animation-delay: 8s;
}
.floating-flower.image-4 {
    width: 140px; height: 140px; top: 120%; left: 60%; /* เริ่มจากด้านล่าง */
    animation-duration: 18s; animation-delay: 0s;
}
.floating-flower.image-5 { /* เพิ่มเข้ามาอีกหนึ่งดอก */
    width: 210px; height: 210px; top: 115%; right: 25%; /* เริ่มจากด้านล่าง */
    animation-duration: 28s; animation-delay: 5s;
}

.floating-flower.image-6 { 
     /* กลางจอ, ความเร็วปานกลาง, เริ่มเร็ว */
     width: 150px; height: 150px; 
    top: 125%; /* เริ่มจากลึก */ 
     left: 45%; 
     animation-duration: 22s; 
     animation-delay: 1s; /* เริ่มทันที */
}

.floating-flower.image-7 { 
     /* ใหญ่, ช้า, ลอยจากขวาบน */
     width: 220px; height: 220px; 
     top: 110%; 
     right: 10%; 
     animation-duration: 34s; 
     animation-delay: 5s; 
}

.floating-flower.image-8 { 
     /* เล็ก, เร็ว, ลอยจากซ้าย */
     width: 150px; height: 100px; 
     top: 110%; /* เริ่มจากลึกมาก */
     left: 5%; 
     animation-duration: 18s; 
     animation-delay: 10s; 
}

.floating-flower.image-9 { 
     /* กลาง, ความเร็วปานกลาง, ลอยจากขวา */
     width: 190px; height: 190px; 
     top: 100%; /* เริ่มจากขอบจอพอดี */
     right: 30%; 
     animation-duration: 26s; 
   animation-delay: 3s; 
}

.floating-flower.image-10 { 
     /* ใหญ่มาก, ช้า, ลอยจากกลางซ้าย */
     width: 250px; height: 250px; 
     top: 130%; 
     left: 15%; 
     animation-duration: 40s; 
     animation-delay: 7s; 
}

/* --- NEW: ดอกไม้ #11 ถึง #15 (เพิ่มความถี่) --- */
.floating-flower.image-11 {
    /* ขนาดกลาง, เร็ว, เริ่มลึก, ซ้ายกลาง */
    width: 180px; height: 180px; 
    top: 140%; 
    left: 35%; 
    animation-duration: 20s; 
    animation-delay: 2s; 
}

.floating-flower.image-12 {
    /* เล็ก, เร็ว, ขวาบน */
    width: 120px; height: 120px; 
    top: 105%; 
    left: 70%; 
    animation-duration: 17s; 
    animation-delay: 9s; 
}

.floating-flower.image-13 {
    /* ใหญ่, ช้า, ซ้ายล่างสุด */
    width: 200px; height: 200px; 
    top: 155%; /* ลึกมาก */
    left: 10%; 
    animation-duration: 38s; 
    animation-delay: 4s; 
}

.floating-flower.image-14 {
    /* กลาง, เร็ว, ขวาล่าง */
   width: 160px; height: 160px; 
   top: 170%; 
   left: 80%; 
   animation-duration: 23s; 
   animation-delay: 11s; 
}

.floating-flower.image-15 {
    /* เล็กมาก, เร็ว, กลาง */
     width: 130px; height: 130px; 
    top: 150%; 
    left: 50%; 
    animation-duration: 19s; 
    animation-delay: 0s; /* เริ่มทันที */
}
/* Keyframes สำหรับการลอยขึ้นและหมุนสไตล์กลีบดอกไม้ (ใช้ keyframes เดิมที่คุณมีอยู่) */
/* Keyframes สำหรับการลอยขึ้นและหมุนสไตล์กลีบดอกไม้ */
@keyframes float-petal {
    0% {
        /* 🎯 เริ่มต้นที่ด้านล่างนอกจอ (100vh) และจางหายไป (opacity: 0) */
        transform: translate(0vw, 100vh) rotate(0deg) scale(0.7); 
        opacity: 0; /* ต้องเป็น 0 เพื่อให้เริ่มลอยขึ้นมาจาก 'ความว่างเปล่า' */
    }
    1% {
        /* 🎯 ปรากฏชัดเจนทันทีเมื่อเริ่มเข้าจอ */
        opacity: 1; 
    }
    
    25% {
        transform: translate(15vw, 75vh) rotate(90deg) scale(1.05); 
        opacity: 1; 
    }
    50% {
        transform: translate(-15vw, 50vh) rotate(180deg) scale(1.2); 
        opacity: 1; 
    }
    75% {
        transform: translate(10vw, 25vh) rotate(270deg) scale(1.1); 
        opacity: 1; 
    }
    99% { 
        /* 🎯 คงความชัดเจนไว้เกือบสุดทาง */
        opacity: 1; 
    }
    100% {
        /* 🎯 จบที่นอกจอ (ด้านบน) และหายไป (opacity: 0) เพื่อให้วนซ้ำได้เนียน */
        transform: translate(0vw, -125vh) rotate(360deg) scale(0.8);
        opacity: 0; /* ต้องเป็น 0 เพื่อให้หายไปก่อนเริ่มรอบใหม่ */
    }
}

/* Info box */
.game-info-box {
  border: 2px solid #f7b7c0;
  border-radius: 14px;
  padding: 10px 0;
  background: #fff;
  box-shadow: 0 3px 8px rgba(255, 155, 155, 0.25);
  
}

/* Label */
.info-label {
  margin-top: 6px;
  font-size: 15px;
  color: #555;
}

/* Card board */
.card-board {
  border: 3px solid #f2a7ac;
  background: #fff4f4;
  box-shadow: 0 0 15px rgba(255, 160, 160, 0.4);
}

/* Cards */
.card-item {
  position: relative;
  width: 100%;
  padding-top: 140%;
  cursor: pointer;
  perspective: 1000px;
  transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.27);
  transform-style: preserve-3d; 
}

.card-item:not(.flipped):not(.matched):not(.shuffling):hover {
    transform: scale(1.05) translateY(-5px); 
    box-shadow: 0 10px 20px rgba(255, 100, 150, 0.4);
}


/*.card-item:hover {
  transform: scale(1.07);
}*/

.card-front,.card-back
 {
  position: absolute;
  inset: 0;
  border-radius: 14px;
  border: 3px solid #00000039;
  backface-visibility: hidden;
  transform-style: preserve-3d;
  transition: transform 0.6s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.595);
}


.card-back {
  background-size: cover;
  background-position: center;
  transform: rotateY(0deg);
}


.card-front {
  background: white;
  border: 2px solid #ddd;
  font-size: 1.75rem;
  font-weight: bold;
  padding: 10px;
  transform: rotateY(180deg);
}


@media (max-width: 640px) {
  .card-item { padding-top: 120%; }
  .card-front { font-size: 1.1rem !important; padding: 6px; }
  .card-back { border-radius: 10px; }
}

.card-front.cn { color: #d92a2a; }
.card-front.th { color: #2a4ad9; }

.card-item.flipped .card-back {
  transform: rotateY(180deg);
}

.card-item.flipped .card-front {
  transform: rotateY(0deg);
}

/* Animation */
@keyframes card-bounce {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); }
}
.card-matched { animation: card-bounce 0.3s ease; }

@keyframes card-shake {
  0% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  50% { transform: translateX(5px); }
  75% { transform: translateX(-5px); }
  100% { transform: translateX(0); }
}
.card-wrong { animation: card-shake 0.3s ease; }

/* Buttons */
.btn-start, .btn-random {
  border-radius: 12px;
  padding: 12px 28px;
  font-size: 18px;
  font-weight: bold;
  transition: 0.25s;
  border-bottom: 4px solid rgba(0, 0, 0, 0.2);
  transform: translateY(0);
}
.btn-start { background: #ff94a0; color: white; }
.btn-start:hover { background: #ff7a8a;transform: translateY(-1px); }
.btn-random { background: #ff5252; color: white;border-bottom-color: #d94545; }
.btn-random:hover { background: #e84747; transform: translateY(-1px);}

.btn-start:active, .btn-random:active {
  transform: translateY(2px);
  border-bottom-width: 2px;
}
/* EXP badge */
.exp-boost {
  font-size: 22px;
  font-weight: 700;
  padding: 10px 20px;
  background: #ffd1d9;
  border-radius: 12px;
  cursor: pointer;
  padding-left: 15px; /* เพิ่ม padding ซ้าย */
}

.exp-boost::after { /* ใช้เป็นปุ่มสวิตช์ */
    content: '';
    position: absolute;
    right: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    transition: transform 0.3s;
}
.exp-boost.active-boost::after {
    /* เลื่อนปุ่มไปด้านซ้ายเมื่อ active */
    transform: translateX(-40px); 
    background: #4cd964; /* สีเขียวเมื่อ ON */
}
/* EXP Bar */
.exp-container {
  width: 125px;
  height: 15px;
  background: #fa768c;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
}
.exp-bar {
  height: 100%;
  background: #990a0a;
  transition: width 0.5s ease;
}
.exp-text {
  position: absolute;
  width: 100%;
  text-align: center;
  top: 0;
  line-height: 17px;
  font-weight: bold;
  color: white;
}

/* Popup */
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}
.popup-box h2 {
    color: #f6efef;
    margin-bottom: 20px;
    font-size: 2rem;
}
.popup-box button {
    margin-top: 20px;
    background: #efb3b3;
    color: white;
    padding: 10px 25px;
    border-radius: 10px;
    font-weight: bold;
    transition: 0.2s;
}
.popup-box button:hover {
    background: #e84747;
}

.result-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 1.1rem;
    color: #efb3b3;
    font-weight: 500;
    border-bottom: 1px dashed #f5c4ce;
    margin-bottom: 5px;
}
.result-row span:last-child {
    font-weight: bold;
    color: #ffffff;
}
@keyframes popup-show {
  0% { transform: scale(0.5); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

.exp-floating {
  position: fixed;
  bottom: 120px;
  left: 50%;
  transform: translateX(-50%);
  
  background: #ffbdd2;
  padding: 12px 22px;
  font-size: 22px;
  font-weight: 800;
  color: white;
  border-radius: 14px;

  animation: expFloat 1.2s ease forwards;
  z-index: 999;
}

@keyframes expFloat {
  0% { opacity: 0; transform: translate(-50%, 20px); }
  20% { opacity: 1; transform: translate(-50%, 0px); }
  100% { opacity: 0; transform: translate(-50%, -40px); }
}

.levelup-floating {
  position: fixed;
  top: 40%;
  left: 50%;
  transform: translateX(-50%);
  background: #ffebf2;
  color: #ff3e74;
  padding: 20px 32px;
  font-size: 28px;
  font-weight: 900;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(255, 100, 150, 0.35);

  animation: levelUpFloat 1.5s ease forwards;
  z-index: 99999;
}

@keyframes levelUpFloat {
  0% {
    opacity: 0;
    transform: translate(-50%, 20px);
  }
  20% {
    opacity: 1;
    transform: translate(-50%, 0);
  }
  100% {
    opacity: 0;
    transform: translate(-50%, -40px);
  }
}

.btn-hsk {
  padding: 8px 16px;
  border-radius: 9999px;
  border: 2px solid #ff94a0;
  background: #fff4f4;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
  box-shadow: 0 2px 4px rgba(255, 100, 100, 0.1);
}

.btn-hsk:hover {
  background: #ffccd6;
}

.btn-hsk.active {
  background: #ff94a0;
  color: white;
  border-color: #ff5252;
  box-shadow: 0 4px 6px rgba(255, 82, 82, 0.4);
  transform: translateY(-2px);
}

/* Side Panels */
.side-panel {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 140px;
}

.info-box {
  border: none;
  border-radius: 18px;
  padding: 14px 0;
  background: linear-gradient(145deg, #ffffff, #fff0f0);
 box-shadow: 0 6px 15px rgba(255, 155, 155, 0.15);
 border-bottom: 4px solid #f7b7c0;
  text-align: center;
  position: relative; /* สำคัญสำหรับการวาง Ornament */
}
.info-box::before {
    content: '';
    position: absolute;
    /* 🎯 ลายมุมกรอบสไตล์จีน */
    top: 5px;
    right: 5px;
    width: 15px;
    height: 15px;
    border-top: 2px solid #ff94a0; 
    border-right: 2px solid #ff94a0;
    border-radius: 0 10px 0 0;
}

.info-box::after {
    content: '';
    position: absolute;
    /* ลายมุมกรอบสไตล์จีน (ฝั่งตรงข้าม) */
    bottom: 5px;
    left: 5px;
    width: 15px;
    height: 15px;
    border-bottom: 2px solid #ff94a0; 
    border-left: 2px solid #ff94a0;
    border-radius: 0 0 0 10px;
}
.info-box .value {
  font-size: 1.6rem;
  font-weight: bold;
}

.info-box .label {
  margin-top: 6px;
  font-size: 14px;
  color: #666;
}

/* Responsive: ถ้ามือถือแคบเกิน ให้ลงด้านล่าง */
@media (max-width: 768px) {
  .side-panel {
    width: 100px;
  }
}

.card-explode {
  animation: explode 0.4s ease-out forwards;
}

@keyframes explode {
  0% { transform: scale(1); opacity:1; }
  100% { transform: scale(1.4); opacity:0; }
}

.card.shuffle {
  animation: shuffleAnim 0.15s linear infinite;
}

@keyframes shuffleAnim {
  0%   { transform: translateX(0) rotate(0deg); }
  25%  { transform: translateX(-3px) rotate(-2deg); }
  50%  { transform: translateX(3px) rotate(2deg); }
  75%  { transform: translateX(-2px) rotate(-1deg); }
  100% { transform: translateX(2px) rotate(1deg); }
}
/* --- Mascot Styling --- */
.mascot-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  transition: all 0.5s ease;
  z-index: 10;
  margin-top: 10px
  /* ปรับตำแหน่งให้เหมาะสม */
  /* margin-top: 50px; */ 
}

/* ซ่อน Mascot ในมือถือ */
@media (max-width: 1024px) {
    .hidden-mobile {
        display: none !important;
    }
    .card-board {
        width: 95% !important; /* ให้กระดานกินพื้นที่เยอะขึ้นบนมือถือ */
    }
}

.mascot-face {
  font-size: 3.5rem;
  line-height: 1;
  background: #ffffff;
  border-radius: 50%;
  padding: 10px;
  box-shadow: 0 4px 10px rgba(255, 100, 150, 0.3);
  animation: float 2s ease-in-out infinite;
}

.mascot-face.preview {
    animation: bounce 0.5s infinite;
}

.mascot-text {
  margin-top: 10px;
  background: #ffc9d6;
  color: #a00030;
  padding: 8px 12px;
  border-radius: 9999px;
  font-weight: bold;
  font-size: 0.9rem;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  white-space: nowrap; /* กันข้อความตกบรรทัด */
}

/* Floating animation */
@keyframes float {
  0% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
  100% { transform: translateY(0); }
}

/* Waving animation */
@keyframes wave {
  0%, 100% { transform: rotate(0deg); }
  50% { transform: rotate(10deg); }
}
.mascot-container.waving .mascot-face {
    /* ใช้คลาส .mascot-container แทน .mascot-left.waving */
    animation: float 2s ease-in-out infinite, wave 0.8s infinite alternate;
  transform-origin: 50% 80%;
}

/* Jumping animation (for game over) */
@keyframes jump {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-20px) scale(1.05); }
}
.mascot-container.jumping .mascot-face {
    /* ใช้คลาส .mascot-container แทน .mascot-right.jumping */
    animation: jump 0.4s infinite;
}

/* Global bounce (for preview) */
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

/* Animation for Card Item during Shuffle */
.card-item.shuffling {
    pointer-events: none;
    cursor: default;
    /* แอนิเมชันเขย่าเล็กน้อย */
    animation: shake 0.1s infinite alternate; 
}
@keyframes shake {
  0% { transform: translate(1px, 1px) rotate(0deg); }
  50% { transform: translate(-1px, -2px) rotate(-1deg); }
  100% { transform: translate(-3px, 0px) rotate(1deg); }
}

/* Animation for Game Board when shuffling */
.board-shuffling {
    box-shadow: 0 0 20px rgba(255, 0, 0, 0.5) !important;
    animation: pulsing-border 1.5s infinite;
}
@keyframes pulsing-border {
    0% { border-color: #f2a7ac; }
    50% { border-color: #ff5252; }
    100% { border-color: #f2a7ac; }
}


/* --- Light Shine on EXP Bar --- */
@keyframes shine-anim {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(150%); }
}

.exp-shine {
    position: absolute;
    top: 0;
    left: 0;
    width: 50%; /* ความยาวของแสง */
    height: 100%;
    /* แสง Gradient สีขาวเฉียง */
    background: linear-gradient(to right, rgba(234, 243, 64, 0) 0%, rgba(245, 249, 24, 0.8) 50%, rgba(255, 255, 255, 0) 100%);
    animation: shine-anim 0.75s ease-out; /* ความเร็วในการวิ่ง */
}

/* --- Screen Shake Effect --- */
@keyframes screen-shake {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    10% { transform: translate(-1px, 1px) rotate(-0.5deg); }
    20% { transform: translate(1px, -1px) rotate(0.5deg); }
    30% { transform: translate(-1px, 1px) rotate(-0.5deg); }
    40% { transform: translate(1px, -1px) rotate(0.5deg); }
    50% { transform: translate(-1px, 1px) rotate(-0.5deg); }
    60% { transform: translate(1px, -1px) rotate(0.5deg); }
    70% { transform: translate(-1px, 1px) rotate(-0.5deg); }
    80% { transform: translate(1px, -1px) rotate(0.5deg); }
    90% { transform: translate(-1px, 1px) rotate(-0.5deg); }
}

.shake-screen {
    animation: screen-shake 0.5s ease-out; /* ใช้ความเร็วปานกลาง */
}

/* ---------------------------------------------------- */
/*                     MOBILE STYLES                     */
/* ---------------------------------------------------- */

/* 1. Base Mobile Layout */
@media (max-width: 768px) {
    /* ปรับ Title และ Subtitle */
    h1 { font-size: 1.75rem !important; }
    p { font-size: 0.9rem; }

    /* ซ่อน Mascot เพื่อประหยัดพื้นที่ */
    .hidden-mobile {
        display: none !important;
    }

    /* 2. Main Game Wrapper: เปลี่ยนการจัดวางเป็นแนวตั้ง */
    .mt-5.flex.justify-center.gap-6.relative {
        flex-direction: column; /* เรียงจากบนลงล่าง */
        gap: 10px;
        align-items: center;
    }

    /* Side Panel: จัดเรียงใหม่ในแนวนอน (Grid) และย้ายไปอยู่ด้านบน */
    .side-panel {
        width: 100%; /* ให้กินพื้นที่เต็มความกว้าง */
        max-width: 400px; /* จำกัดความกว้างไม่ให้ยืดเกินไป */
        flex-direction: row; /* จัดเรียงองค์ประกอบในแนวนอน */
        justify-content: space-around; /* กระจายองค์ประกอบ */
        gap: 8px;
    }

    .info-box {
        padding: 10px 0;
        min-width: 75px; /* กำหนดความกว้างขั้นต่ำ */
        flex: 1; /* แบ่งพื้นที่เท่าๆ กัน */
    }

    .info-box .value {
        font-size: 1.2rem; /* ลดขนาดตัวเลข */
    }

    .info-box .label {
        font-size: 0.75rem; /* ลดขนาด Label */
        margin-top: 4px;
    }
    
    /* ซ่อนแถบ EXP ใน info-box เพื่อประหยัดพื้นที่ แต่แสดงตัวเลข */
    .info-box > .mt-4 { 
        display: none; 
    }
    
    /* 3. Card Board */
    .card-board {
        width: 100% !important; /* กินพื้นที่เต็มความกว้าง */
        max-width: 450px; /* จำกัดขนาดสูงสุด */
        padding: 10px; /* ลด padding */
    }

    .grid.grid-cols-4.gap-6 {
        gap: 8px; /* ลดช่องว่างระหว่างการ์ด */
    }
    
    /* Cards (ปรับขนาดตัวอักษรและ padding สำหรับมือถือ) */
    .card-item { padding-top: 130%; } /* ปรับอัตราส่วนการ์ด */
    .card-front { 
        font-size: 1.2rem !important; /* ลดขนาดตัวอักษร */
        padding: 5px; 
    }
    
    /* 4. Buttons: จัดเรียงเป็นคอลัมน์เดียว */
    .text-center.mt-5.flex {
        flex-direction: column;
        gap: 10px;
        align-items: center; /* จัดให้อยู่ตรงกลาง */
    }

    .btn-start, .btn-random {
        width: 90%; /* ให้ปุ่มกว้างขึ้น กดง่าย */
        max-width: 380px;
        padding: 10px 20px;
        font-size: 16px; /* ลดขนาดตัวอักษร */
    }
    
    .exp-boost {
        font-size: 18px; /* ลดขนาด EXP Boost */
        padding: 8px 15px;
        position: relative;
        padding-right: 45px; /* เพิ่มพื้นที่สำหรับปุ่มสวิตช์ */
    }

    /* 5. HSK Selector */
    .text-center.mt-6.flex {
        flex-direction: column;
    }
    .btn-hsk {
        margin: 4px;
    }

    /* 6. Floating Popups */
    .exp-floating {
        bottom: 80px; /* เลื่อนขึ้นเล็กน้อย */
        font-size: 18px;
    }

    .levelup-floating {
        font-size: 22px;
        padding: 15px 25px;
    }
}

/* 7. Floating Flowers (ปรับขนาดให้เล็กลงบนมือถือ) */
@media (max-width: 640px) {
    .floating-flower {
        width: 100px; /* ขนาดพื้นฐานเล็กลง */
        height: 100px;
    }
    
    /* ปรับขนาดดอกไม้แต่ละรูป */
    .floating-flower.image-1 { width: 150px; height: 180px; }
    .floating-flower.image-2 { width: 90px; height: 60px; }
    .floating-flower.image-3 { width: 180px; height: 180px; }
    .floating-flower.image-4 { width: 70px; height: 70px; }
    .floating-flower.image-5 { width: 110px; height: 110px; }
    .floating-flower.image-6 { width: 80px; height: 80px; }
    .floating-flower.image-7 { width: 120px; height: 120px; }
    .floating-flower.image-8 { width: 80px; height: 50px; }
    .floating-flower.image-9 { width: 100px; height: 100px; }
    .floating-flower.image-10 { width: 150px; height: 150px; }
}

/* --- NEW: Start Game Overlay --- */
.start-game-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9); /* สีพื้นหลังขาวโปร่งแสง */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 18px; /* ให้ตรงกับ Card Board */
    z-index: 20; /* ต้องสูงกว่าการ์ดแต่ต่ำกว่า pop-up อื่นๆ */
    text-align: center;
    padding: 20px;
    border: 1px  #ff94a0; /* เพิ่มขอบน่ารักๆ */
}

/* ปรับสีข้อความใน Overlay */
.start-game-overlay p {
    color: #ff5252;
}

/* ปรับให้ Overlay ดูนุ่มนวลขึ้น */
.card-board {
    position: relative; /* สำคัญ: เพื่อให้ Overlay ตำแหน่ง relative กับ Board */
}

/* --- NEW: Disabled Button Style (ใช้แทน :disabled) --- */
.disabled-btn {
    opacity: 0.5; /* ทำให้ปุ่มดูจางลง */
    cursor: not-allowed; /* เปลี่ยนเคอร์เซอร์เป็นไม่อนุญาต */
    pointer-events: all !important; /* ต้องเปิด pointer-events เพื่อให้ click event ทำงาน */
    transform: none !important; /* ลบ hover/active effect ออก */
    box-shadow: none !important;
}

.disabled-btn:hover {
    background: #ff94a0; /* ใช้สีพื้นหลังเดิมเพื่อไม่ให้ดูแปลก */
}

/* --- NEW: Disabled Alert Floating Popup --- */
.disabled-alert-floating {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    
    background: #fff4f4;
    border: 3px solid #ff5252;
    padding: 15px 30px;
    font-size: 1.2rem;
    font-weight: 700;
    color: #ff5252;
    border-radius: 16px;
    
    animation: alertFade 2s ease forwards;
    z-index: 10000;
}

@keyframes alertFade {
    0% { opacity: 0; transform: translate(-50%, 0); }
    10% { opacity: 1; transform: translate(-50%, -50%); }
    90% { opacity: 1; transform: translate(-50%, -50%); }
    100% { opacity: 0; transform: translate(-50%, -100px); }
}
</style>
