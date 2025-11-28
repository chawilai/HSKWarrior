<script setup>
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted,computed } from "vue";
import 'animate.css';

import Typewriter from "typewriter-effect/dist/core";

import StarIcon from "@/../icons/star.svg";
import FuelerIcon from "@/../icons/fueler.svg";

import bgSoundPath from '@/../sound/soundbg1.mp3';

import DialogueScene from '@/Pages/DialogueScene.vue';

const dialogues = [
    { name: 'ศิษย์พี่', text: 'เจ้าหนู! นั่นมันปีศาจหนูระดับ 1 ระวังตัวด้วย!', side: 'left' },
    { name: 'ปีศาจหนู', text: 'จี๊ดๆๆ!! ข้าจะขโมยตำราเรียนจีนของเจ้า!', side: 'right' },
    { name: 'ศิษย์พี่', text: 'ใช้วิชา "คำศัพท์พื้นฐาน" จัดการมันซะ!', side: 'left' },
];


import HeroImg from '@/../images/player1.png';
import RatMonsterImg from '@/../images/rat1.png'; // สมมติว่ามีรูปปีศาจ

// 2. สร้างตัวคำนวณว่า "ด้านซ้าย" หน้าปัจจุบันควรเป็นใคร
const currentLeftChar = computed(() => {
    if (currentPage.value === 3) return HeroImg; // หน้า 3: อาจารย์ยืนซ้าย
    if (currentPage.value === 4) return HeroImg;   // หน้า 4: พระเอกย้ายมายืนซ้าย
    return MasterImg; // ค่า Default
});

// 3. สร้างตัวคำนวณว่า "ด้านขวา" หน้าปัจจุบันควรเป็นใคร
const currentRightChar = computed(() => {
    if (currentPage.value === 3) return RatMonsterImg;       // หน้า 3: พระเอกยืนขวา
    if (currentPage.value === 4) return RatMonsterImg; // หน้า 4: ปีศาจยืนขวา
    return HeroImg; // ค่า Default
});

const dialogueIndex = ref(0);
const currentDialogue = computed(() => dialogues[dialogueIndex.value]);

const nextDialogue = () => {
    if (dialogueIndex.value < dialogues.length - 1) {
        dialogueIndex.value++;
    } else {
        goNextPage();
    }
};

const bgMusic = new Audio(bgSoundPath);
bgMusic.loop = true;   // ตั้งให้เล่นวนลูป
bgMusic.volume = 0.4;  // ปรับความดัง 0.0 - 1.0 (แนะนำ 0.4 ไม่ดังแสบหูเกินไป)

const toggleSound = () => {
    isMuted.value = !isMuted.value;
    bgMusic.muted = isMuted.value;
};

const isMuted = ref(false); // เริ่มต้นเสียงเปิดอยู่

defineOptions({ layout: OrganicLayout });

// // avatar images
// const avatarImports = import.meta.glob("../../images/img/avatar-*.jpeg", {
//     eager: true,
// });

// const avatars = ref({});

// Object.keys(avatarImports).forEach((path) => {
//     const match = path.match(/avatar-(\d+)\.jpeg$/);
//     if (match) {
//         avatars.value[match[1]] = avatarImports[path].default;
//     }
// });

// const getAvatarSrc = (index) => avatars.value[index] || "";
// // avatar images

// ✅ เพิ่มส่วนนี้เข้าไปครับ
// กำหนดเลขหน้า ที่ "ไม่ต้องการ" ให้มีกล่องข้อความ
const noBoxPages = [3, 4, 5, 8]; 

// สร้างตัวคำนวณอัตโนมัติว่าหน้านี้ต้องโชว์กล่องไหม
const showTextBox = computed(() => {
    // ถ้าเลขหน้าปัจจุบัน ไม่อยู่ในลิสต์ noBoxPages -> ให้โชว์กล่อง (True)
    return !noBoxPages.includes(currentPage.value);
});

let typeWords = (el_id, text_arr = []) => {
  let typewriter = new Typewriter(document.getElementById(el_id), {
    loop: true, // false,
    delay: 100, // 50,
    deleteSpeed: 50, // 50,
    cursor: "|", // "|",
    cursorClassName: "typewriter-cursor", // "typewriter-cursor",
    autoStart: false, // false,
    strings: [], // [],
  });

  text_arr.forEach((word, index) => {
    typewriter.typeString(word).pauseFor(500).deleteAll().pauseFor(500);
  });
  typewriter.start();
};

