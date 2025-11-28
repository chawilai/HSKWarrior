<script setup>
import { onMounted, ref, watch } from 'vue';
import HanziWriter from 'hanzi-writer';

const props = defineProps({
  hanzi: {
    type: String,
    required: true,
  },
  pinyin: {
    type: String,
    required: true,
  },
  // **NEW PROP:** คำแปล/คำอธิบายที่ด้านล่างของการ์ด
  explanation: {
    type: String,
    default: '',
  },
  // **NEW PROP:** ระดับ HSK (เพื่อแสดงป้ายเล็กๆ ด้านบนซ้าย เช่น "HSK 3")
  hskLevel: {
    type: [String, Number],
    default: null,
  },
});

const writerRef = ref(null);
const writerInstance = ref(null);

onMounted(() => {
  // สร้าง HanziWriter
  writerInstance.value = HanziWriter.create(writerRef.value, props.hanzi, {
    width: 120,
    height: 120,
    padding: 5,
    showOutline: false,
    strokeColor: '#B71F1F', // สีแดงเข้ม HSKwarrior
    radicalColor: '#333333',
  });
});

watch(() => props.hanzi, (newHanzi) => {
  if (writerInstance.value) {
    writerInstance.value.setCharacter(newHanzi);
  }
});
</script>

<template>
  <div 
    class="relative w-48 h-64 flex flex-col p-4 transition-all duration-300 ease-out 
           hover:scale-[1.02] hover:-rotate-1 hover:shadow-[0_10px_20px_-5px_rgba(183,31,31,0.5)] 
           cursor-pointer border-4 border-[#B71F1F] rounded-xl  bg-[#FFF7E8]"
  >
    
    <div class="absolute inset-0">
        <div class="absolute inset-1.5 border border-[#F0D5BB] rounded-lg"></div>
        <div class="absolute top-0 left-0 w-4 h-4 border-t-4 border-l-4 border-[#B71F1F] rounded-tl-xl opacity-20"></div>
        <div class="absolute top-0 right-0 w-4 h-4 border-t-4 border-r-4 border-[#B71F1F] rounded-tr-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-4 h-4 border-b-4 border-l-4 border-[#B71F1F] rounded-bl-xl opacity-20"></div>
        <div class="absolute bottom-0 right-0 w-4 h-4 border-b-4 border-r-4 border-[#B71F1F] rounded-br-xl opacity-20"></div>
    </div>

    <div class="relative flex justify-between items-center z-10 pt-1 px-1">
      <div v-if="hskLevel">
      </div>
      <div v-else class="h-4 w-4"></div> <div class="text-[#B71F1F] font-bold text-lg uppercase leading-none">{{ pinyin }}</div>
    </div>

    <div ref="writerRef" class="flex-grow flex items-center justify-center p-2 z-10">
      </div>

    <div v-if="explanation" class="relative z-10 pb-2 px-1">
      <div class="text-xs font-medium text-[#B71F1F] bg-[#FEEBC8] border-2 border-[#B71F1F] rounded-lg py-1 px-3 text-center shadow-inner uppercase tracking-wider">
        {{ explanation }}
      </div>
    </div>
    
    <img
      class="absolute -bottom-3 -left-3 w-10 h-auto -rotate-12 opacity-90 z-20"
      src="@/../images/img/object/lantern_1.png"
      alt="Chinese Lantern"
    />
  </div>
</template>