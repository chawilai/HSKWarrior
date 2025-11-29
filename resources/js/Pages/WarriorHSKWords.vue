<script setup>
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePlaySound } from "@/composables/usePlaySound.js";
import HanziWriterCharacter from "@/Components/HanziWriterCharacter.vue";
import axios from "axios";

const props = defineProps({
    words_list: Array,
    current_level: String,
});

const { playSound, playingWord } = usePlaySound();

const showPinyin = ref(true);
const showMeaning = ref(true);

const hskLevels = [
    { id: "HSK 1", name: "HSK 1", color: "bg-blue-100 text-blue-700" },
    { id: "HSK 2", name: "HSK 2", color: "bg-teal-100 text-teal-700" },
    { id: "HSK 3", name: "HSK 3", color: "bg-orange-100 text-orange-700" },
    { id: "HSK 4", name: "HSK 4", color: "bg-red-100 text-red-700" },
    { id: "HSK 5", name: "HSK 5", color: "bg-purple-100 text-purple-700" },
    { id: "HSK 6", name: "HSK 6", color: "bg-gray-100 text-gray-700" },
];

const selectLevel = (level) => {
    router.get(
        "/chinese_words",
        { level },
        {
            preserveState: true,
            preserveScroll: true,
            only: ["words_list", "current_level"],
        }
    );
};

const getLevelColor = (level) => {
    const found = hskLevels.find((l) => l.id === level);
    return found ? found.color : "bg-gray-100 text-gray-700";
};

const getCharSize = (text) => {
    const len = text.length;
    if (len <= 1) return 70;
    if (len === 2) return 55;
    if (len === 3) return 40;
    return 30;
};

const hanziRefs = ref({});

const setHanziRef = (el, wordId, charIndex) => {
    if (el) {
        if (!hanziRefs.value[wordId]) {
            hanziRefs.value[wordId] = [];
        }
        hanziRefs.value[wordId][charIndex] = el;
    }
};

const playWord = (word) => {
    playSound(word.word);

    // Trigger animation for all characters in the word
    const chars = hanziRefs.value[word.id];
    if (chars) {
        chars.forEach((charComp) => {
            if (charComp && charComp.animate) {
                charComp.animate();
            }
        });
    }
};

// Animation delay for staggered entrance
const getDelay = (index) => {
    return { animationDelay: `${index * 50}ms` };
};

const highlightWord = (sentence, word) => {
    if (!sentence || !word) return sentence;
    const regex = new RegExp(word, "g");
    return sentence.replace(
        regex,
        `<span class="text-red-600 font-bold">${word}</span>`
    );
};

// AI Sentence Logic
const showAiModal = ref(false);
const aiSentences = ref([]);
const loadingAi = ref(false);
const currentWordForAi = ref(null);

const openAiModal = async (word, forceRefresh = false) => {
    if (!forceRefresh) {
        currentWordForAi.value = word;
        showAiModal.value = true;
        aiSentences.value = [];
    }
    
    loadingAi.value = true;

    try {
        const response = await axios.post('/api/ai-sentences/generate', {
            word: word.word,
            level: props.current_level,
            force_refresh: forceRefresh
        });
        aiSentences.value = response.data.sentences;
    } catch (error) {
        console.error("Failed to fetch AI sentences", error);
    } finally {
        loadingAi.value = false;
    }
};

const closeAiModal = () => {
    showAiModal.value = false;
    aiSentences.value = [];
    currentWordForAi.value = null;
};

const playAiAudio = async (text) => {
    try {
        const response = await axios.post('/api/azure-tts', {
            text: text,
            voice: 'zh-CN-XiaoxiaoNeural',
            rate: 0.7,
            volume: 80,
            pitch: 1.0
        }, {
            responseType: 'arraybuffer' // Important for audio
        });

        const blob = new Blob([response.data], { type: 'audio/mpeg' });
        const url = URL.createObjectURL(blob);
        const audio = new Audio(url);
        audio.play();
    } catch (error) {
        console.error("TTS Error", error);
    }
};
</script>