// function speak(text) {
//     if ('speechSynthesis' in window) {
//         var msg = new SpeechSynthesisUtterance(text);
//         window.speechSynthesis.speak(msg);

//         alert('support speech synthesis.');

//     } else {
//         alert('Your browser does not support speech synthesis.');
//     }
// }

// Example usage

// เพิ่มตัวแปรสำหรับควบคุม "หน้า" (page) ภายใน drawer
// ===> ตัวแปร State สำหรับคุมเนื้อเรื่อง <===
const storyStep = ref(0); // 0=เริ่ม, 1=ซูม, 2=รูปสอง, 3=รูปสาม

const isCinematicMode = ref(false);

const currentPage = ref(1); // หน้าปัจจุบัน
const maxPage = 3; // จำนวนหน้ารวมสมมติ

// ref สำหรับอ้างถึง element ที่จะทำ animation
const contentContainerRef = ref(null);

const isZoomed = ref(false); // <--- เพิ่มตัวนี้
const backgroundStage = ref(1);

const isBlurred = ref(false);

// ฟังก์ชันสำหรับกดไปหน้าต่อไปพร้อม Animation
const goNextPage = () => {
    if (currentPage.value < maxPage) {
        // 1. นำ Animation ออกก่อน
        const container = document.getElementById('drawer-content-container');
        if (container) {
            container.classList.remove('animate__animated', 'animate__slideInRight');
            isZoomed.value = false; // รีเซ็ตสถานะซูมก่อนเปลี่ยนหน้า
        }

        // 2. เปลี่ยนสถานะหน้า
        currentPage.value++;

        setTimeout(() => {
            if (container) {
                // ใส่ Animation เข้ามาใหม่
                container.classList.add('animate__animated', 'animate__slideInRight');
            }
        }, 10);
    } else {
        alert("ถึงหน้าสุดท้ายแล้ว! จะพาไปสู่การผจญภัย!");
        closeDrawer();
    }
};

onMounted(() => {
  typeWords("service_typing", [
    "แบบเรียน HSK 1-6",
    "แบบฝึกหัด HSK 1-6",
    "Flash Card ทายคำ",
    "ฝึก Pīnyīn",
    "หมวดหมู่คำศัพท์",
    "ทายประโยค",
    "ฝีกเขียนจีน Hànzì",
    "แข่งกับเพื่อน",
  ]);
});



// ===> โค้ดสำหรับ Drawer <===
const isDrawerOpen = ref(false);

// 🌟🌟🌟 แก้ไข Logic การ Chaining Timers สำหรับ Cinematic Blur 🌟🌟🌟
const openDrawer = () => {
    isDrawerOpen.value = true;

    // ✅ เพิ่มบรรทัดนี้: รีเซ็ตบทสนทนาเป็นประโยคแรก
    dialogueIndex.value = 0;

    bgMusic.currentTime = 0; // เริ่มเล่นจากวินาทีที่ 0 เสมอ
    bgMusic.muted = isMuted.value; // set ตามสถานะปัจจุบัน
    bgMusic.play().catch(error => {
        console.log("Audio play failed (user interaction needed):", error);
    });


    currentPage.value = 1; 
    storyStep.value = 0;
    backgroundStage.value = 1; // Stage 1: 1.jpg
    isZoomed.value = false;
    isBlurred.value = false; // 🌟 รีเซ็ตสถานะเบลอ
    isCinematicMode.value = false;

    // *** กำหนดเวลาสำหรับ Cinematic Sequence ***
    //const delayStart = 3000; // 2.0s: Stage 1 (1.jpg) นิ่ง
    //const zoomDuration = 2500; // 2.5s: เวลาซูม (Stage 2)
    //const holdPage2 = 2500; // 2.5s: พักที่ภาพซูม
    //const blurDuration = 500; // 0.5s: เวลา Transition เบลอ (ตาม CSS)
    //const buffer = 50; // 0.05s: พักที่จอดำก่อนเปลี่ยนภาพ (เพื่อความแน่นอน)

   // 1. Stage: เริ่ม Zoom In รูป 1 (ที่วินาทีที่ 2.0)
    //setTimeout(() => {
        //isZoomed.value = true; // 🌟 สั่งให้ CSS เริ่มซูมรูป 1
    //}, delayStart); 

    // 2. Stage: ซูมเสร็จแล้ว -> เปลี่ยนเป็นรูป 2 (ที่วินาทีที่ 4.5)
    //setTimeout(() => {
        //backgroundStage.value = 2; // 🌟 เปลี่ยนเป็นรูป 2
        
    //}, delayStart + zoomDuration); 

    // 3. Stage: รูป 2 เบลอ (ที่วินาทีที่ 7.0)
    //setTimeout(() => {
        //isBlurred.value = true; // 🌟 รูป 2 เริ่มเบลอ
    //}, delayStart + zoomDuration + holdPage2); 

    // 4. Stage: เปลี่ยนเป็นรูป 3 + หายเบลอ (ที่วินาทีที่ 7.55)
    //setTimeout(() => {
        //backgroundStage.value = 3; // 🌟 เปลี่ยนเป็นรูป 3
        
        //setTimeout(() => {
          //isZoomed.value = false;    // รีเซ็ตค่าซูม (เพราะรูป 2 มาแล้ว)
            //isBlurred.value = false; // 🌟 หายเบลอ (Reveal รูป 3)
        //}, buffer);

    //}, delayStart + zoomDuration + holdPage2 + blurDuration + buffer); 
};

