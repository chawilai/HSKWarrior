<script setup>
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePlaySound } from "@/composables/usePlaySound.js";

const props = defineProps({
    words_list: Array,
    current_level: String,
});

const { playSound, playingWord } = usePlaySound();

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

// Animation delay for staggered entrance
const getDelay = (index) => {
    return { animationDelay: `${index * 50}ms` };
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
                        <div class="hidden md:block">
                            <div
                                class="badge badge-lg p-4 font-bold"
                                :class="getLevelColor(current_level)"
                            >
                                {{ words_list.length }} คำ
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
                            <div class="card-body p-2">
                                <!-- Word Header -->
                                <div
                                    class="flex justify-between items-start mb-1"
                                >
                                    <div>
                                        <h2
                                            class="text-xl font-bold text-gray-800 mb-0 cursor-pointer hover:text-primary transition-colors"
                                            @click="playSound(word.word)"
                                        >
                                            {{ word.word }}
                                        </h2>
                                        <div
                                            class="text-xs text-gray-500 font-serif italic"
                                        >
                                            {{ word.pinyin }}
                                        </div>
                                    </div>
                                    <button
                                        @click="playSound(word.word)"
                                        class="btn btn-circle btn-ghost btn-xs text-gray-400 hover:text-primary hover:bg-primary/10 h-6 w-6 min-h-0"
                                    >
                                        <i
                                            class="pi pi-volume-up text-xs"
                                            :class="{
                                                'animate-pulse text-primary':
                                                    playingWord === word.word,
                                            }"
                                        ></i>
                                    </button>
                                </div>

                                <!-- Meaning -->
                                <div class="mb-1 leading-tight">
                                    <div
                                        class="flex flex-wrap items-center gap-1"
                                    >
                                        <span
                                            class="badge badge-xs badge-outline opacity-70 scale-90 origin-left"
                                            >{{ word.part_of_speech }}</span
                                        >
                                        <span
                                            class="font-medium text-gray-700 text-xs truncate"
                                        >
                                            {{ word.meaning_thai }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Example Section (Ultra Compact) -->
                                <div
                                    class="bg-gray-50 rounded p-1.5 text-[10px] group relative mt-auto"
                                >
                                    <button
                                        @click="playSound(word.example)"
                                        class="absolute top-0.5 right-0.5 opacity-0 group-hover:opacity-100 transition-opacity btn btn-xs btn-circle btn-ghost h-4 w-4 min-h-0"
                                    >
                                        <i
                                            class="pi pi-volume-up text-[8px]"
                                        ></i>
                                    </button>
                                    <p
                                        class="text-gray-800 font-medium mb-0.5 line-clamp-1"
                                    >
                                        {{ word.example }}
                                    </p>
                                    <p
                                        class="text-gray-500 text-[9px] truncate"
                                    >
                                        {{ word.example_pinyin }}
                                    </p>
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