<template>
    <OrganicLayout>
        <Head title="คลังศัพท์ HSK" />

        <div class="container mx-auto p-4 min-h-screen">
            <div class="grid grid-cols-12 gap-6">
                <!-- Sidebar (HSK Levels) -->
                <div class="col-span-12 lg:col-span-3">
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 sticky top-24 overflow-hidden"
                    >
                        <div
                            class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-gray-700 flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-primary"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            เลือกระดับ (HSK Level)
                        </div>
                        <div class="p-2 space-y-1">
                            <button
                                v-for="level in hskLevels"
                                :key="level.id"
                                @click="selectLevel(level.id)"
                                class="w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-between group"
                                :class="
                                    current_level === level.id
                                        ? 'bg-blue-600 text-white shadow-md transform scale-105'
                                        : 'hover:bg-gray-50 text-gray-600'
                                "
                            >
                                <span class="font-medium">{{
                                    level.name
                                }}</span>
                                <span
                                    v-if="current_level === level.id"
                                    class="bg-white/20 px-2 py-0.5 rounded text-xs"
                                    >Selected</span
                                >
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-span-12 lg:col-span-9">
                    <!-- Header -->
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                                คลังคำศัพท์ {{ current_level }}
                            </h1>
                            <p class="text-gray-500">
                                รวมคำศัพท์ที่จำเป็นสำหรับการสอบวัดระดับภาษาจีน
                                {{ current_level }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <!-- Config Zone -->
                            <div
                                class="flex items-center gap-3 bg-white p-2 rounded-lg border border-gray-100 shadow-sm"
                            >
                                <label class="cursor-pointer label gap-2 p-0">
                                    <span
                                        class="label-text text-xs font-bold text-gray-600"
                                        >Pinyin</span
                                    >
                                    <input
                                        type="checkbox"
                                        class="toggle toggle-xs toggle-primary"
                                        v-model="showPinyin"
                                    />
                                </label>
                                <div class="w-px h-4 bg-gray-200"></div>
                                <label class="cursor-pointer label gap-2 p-0">
                                    <span
                                        class="label-text text-xs font-bold text-gray-600"
                                        >คำแปล</span
                                    >
                                    <input
                                        type="checkbox"
                                        class="toggle toggle-xs toggle-secondary"
                                        v-model="showMeaning"
                                    />
                                </label>
                            </div>

                            <div class="hidden md:block">
                                <div
                                    class="badge badge-lg p-3 font-bold"
                                    :class="getLevelColor(current_level)"
                                >
                                    {{ words_list.length }} คำ
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cards Grid -->
                    <div
                        v-if="words_list.length > 0"
                        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3"
                    >
                        <div
                            v-for="(word, index) in words_list"
                            :key="word.id"
                            class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 animate-fade-in-up"
                            :style="getDelay(index % 20)"
                        >
                            <div class="card-body p-0 overflow-hidden">
                                <!-- Word Header with Gradient -->
                                <div
                                    class="bg-gradient-to-r from-blue-50 to-indigo-50 p-3 flex flex-col gap-2"
                                >
                                    <!-- Characters Row -->
                                    <div
                                        class="flex justify-center items-center gap-1 cursor-pointer h-[75px] relative"
                                        @click="playWord(word)"
                                    >
                                        <HanziWriterCharacter
                                            v-for="(
                                                char, charIndex
                                            ) in word.word.split('')"
                                            :key="`${word.id}-${charIndex}`"
                                            :id="`${word.id}-${charIndex}`"
                                            :character="char"
                                            :size="getCharSize(word.word)"
                                            :ref="
                                                (el) =>
                                                    setHanziRef(
                                                        el,
                                                        word.id,
                                                        charIndex
                                                    )
                                            "
                                        />
                                        <button
                                            @click.stop="playWord(word)"
                                            class="btn btn-circle btn-ghost btn-xs text-indigo-400 hover:text-indigo-600 hover:bg-indigo-100 h-7 w-7 absolute bottom-0 right-0"
                                        >
                                            <i
                                                class="pi pi-volume-up text-sm"
                                                :class="{
                                                    'animate-pulse text-indigo-600':
                                                        playingWord ===
                                                        word.word,
                                                }"
                                            ></i>
                                        </button>
                                    </div>

                                    <!-- Pinyin Row -->
                                    <div
                                        v-if="showPinyin"
                                        class="flex justify-center items-center w-full border-t border-indigo-100 pt-2 mt-1"
                                    >
                                        <div
                                            class="text-sm font-bold text-indigo-500 font-serif italic"
                                        >
                                            {{ word.pinyin }}
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 pt-2">
                                    <!-- Meaning -->
                                    <div
                                        class="mb-2 leading-tight"
                                        v-if="showMeaning"
                                    >
                                        <div
                                            class="flex flex-wrap items-center gap-1"
                                        >
                                            <span
                                                class="badge badge-sm badge-primary badge-outline opacity-80"
                                                >{{ word.part_of_speech }}</span
                                            >
                                            <span
                                                class="font-bold text-gray-700 text-sm"
                                            >
                                                {{ word.meaning_thai }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Example Section (Ultra Compact) -->
                                    <div
                                        class="bg-amber-50 rounded-lg p-2 text-[11px] group relative mt-auto border border-amber-100"
                                    >
                                        <button
                                            @click="playSound(word.example)"
                                            class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity btn btn-xs btn-circle btn-ghost h-5 w-5 min-h-0 text-amber-600 hover:bg-amber-200"
                                        >
                                            <i
                                                class="pi pi-volume-up text-[10px]"
                                            ></i>
                                        </button>
                                        <p
                                            class="text-gray-800 font-medium mb-0.5 line-clamp-2"
                                            v-html="
                                                highlightWord(
                                                    word.example,
                                                    word.word
                                                )
                                            "
                                        ></p>
                                        <p
                                            class="text-gray-500 text-[10px] truncate"
                                            v-if="showPinyin"
                                        >
                                            {{ word.example_pinyin }}
                                        </p>
                                    </div>
                                    
                                    <!-- AI Button -->
                                    <button 
                                        @click="openAiModal(word)"
                                        class="btn btn-xs btn-outline btn-primary w-full mt-2 flex items-center gap-1"
                                    >
                                        <i class="pi pi-sparkles"></i>
                                        ตัวอย่างประโยค AI
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center py-20 text-center"
                    >
                        <div
                            class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
                        >
                            <span class="text-4xl">📭</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700">
                            ไม่พบคำศัพท์
                        </h3>
                        <p class="text-gray-500">ลองเลือกระดับอื่นดูนะครับ</p>
                    </div>
                </div>
            </div>
        </div>
    </OrganicLayout>

    <!-- AI Sentences Modal -->
    <dialog class="modal" :class="{ 'modal-open': showAiModal }">
        <div class="modal-box relative">
            <button @click="closeAiModal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
            
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i class="pi pi-sparkles text-yellow-500"></i>
                    ตัวอย่างประโยค AI: <span class="text-primary">{{ currentWordForAi?.word }}</span>
                </h3>
                <button 
                    @click="openAiModal(currentWordForAi, true)" 
                    class="btn btn-sm btn-ghost text-primary"
                    :disabled="loadingAi"
                >
                    <i class="pi pi-refresh" :class="{ 'animate-spin': loadingAi }"></i>
                    สร้างใหม่
                </button>
            </div>
            
            <div class="py-2">
                <div v-if="loadingAi" class="flex flex-col items-center justify-center py-8">
                    <span class="loading loading-dots loading-lg text-primary"></span>
                    <p class="text-gray-500 mt-2">AI กำลังแต่งประโยค...</p>
                </div>
                
                <div v-else-if="aiSentences.length > 0" class="space-y-4">
                    <div v-for="(item, idx) in aiSentences" :key="idx" class="bg-gray-50 p-3 rounded-lg border border-gray-100 relative group">
                        <button 
                            @click="playAiAudio(item.sentence)"
                            class="btn btn-circle btn-sm btn-ghost absolute top-2 right-2 text-primary"
                        >
                            <i class="pi pi-volume-up"></i>
                        </button>
                        
                        <p class="text-lg font-medium text-gray-800 pr-8">{{ item.sentence }}</p>
                        <p class="text-sm text-gray-500 italic mb-1">{{ item.pinyin }}</p>
                        <p class="text-sm text-gray-600">{{ item.translation }}</p>
                    </div>
                </div>
                
                <div v-else class="text-center py-8 text-gray-500">
                    ไม่พบข้อมูล
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button @click="closeAiModal">close</button>
        </form>
    </dialog>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