const handleNextStep = () => {
    // กรณีหน้า 1: Intro
    if (currentPage.value === 1) {
        
        // ถ้าอยู่จุดเริ่มต้น (Step 0) ให้เริ่มเข้าโหมดหนัง
        if (storyStep.value === 0) {
            // 1. ซ่อน UI เข้าโหมดหนัง
            isCinematicMode.value = true; 
            storyStep.value = 1; 
            isZoomed.value = true; // เริ่มซูม

            // 2. รอ 2.5 วิ -> เปลี่ยนภาพเป็นลานฝึก (Step 2)
            setTimeout(() => {
                storyStep.value = 2; // Reset Zoom
                backgroundStage.value = 2; // เปลี่ยนภาพ

                // 3. รออีก 2.5 วิ -> เริ่มเบลอภาพ (เตรียมเข้า Step 3)
                setTimeout(() => {
                    isBlurred.value = true;

                    // 4. รอ 0.5 วิ (Effect Blur) -> เปลี่ยนภาพ 3 + เรียก UI กลับมา
                    setTimeout(() => {
                        backgroundStage.value = 3; 
                        storyStep.value = 3; // จบ Step
                        
                        // หายเบลอ + โชว์กล่องข้อความ
                        setTimeout(() => {
                            isZoomed.value = false;
                            isBlurred.value = false;
                            isCinematicMode.value = false; // 🌟 เรียก UI กลับมาตรงนี้
                        }, 100);

                    }, 500); 

                }, 2500); // เวลาโชว์ภาพลานฝึก

            }, 2500); // เวลาซูมภาพแรก
        } 
        // ถ้าจบฉากหนังแล้ว (Step 3) กดอีกทีไปหน้า 2
        else if (storyStep.value === 3) {
            goNextPage(); 
        }

    } else {
        // หน้าอื่นๆ
        goNextPage();
    }
};

const closeDrawer = () => {
    isDrawerOpen.value = false;

    // --- 🔇 สั่งหยุดเพลง ---
    bgMusic.pause();
    bgMusic.currentTime = 0; // รีเซ็ตเพลงเผื่อเล่นใหม่
    // -------------------

    setTimeout(() => {
      storyStep.value = 0;
    isZoomed.value = false; 
    backgroundStage.value = 1; 
    currentPage.value = 1; 
    isBlurred.value = false; // 🌟

    // ✅ เพิ่มบรรทัดนี้: รีเซ็ตบทสนทนาเผื่อไว้ด้วยตอน Animation ปิดจบ
        dialogueIndex.value = 0;
    }, 300);
};
</script>

