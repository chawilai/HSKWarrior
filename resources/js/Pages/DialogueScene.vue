<script setup>
import { computed } from 'vue';

const props = defineProps({
  // รูปตัวละครซ้าย
  leftCharSrc: { type: String, required: true },
  // รูปตัวละครขวา
  rightCharSrc: { type: String, required: true },
  // ข้อมูลบทสนทนา { name: 'ชื่อคนพูด', text: 'ข้อความ', side: 'left' | 'right' }
  dialogueData: {
    type: Object,
    default: () => ({ name: '???', text: '...', side: 'left' })
  },
  // สถานะเปิดใช้งาน (เพื่อทำ Animation เข้าฉาก)
  isVisible: { type: Boolean, default: false }
});

// ตรวจสอบว่าใครกำลังพูด
const isLeftSpeaking = computed(() => props.dialogueData.side === 'left');
const isRightSpeaking = computed(() => props.dialogueData.side === 'right');

</script>

<template>
  <div class="absolute inset-0 w-full h-full pointer-events-none overflow-hidden">
    
    <div class="relative w-full h-full flex justify-between items-end px-4 md:px-16 pb-32 md:pb-40">
      
      <div 
        class="transition-all duration-500 ease-out transform origin-bottom z-10 flex-1 flex justify-star"
        :class="[
           isVisible ? 'translate-x-0 opacity-100' : '-translate-x-20 opacity-0',
           isLeftSpeaking ? 'scale-105 brightness-110 drop-shadow-2xl z-20' : 'scale-95 brightness-50 grayscale-[30%]'
        ]"
      >
        <img 
          :src="leftCharSrc" 
          class="h-[350px] sm:h-[450px] md:h-[55vh] w-auto"
          alt="Left Character"
        />
      </div>

      <div 
        class="transition-all duration-500 ease-out transform origin-bottom z-10 flex-1 flex justify-end"
        :class="[
           isVisible ? 'translate-x-0 opacity-100' : 'translate-x-20 opacity-0',
           isRightSpeaking ? 'scale-105 brightness-110 drop-shadow-2xl z-20' : 'scale-95 brightness-50 grayscale-[30%]'
        ]"
      >
        <img 
          :src="rightCharSrc" 
          class="h-[350px] sm:h-[450px] md:h-[50vh] w-auto" 
          alt="Right Character"
        />
      </div>

    </div>

    <div 
       class="absolute bottom-4 md:bottom-8 left-0 w-full flex justify-center px-4 z-50 transition-all duration-500"
       :class="isVisible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
    >
      <div class="pointer-events-auto w-full max-w-4xl bg-black/80 backdrop-blur-md border-2 border-white/20 rounded-xl md:rounded-2xl p-4 md:p-6 shadow-2xl relative">
        
        <div 
            class="absolute -top-5 left-6 bg-red text-white px-4 py-1 rounded-lg text-lg font-bold shadow-lg border border-red-800 transition-all duration-300"
            :class="isRightSpeaking ? 'left-auto right-6 bg-blue-600 border-blue-800' : ''"
        >
            {{ dialogueData.name }}
        </div>

        <p class="text-white text-lg md:text-2xl leading-relaxed mt-2 font-medium min-h-[3rem]">
            {{ dialogueData.text }}
        </p>

        <div class="absolute bottom-2 right-4 text-white/50 animate-pulse text-sm">
            แตะเพื่อไปต่อ ▶
        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* ปรับแต่งเพิ่มเติมถ้าจำเป็น */
</style>