<script setup>
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";

defineOptions({ layout: OrganicLayout });

// --- Data ---
const hskLevels = [
    { id: 1, name: "HSK 1", locked: false },
    { id: 2, name: "HSK 2", locked: true },
    { id: 3, name: "HSK 3", locked: true },
    { id: 4, name: "HSK 4", locked: true },
    { id: 5, name: "HSK 5", locked: true },
    { id: 6, name: "HSK 6", locked: true },
];

const lessons = Array.from({ length: 15 }, (_, i) => ({
    id: i + 1,
    title: `บทที่ ${i + 1}`,
    subtitle: `Lesson ${i + 1}`,
}));

const topics = [
    { id: "summary", name: "Summary", icon: "📝", label: "บทสรุป" },
    { id: "vocab", name: "Vocabulary", icon: "🔤", label: "คำศัพท์" },
    {
        id: "conversation",
        name: "Conversations",
        icon: "💬",
        label: "บทสนทนา",
    },
    { id: "grammar", name: "Grammar", icon: "📚", label: "ไวยากรณ์" },
    { id: "extra", name: "Extra", icon: "✨", label: "เนื้อหาเสริม" },
];

// --- State ---
const selectedLevel = ref(1);
const selectedLesson = ref(1);
const selectedTopic = ref("summary");

// --- Actions ---
const selectLevel = (levelId) => {
    if (levelId !== 1) return; // Prevent selection if locked (only HSK 1 active for now)
    selectedLevel.value = levelId;
    // Reset lesson/topic if needed, or keep state
};

const selectLesson = (lessonId) => {
    selectedLesson.value = lessonId;
    // Default to summary when switching lessons?
    // selectedTopic.value = 'summary';
};

const selectTopic = (topicId) => {
    selectedTopic.value = topicId;
};
</script>