<template>
  <Head title="Home" />

  <div
  
    class="flex flex-wrap-reverse gap-y-24 justify-between py-14 px-6 mx-auto max-w-screen-xl sm:px-8 md:px-12 lg:px-16 xl:px-24"
    
  >
    <div class="relative z-10 md:w-1/2 w-full">
      <img
        class="absolute z-20 -top-6 right-4 md:right-14 md:-top-8 w-14 h-auto rotate-12 hover:scale-125 duration-300"
        src="@/../images/img/object/lantern_1.png"
        alt=""
      />
      <img
        class="absolute z-20 bottom-6 left-0 md:bottom-20 md:left-0 w-14 h-auto -rotate-12 hover:scale-125 duration-300"
        src="@/../images/img/object/lantern_1.png"
        alt=""
      />
      <span class="flex items-center px-1 text-xl text-red">
        <span class="font-medium">🚀🇨🇳 ฝึกฝนจนเป็นจอมยุทธ! 🇹🇭💪</span>
        <!-- <img
                        class="w-auto h-8"
                        src="@/../images/img/vegetable.png"
                        alt=""
                    /> -->
      </span>
      <h1
        class="pt-4 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-none whitespace-nowrap"
      >
        <span class="whitespace-nowrap text-red"> HSK </span>
        <span class="whitespace-nowrap">Warrior</span><br />
        <span class="whitespace-nowrap">สนุกกับภาษาจีน</span>
      </h1>
      <p class="pt-8 sm:text-lg max-w-md font-normal text-gray-600 leading-relaxed">
        <span class="whitespace-nowrap">ผ่านแบบเรียน แบบฝึกหัด และเกมสนุก ๆ</span>
        <span class="whitespace-nowrap"
          >พร้อมบันทึกประวัติการฝึกฝน เพิ่มระดับเลเวลของคุณ</span
        >
        <span class="whitespace-nowrap">จนเป็นนักรบที่แข็งแกร่ง</span>
      </p>

      <div class="flex sm:ml-20 pt-4 font-sans font-semibold space-x-4 sm:space-x-6">
        <div id="service_typing" class="mt-5 text-2xl text-center text-red"></div>
      </div>

      <div class="flex sm:ml-24 pt-8 space-x-4 sm:space-x-6">
        <button
          @click="openDrawer"
          role="button"
          class="flex justify-center items-center w-full sm:w-auto h-16 px-7 py-2 text-xl font-medium hover:-rotate-3 transition-all ease-out duration-300 text-base font-semibold leading-7 text-white bg-red border border-red rounded-lg focus:outline-red focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 hover:bg-transparent hover:text-red sm:w-auto hover:scale-[1.01] focus:bg-transparent focus:text-red hover:shadow-hsk whitespace-nowrap"
        >
          เริ่มผจญภัย
        </button>
        <!-- <button
                        class="flex justify-center items-center w-full sm:w-auto h-13 px-8 font-medium text-gray-900 border border-gray-900 rounded-xl whitespace-nowrap hover:shadow-xl transition-shadow duration-300"
                    >
                        Explore menu
                    </button> -->
      </div>
      <div
      class="fixed inset-0 z-[9999] transform transition-transform duration-500 flex items-center justify-center px-2 py-2 md:px-0 md:py-0"
      :class="isDrawerOpen ? 'translate-y-0' : 'translate-y-full'"
    >
      <div 
        @click="closeDrawer" 
        class="absolute inset-0 bg-black/60 transition-opacity duration-300 backdrop-blur-sm"
        :class="isDrawerOpen 
              ? 'opacity-100 duration-1000 delay-300' 
              : 'opacity-0 duration-300'"
      ></div>

      <div 
        class="relative w-full h-full md:w-[90%] md:h-[85%] max-w-7xl drawer-bg overflow-hidden rounded-2xl md:rounded-[2rem] border-4 md:border-[6px] border-[#7f1d1d] shadow-[0_0_50px_rgba(0,0,0,0.7)]"
        :class="{
            'bg-page-1': currentPage === 1 && backgroundStage === 1,
            'is-zoomed': currentPage === 1 && isZoomed,
            'bg-page-2': currentPage === 1 && backgroundStage === 2,
            'bg-page-3': (currentPage === 1 && backgroundStage === 3) ,
            'bg-page-2-content': currentPage === 2,
            'bg-page-3-content': currentPage === 3,
            'is-blurred': isBlurred
        }"
      >
      <button 
    @click="toggleSound" 
    class="absolute top-2 left-2 md:top-4 md:left-4 z-50 btn btn-circle btn-sm md:btn-md btn-ghost text-white bg-black/20 hover:bg-black/40 border border-white/20"
