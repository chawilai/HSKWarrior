<script setup>
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import HanziWriterCharacter from "@/Components/HanziWriterCharacter.vue";
import { usePlaySound } from "@/composables/usePlaySound.js";

defineOptions({ layout: OrganicLayout });

const props = defineProps({
    hanzi_list_data: Object,
});

const { playSound, stopPlayback, playingWord } = usePlaySound();

const hanzi_list = ref(props.hanzi_list_data);

const hanziRefs = ref({});

const setHanziRef = (el, index) => {
    if (el) {
        hanziRefs.value[index] = el;
    }
};

const playWordWithAnimation = (word, index) => {
    playSound(word.character);
    
    const charComp = hanziRefs.value[index];
    if (charComp && charComp.animate) {
        charComp.animate();
    }
};
</script>

<template>
    <Head title="รายการอักษร" />

    <div class="container mx-auto px-4 py-8 min-h-screen">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    {{ hanzi_list.list_name || 'รายการคำศัพท์' }}
                </h1>
                <p class="text-gray-500 mt-1">
                    Reference: {{ hanzi_list.reference }} | จำนวน: {{ hanzi_list.words.length }} คำ
                </p>
            </div>

            <div class="flex gap-3">
                <Link 
                    :href="`/hanzi_list_writing?reference=${hanzi_list.reference}`" 
                    class="btn btn-primary gap-2 text-white shadow-md hover:scale-105 transition-transform"
                >
                    <i class="pi pi-pen-to-square"></i>
                    คัดจีน
                </Link>
                <Link 
                    :href="`/warrior_flip_card?reference=${hanzi_list.reference}`" 
                    class="btn btn-secondary gap-2 text-white shadow-md hover:scale-105 transition-transform"
                >
                    <i class="pi pi-clone"></i>
                    การ์ดคำ
                </Link>
            </div>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 mb-8">
            <div 
                v-for="(word, index) in hanzi_list.words" 
                :key="index"
                class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group"
            >
                <div class="card-body p-4 items-center text-center relative">
                    <!-- Sequence Number (Top Left) -->
                    <div class="absolute top-2 left-2 badge badge-ghost badge-sm font-mono opacity-50">
                        {{ index + 1 }}
                    </div>

                    <!-- Radical (Top Right) -->
                     <div class="absolute top-2 right-2 text-xs text-red-500 font-bold" title="Radical">
                        {{ word.radical }}
                    </div>

                    <!-- Character -->
                    <div 
                        class="mt-2 mb-1 cursor-pointer hover:scale-110 transition-transform"
                        @click="playWordWithAnimation(word, index)"
                    >
                        <HanziWriterCharacter 
                            :ref="(el) => setHanziRef(el, index)"
                            :id="`hanzi-${index}`"
                            :character="word.character"
                            :size="60"
                            :strokeColor="'#1f2937'"
                            :radicalColor="'#B71F1F'"
                        />
                    </div>

                    <!-- Pinyin -->
                    <div class="text-lg font-bold text-primary font-serif italic mb-1">
                        {{ word.pinyin }}
                    </div>

                    <!-- Meaning -->
                    <div class="text-sm text-gray-600 line-clamp-2 h-10 flex items-center justify-center leading-tight">
                        {{ word.definition }}
                    </div>

                    <!-- Audio Button (Bottom) -->
                    <button 
                        class="btn btn-circle btn-ghost btn-sm text-gray-400 hover:text-primary mt-2"
                        @click="playWordWithAnimation(word, index)"
                    >
                        <i class="pi pi-volume-up"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Empty State -->
        <div v-if="hanzi_list.words.length === 0" class="text-center py-12 text-gray-400">
            <i class="pi pi-inbox text-4xl mb-4"></i>
            <p>ไม่มีรายการคำศัพท์</p>
        </div>
    </div>
</template>