<template>
    <Head title="HSK Textbook" />

    <div class="container mx-auto p-4 min-h-screen">
        <div class="grid grid-cols-12 gap-6">
            <!-- Left Sidebar (Menu) -->
            <div class="col-span-12 lg:col-span-3 space-y-4">
                <!-- HSK Levels -->
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                >
                    <div
                        class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-gray-700"
                    >
                        เลือกระดับ (HSK Level)
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="level in hskLevels"
                            :key="level.id"
                            @click="selectLevel(level.id)"
                            class="p-3 flex items-center justify-between cursor-pointer transition-colors hover:bg-gray-50"
                            :class="{
                                'bg-blue-50 text-blue-600':
                                    selectedLevel === level.id,
                                'opacity-60 cursor-not-allowed': level.locked,
                            }"
                        >
                            <span class="font-medium">{{ level.name }}</span>
                            <span v-if="level.locked" class="text-gray-400">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                            <span
                                v-else-if="selectedLevel === level.id"
                                class="text-blue-500"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L7 13.586l9.293-9.293a1 1 0 111.414 1.414l-10 10z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Lessons List (Only show if HSK 1 is selected) -->
                <div
                    v-if="selectedLevel === 1"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                >
                    <div
                        class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-gray-700"
                    >
                        บทเรียน (Lessons)
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto scrollbar-thin">
                        <div
                            v-for="lesson in lessons"
                            :key="lesson.id"
                            @click="selectLesson(lesson.id)"
                            class="p-3 border-l-4 cursor-pointer transition-all hover:bg-gray-50"
                            :class="
                                selectedLesson === lesson.id
                                    ? 'border-blue-500 bg-blue-50'
                                    : 'border-transparent'
                            "
                        >
                            <div class="font-bold text-gray-800">
                                {{ lesson.title }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ lesson.subtitle }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content Area -->
            <div class="col-span-12 lg:col-span-9">
                <div
                    class="bg-white rounded-3xl shadow-lg border border-gray-100 min-h-[80vh] flex flex-col"
                >
                    <!-- Header -->
                    <div
                        class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-blue-50 to-white rounded-t-3xl"
                    >
                        <div>
                            <div class="text-sm font-bold text-blue-500 mb-1">
                                HSK {{ selectedLevel }}
                            </div>
                            <h1 class="text-3xl font-bold text-gray-800">
                                บทที่ {{ selectedLesson }}
                            </h1>
                            <p class="text-gray-500">
                                การทักทาย (Greetings) - สวัสดี
                            </p>
                        </div>

                        <!-- Topic Tabs -->
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="topic in topics"
                                :key="topic.id"
                                @click="selectTopic(topic.id)"
                                class="btn btn-sm rounded-full transition-all"
                                :class="
                                    selectedTopic === topic.id
                                        ? 'btn-primary text-white shadow-md'
                                        : 'btn-ghost bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'
                                "
                            >
                                <span class="mr-1">{{ topic.icon }}</span>
                                {{ topic.name }}
                            </button>
                        </div>
                    </div>

                    <!-- Content Body -->
                    <div class="p-8 flex-grow">
                        <!-- Summary Content -->
                        <div
                            v-if="selectedTopic === 'summary'"
                            class="animate-fade-in"
                        >
                            <h2
                                class="text-2xl font-bold mb-4 flex items-center gap-2"
                            >
                                <span class="text-3xl">📝</span> บทสรุป
                                (Summary)
                            </h2>
                            <div class="prose max-w-none text-gray-600">
                                <p class="text-lg leading-relaxed">
                                    ในบทเรียนนี้
                                    คุณจะได้เรียนรู้วิธีการทักทายพื้นฐานในภาษาจีน
                                    คำว่า "หนีห่าว" (Nǐ hǎo) ซึ่งแปลว่า "สวัสดี"
                                    เป็นคำที่ใช้บ่อยที่สุด
                                    นอกจากนี้ยังจะได้เรียนรู้การถามสารทุกข์สุกดิบ
                                    และการแนะนำตัวเบื้องต้น
                                </p>
                                <div class="alert alert-info mt-6 shadow-sm">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        class="stroke-current shrink-0 w-6 h-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                    <span
                                        >Key Point:
                                        การออกเสียงวรรณยุกต์เป็นสิ่งสำคัญมากในภาษาจีน</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Vocabulary Content -->
                        <div
                            v-else-if="selectedTopic === 'vocab'"
                            class="animate-fade-in"
                        >
                            <h2
                                class="text-2xl font-bold mb-6 flex items-center gap-2"
                            >
                                <span class="text-3xl">🔤</span> คำศัพท์
                                (Vocabulary)
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    class="card bg-base-100 border border-gray-100 shadow-sm hover:shadow-md transition-shadow"
                                >
                                    <div
                                        class="card-body p-4 flex flex-row items-center justify-between"
                                    >
                                        <div>
                                            <h3
                                                class="text-2xl font-bold text-primary"
                                            >
                                                你好
                                            </h3>
                                            <p class="text-gray-500">nǐ hǎo</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-lg">
                                                สวัสดี
                                            </div>
                                            <div class="badge badge-ghost">
                                                pron.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="card bg-base-100 border border-gray-100 shadow-sm hover:shadow-md transition-shadow"
                                >
                                    <div
                                        class="card-body p-4 flex flex-row items-center justify-between"
                                    >
                                        <div>
                                            <h3
                                                class="text-2xl font-bold text-primary"
                                            >
                                                我
                                            </h3>
                                            <p class="text-gray-500">wǒ</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-lg">
                                                ฉัน, ผม
                                            </div>
                                            <div class="badge badge-ghost">
                                                pron.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="card bg-base-100 border border-gray-100 shadow-sm hover:shadow-md transition-shadow"
                                >
                                    <div
                                        class="card-body p-4 flex flex-row items-center justify-between"
                                    >
                                        <div>
                                            <h3
                                                class="text-2xl font-bold text-primary"
                                            >
                                                叫
                                            </h3>
                                            <p class="text-gray-500">jiào</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-lg">
                                                ชื่อว่า, เรียก
                                            </div>
                                            <div class="badge badge-ghost">
                                                v.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conversations Content -->
                        <div
                            v-else-if="selectedTopic === 'conversation'"
                            class="animate-fade-in"
                        >
                            <h2
                                class="text-2xl font-bold mb-6 flex items-center gap-2"
                            >
                                <span class="text-3xl">💬</span> บทสนทนา
                                (Conversations)
                            </h2>
                            <div class="space-y-6">
                                <div class="chat chat-start">
                                    <div class="chat-image avatar">
                                        <div
                                            class="w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold"
                                        >
                                            A
                                        </div>
                                    </div>
                                    <div
                                        class="chat-bubble chat-bubble-primary text-white"
                                    >
                                        <div class="text-lg">
                                            你好！(Nǐ hǎo!)
                                        </div>
                                        <div class="text-xs opacity-80 mt-1">
                                            สวัสดี!
                                        </div>
                                    </div>
                                </div>
                                <div class="chat chat-end">
                                    <div class="chat-image avatar">
                                        <div
                                            class="w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold"
                                        >
                                            B
                                        </div>
                                    </div>
                                    <div
                                        class="chat-bubble bg-gray-100 text-gray-800"
                                    >
                                        <div class="text-lg">
                                            你好！你是老师吗？(Nǐ hǎo! Nǐ shì
                                            lǎoshī ma?)
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            สวัสดี! คุณเป็นครูใช่ไหม?
                                        </div>
                                    </div>
                                </div>
                                <div class="chat chat-start">
                                    <div class="chat-image avatar">
                                        <div
                                            class="w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold"
                                        >
                                            A
                                        </div>
                                    </div>
                                    <div
                                        class="chat-bubble chat-bubble-primary text-white"
                                    >
                                        <div class="text-lg">
                                            不是，我是学生。(Bú shì, wǒ shì
                                            xuésheng.)
                                        </div>
                                        <div class="text-xs opacity-80 mt-1">
                                            ไม่ใช่, ฉันเป็นนักเรียน
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grammar Content -->
                        <div
                            v-else-if="selectedTopic === 'grammar'"
                            class="animate-fade-in"
                        >
                            <h2
                                class="text-2xl font-bold mb-6 flex items-center gap-2"
                            >
                                <span class="text-3xl">📚</span> ไวยากรณ์
                                (Grammar)
                            </h2>
                            <div class="space-y-6">
                                <div
                                    class="bg-yellow-50 p-6 rounded-2xl border border-yellow-100"
                                >
                                    <h3
                                        class="font-bold text-lg text-yellow-800 mb-2"
                                    >
                                        1. การใช้ 吗 (ma)
                                    </h3>
                                    <p class="text-gray-700 mb-4">
                                        วางไว้ท้ายประโยคเพื่อเปลี่ยนประโยคบอกเล่าให้เป็นประโยคคำถาม
                                        (Yes/No Question)
                                    </p>
                                    <div
                                        class="bg-white p-4 rounded-xl border border-yellow-100"
                                    >
                                        <p class="mb-2">
                                            <span class="font-bold text-primary"
                                                >你是老师。</span
                                            >
                                            (Nǐ shì lǎoshī) = คุณเป็นครู
                                        </p>
                                        <p>
                                            <span class="font-bold text-primary"
                                                >你是老师<span
                                                    class="text-red-500"
                                                    >吗</span
                                                >？</span
                                            >
                                            (Nǐ shì lǎoshī ma?) =
                                            คุณเป็นครู<span class="text-red-500"
                                                >ไหม</span
                                            >?
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="bg-blue-50 p-6 rounded-2xl border border-blue-100"
                                >
                                    <h3
                                        class="font-bold text-lg text-blue-800 mb-2"
                                    >
                                        2. การใช้ 不 (bù)
                                    </h3>
                                    <p class="text-gray-700 mb-4">
                                        วางไว้หน้าคำกริยาหรือคำคุณศัพท์เพื่อแสดงการปฏิเสธ
                                    </p>
                                    <div
                                        class="bg-white p-4 rounded-xl border border-blue-100"
                                    >
                                        <p class="mb-2">
                                            <span class="font-bold text-primary"
                                                >我是学生。</span
                                            >
                                            (Wǒ shì xuésheng) = ฉันเป็นนักเรียน
                                        </p>
                                        <p>
                                            <span class="font-bold text-primary"
                                                >我<span class="text-red-500"
                                                    >不</span
                                                >是学生。</span
                                            >
                                            (Wǒ bú shì xuésheng) = ฉัน<span
                                                class="text-red-500"
                                                >ไม่</span
                                            >ใช่นักเรียน
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Extra Content -->
                        <div
                            v-else-if="selectedTopic === 'extra'"
                            class="animate-fade-in"
                        >
                            <h2
                                class="text-2xl font-bold mb-6 flex items-center gap-2"
                            >
                                <span class="text-3xl">✨</span> เนื้อหาเสริม
                                (Extra)
                            </h2>
                            <div
                                class="card bg-gradient-to-br from-purple-500 to-indigo-600 text-white shadow-xl"
                            >
                                <div class="card-body">
                                    <h3 class="card-title text-2xl">
                                        รู้หรือไม่?
                                    </h3>
                                    <p class="text-lg opacity-90">
                                        คนจีนมักจะไม่ทักทายว่า "หนีห่าว"
                                        กับคนสนิท แต่จะถามว่า "กินข้าวหรือยัง?"
                                        (Chī fàn le ma?) แทน
                                        เป็นการแสดงความห่วงใย
                                    </p>
                                    <div class="card-actions justify-end mt-4">
                                        <button
                                            class="btn btn-white text-purple-600"
                                        >
                                            อ่านเพิ่มเติม
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