>
    <svg v-if="!isMuted" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
    </svg>

    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 drop-shadow-md text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
    </svg>
</button>
        <button @click="closeDrawer" class="absolute top-2 right-2 md:top-4 md:right-4 z-50 btn btn-circle btn-sm md:btn-md btn-ghost text-white bg-black/20 hover:bg-black/40 border border-white/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-8 md:w-8 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <div id="drawer-content-container" class="w-full h-full relative flex flex-col justify-end pb-2 md:pb-6" v-cloak>
            
    <template v-if="currentPage === 3">
        <DialogueScene 
            :isVisible="true"
            :leftCharSrc="currentLeftChar" 
            :rightCharSrc="currentRightChar"
            :dialogueData="currentDialogue"
        />
        <div 
            @click="nextDialogue"
            class="absolute inset-0 z-[60] cursor-pointer"
            title="คลิกเพื่ออ่านต่อ"
        ></div>
    </template>

    <template v-else>
        <div class="w-full max-w-5xl mx-auto px-2 md:px-4 mb-2 md:mb-8 z-40 animate__animated animate__fadeInUp">
            
            <div v-if="!isCinematicMode" 
                class="p-4 md:p-8 flex flex-col md:flex-row gap-3 md:gap-6 items-end justify-between transition-all duration-300"
                :class="showTextBox ? 'bg-black/75 backdrop-blur-md border border-white/20 rounded-xl md:rounded-2xl shadow-2xl min-h-[auto] md:min-h-[160px]' : ''">
                
                <div class="flex-1 text-white text-base md:text-2xl font-medium leading-relaxed tracking-wide w-full">
                    
                    <template v-if="currentPage === 1">
                        <p v-if="storyStep === 0" class="animate__animated animate__fadeIn">
                            "ดินแดนจงหยวนเคยรุ่งเรือง... <br>
                            <span class="text-gray-300 text-sm md:text-base">ผู้คนใช้ชีวิตอย่างเรียบง่ายภายใต้ฟ้าดินที่สงบสุข"</span>
                        </p>
                        <p v-else-if="storyStep === 3" class="animate__animated animate__fadeIn">
                            <span class="text-red-400 font-bold text-xl md:text-3xl">ทว่า!</span> <br>
                            'ภาษาแห่งการควบคุม' ที่บรรพบุรุษใช้สะกดพลังเวทมนตร์กลับถูกลืมเลือน...
                        </p>
                    </template>

                    <template v-else-if="currentPage === 2">
                        <p class="animate__animated animate__fadeIn">
                            นั่นมันหนูไร้มารยาท 鼠精 (Shŭ Jīng) <br>
                            </p>
                    </template>

                    <template v-else>
                         </template>

                </div>

                <div class="shrink-0 w-full md:w-auto flex justify-end mt-2 md:mt-0">
                    <button 
                        @click="handleNextStep"
                        class="group relative flex items-center justify-center gap-2 w-full md:w-auto px-6 py-2 md:px-8 md:py-3 bg-red text-white text-lg md:text-xl font-bold rounded-lg overflow-hidden transition-all hover:scale-105 active:scale-95 shadow-lg border border-red hover:bg-transparent hover:text-red focus:ring-4 focus:ring-red/30"
                    >
                        <span class="relative z-10">
                            {{ currentPage === 1 && storyStep < 3 ? 'ต่อไป' : (currentPage < maxPage ? 'ไปต่อ' : 'เริ่มเลย!') }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 relative z-10 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                        <div class="absolute inset-0 h-full w-full scale-0 rounded-lg transition-all duration-300 group-hover:scale-100 group-hover:bg-white/10"></div>
                    </button>
                </div>

            </div>
            
        </div>
    </template>

</div>
      </div>
    </div>

      <div v-if="false">
        <div class="flex md:hidden pt-8 justify-end space-x-1 font-bold">
          <span>Powered by</span>
          <FuelerIcon class="w-6 h-6 text-gray-900 fill-current" />
          <span>Fueler</span>
        </div>
      </div>
    </div>

    <div class="relative md:w-1/2 w-full flex flex-col justify-between">
      <img
        class="w-96 lg:w-full drop-shadow-2xl self-center lg:self-end animate-up-down"
        src="@/../images/warrior_exam.png"
        alt=""
      />
      <div
        class="absolute right-0 md:-right-10 lg:-right-6 -top-16 md:-top-24 lg:-top-16 flex flex-col py-5 px-7 rounded-2xl shadow-xl bg-white/40 hover:bg-white/80 backdrop-blur-md hover:-translate-y-2 hover:shadow-2xl transition-all duration-300 group/card"
      >
        <div class="flex justify-center space-x-1">
          <div
            class="w-13 h-13 bg-white/80 group-hover/card:border-red/80 rounded-2xl border-2 border-white object-cover overflow-hidden"
          >
            <img src="@/../images/img/object/calculator.png" alt="" />
          </div>
          <div
            class="w-13 h-13 bg-white/80 group-hover/card:border-red/40 rounded-2xl border-2 border-white object-cover overflow-hidden"
          >
            <img src="@/../images/img/object/fan_1.png" alt="" />
          </div>
          <div
            class="w-13 h-13 bg-white/80 group-hover/card:border-red/40 rounded-2xl border-2 border-white object-cover overflow-hidden"
          >
            <img src="@/../images/img/object/lantern_2.png" alt="" />
          </div>
        </div>
        <div class="pt-3 font-bold">สร้างโปรไฟล์เพื่อบันทึกเลเวล</div>
        <div class="flex items-center text-gray-600 leading-relaxed">
          <StarIcon class="w-5 h-5" />
          <StarIcon class="w-5 h-5" />
          <span class="pl-1">ระดับ 16 (ในจังหวัด)</span>
        </div>
      </div>
      <div
        class="absolute left-0 bottom-0 md:-bottom-6 lg:-left-16 lg:bottom-16 flex rounded-2xl shadow-xl bg-white/40 hover:bg-white/80 backdrop-blur-md hover:-translate-y-2 hover:shadow-2xl transition-all duration-300"
      >
        <div class="flex items-center gap-x-2">
          <img
            class="w-auto h-20 ml-3"
            src="@/../images/img/object/object_1.png"
            alt=""
          />
          <div class="pr-7 pl-2 py-5">
            <div class="font-bold text-red">เรียนด้วย Game</div>
            <div class="text-gray-600 leading-relaxed">เพียง 10 นาทีต่อวัน 🔥</div>
          </div>
        </div>
      </div>
      <img
        class="absolute z-5 bottom-16 right-4 md:right-14 md:bottom-18 w-14 h-auto rotate-12 hover:scale-125 duration-300"
        src="@/../images/img/object/lantern_1.png"
        alt=""
      />
      <div class="hidden md:flex justify-end space-x-1 font-bold" v-if="false">
        <span>Powered by</span>
        <FuelerIcon class="w-6 h-6 text-gray-900 fill-current" />
        <span>Fueler</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* --- 1. สไตล์พื้นฐานและ Overlay --- */
.drawer-bg {
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    
    /* 🌟🌟 เพิ่ม filter และ transition สำหรับ blur 🌟🌟 */
    filter: blur(0px); /* ค่าเริ่มต้น */
    transition: 
        background-image 0.1s linear, /* เปลี่ยนภาพเร็วที่สุด */
        background-size 2.5s ease-in-out,
        filter 0.5s ease-out; /* Transition 0.5 วินาทีสำหรับการเบลอ */
    
    color: #333; 
}

.drawer-bg.is-blurred {
    filter: blur(10px); /* ปรับความเบลอตามต้องการ */
}

/* สร้าง Overlay สีขาวทับบนรูปภาพ */
.drawer-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    /* 🎨 ปรับให้เป็นสีดำจางๆ (Black Overlay) เพื่อให้ภาพเบลอดูมืดลงเหมือนฉาก Cinematic */ 
    /*background-color: rgba(0, 0, 0, 0.4);*/ 
    z-index: 0; 
}

/* ทำให้เนื้อหาหลักอยู่เหนื่อ Overlay */
#drawer-content-container {
    position: relative;
    z-index: 10; 
}

/* --- 2. รูปภาพสำหรับแต่ละหน้า --- */

/* 1. ภาพเริ่มต้นของ Drawer (Page 1 Stage 1) */
.bg-page-1 {
    background-image: url('@/../images/1.jpg'); 
    background-size: 100% 100%; /* เพิ่มตรงนี้ */
}

/* 2. ภาพที่ 2 ที่จะเริ่มซูม (Page 1 Stage 2) */
.bg-page-2 {
    background-image: url('@/../images/2.jpg'); 
    background-size: 100% 100%; /* รีเซ็ตขนาดกลับมาเต็มจอสำหรับรูป 2 */
}

.bg-page-2-content {
    background-image: url('@/../images/4.png'); 
    background-size: 100% 100%; /* รีเซ็ตขนาดกลับมาเต็มจอสำหรับรูป 2 */
}

/* 🌟 3. Stage 3 (3.jpg) */
.bg-page-3-intro {
     background-image: url('@/../images/3.png'); 
     background-position: center center; 
     
}

.bg-page-3-content {
    background-image: url('@/../images/4.png'); 
    background-size: 100% 100%; /* รีเซ็ตขนาดกลับมาเต็มจอสำหรับรูป 2 */
}

/* 4. สไตล์สำหรับสถานะซูมเข้า (is-zoomed) */
.drawer-bg.is-zoomed { 
    /* ซูมเข้า (จาก 100% 100% เป็น 120% 120%) */
    background-size: 130% 130%;
    background-position: center center; 
}


.bg-page-3 {
    /* 🛠️ เพิ่ม Path รูปภาพหน้า 3 */
    background-image: url('@/../images/3.png'); /* **กรุณาเปลี่ยนเป็น path รูปภาพหน้า 3 ของคุณ** */
}

.bg-page-4 {
    /* 🛠️ เพิ่ม Path รูปภาพหน้า 3 */
    background-image: url('@/../images/4.png'); /* **กรุณาเปลี่ยนเป็น path รูปภาพหน้า 3 ของคุณ** */
}
/* ===== Hero center + floating animation ===== */
.page1-hero {
  /* ใช้ height สูงพอให้ดูเป็นศูนย์กลางแบบ cinematic */
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ทำให้ตัวละครลอยขึ้นลงเล็กน้อย */
@keyframes floatY {
  0% { transform: translateY(0px) translateX(0px) rotate(0deg); }
  25% { transform: translateY(-12px) translateX(-4px) rotate(-1deg); }
  50% { transform: translateY(-20px) translateX(-8px) rotate(0deg); }
  75% { transform: translateY(-12px) translateX(-4px) rotate(1deg); }
  100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
}

/* นิยาม class สำหรับรูปตัวละคร */
.hero-character {
  animation: floatY 3.6s ease-in-out infinite;
  will-change: transform;
  /* ถ้าต้องการให้บินเข้ามาด้วย fade-in จากซ้าย ให้เพิ่มด้านล่าง */
  animation-name: floatY, heroEntrance;
  animation-duration: 3.6s, 0.8s;
  animation-timing-function: ease-in-out, cubic-bezier(.2,.9,.3,1);
  animation-iteration-count: infinite, 1;
}

/* entrance เล็กๆ จากซ้าย (เล่นครั้งเดียว) */
@keyframes heroEntrance {
  0% { opacity: 0; transform: translateX(-80px) scale(0.98); }
  60% { opacity: 1; transform: translateX(6px) scale(1.02); }
  100% { opacity: 1; transform: translateX(0px) scale(1); }
}

/* ปรับขนาดกล่องให้โฟกัสตรงกลาง และให้มีการจัดปุ่มกลาง */
.hero-card {
  text-align: center;
}

/* Responsive tweaks */
@media (max-width: 767px) {
  .hero-character { width: 90% !important; }
  .hero-card { width: 95% !important; padding: 18px; margin-top: -10px; }
  .page1-hero { height: auto; padding: 30px 0; }
}

.drawer-slide-enter-active,
.drawer-slide-leave-active {
  transition: all 0.35s ease;
  position: absolute;
  width: 100%;
}

.drawer-slide-enter-from {
  opacity: 0;
  transform: translateX(50px);
}

.drawer-slide-leave-to {
  opacity: 0;
  transform: translateX(-50px);
}


</style>
