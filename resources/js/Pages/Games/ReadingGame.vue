<script setup>
import { ref, onMounted } from "vue";
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head } from "@inertiajs/vue3";
import AzureTTS from "@/azure_tts";
import { dummyData } from "@/data/readingGameDummyData";

defineOptions({ layout: OrganicLayout });

const tts = new AzureTTS();

// --- State ---
const contentTypes = [
    { label: "ประโยค (Sentences)", value: "sentence" },
    { label: "คำศัพท์ (Words)", value: "word" },
];

const hskLevels = [
    { label: "ทั้งหมด (All)", value: 0 },
    { label: "HSK 1", value: 1 },
    { label: "HSK 2", value: 2 },
    { label: "HSK 3", value: 3 },
    { label: "HSK 4", value: 4 },
];

const categories = [
    { label: "ทั้งหมด (All)", value: "" },
    { label: "คำกริยา (Verbs)", value: "v." },
    { label: "คำนาม (Nouns)", value: "n." },
    { label: "ตัวเลข (Numbers)", value: "num." },
    { label: "คำสรรพนาม (Pronouns)", value: "pron." },
    { label: "คำคุณศัพท์ (Adjectives)", value: "adj." },
    { label: "คำวิเศษณ์ (Adverbs)", value: "adv." },
];

const selectedType = ref("sentence");
const selectedLevel = ref(1);
const selectedCategory = ref("");
const isLoadingContent = ref(false);

const examples = ref([]);

const selectedExample = ref(null);
const refText = ref("");
const isRecording = ref(false);
const isProcessing = ref(false);
const isPlayingReference = ref(false);
const statusMessage = ref("");
const result = ref(null);
const animatedResult = ref({
    accuracy: 0,
    fluency: 0,
    completeness: 0,
});
const selfAudioSrc = ref(null);

let mediaRecorder = null;
let chunks = [];

// --- Methods ---

const fetchContent = async () => {
    isLoadingContent.value = true;
    try {
        // --- Dummy Data Logic ---
        const type = selectedType.value === "sentence" ? "sentences" : "words";
        let data = dummyData[type];

        if (selectedLevel.value !== 0) {
            data = data.filter((item) => item.level === selectedLevel.value);
        }

        if (selectedType.value === "word" && selectedCategory.value) {
            data = data.filter(
                (item) => item.category === selectedCategory.value
            );
        }

        // Simulate network delay
        await new Promise((resolve) => setTimeout(resolve, 300));

        examples.value = data;

        /* 
        // --- Original API Logic (Disabled) ---
        const params = new URLSearchParams({
            type: selectedType.value,
            level: selectedLevel.value,
            category: selectedCategory.value,
        });

        const res = await fetch(`/api/reading-game/content?${params}`);
        if (!res.ok) throw new Error("Failed to fetch content");

        const data = await res.json();
        examples.value = data;
        */

        if (examples.value.length > 0) {
            selectExample(examples.value[0]);
        } else {
            selectedExample.value = null;
            refText.value = "";
        }
    } catch (e) {
        console.error(e);
        statusMessage.value = "Error loading content";
    } finally {
        isLoadingContent.value = false;
    }
};

const selectExample = (example) => {
    selectedExample.value = example;
    refText.value = example.text;
    result.value = null;
    // Reset animated values
    animatedResult.value = { accuracy: 0, fluency: 0, completeness: 0 };
    statusMessage.value = "";
    selfAudioSrc.value = null;
};

onMounted(() => {
    fetchContent();
});

const animateValue = (key, start, end, duration) => {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        // Ease out quart
        const easeProgress = 1 - Math.pow(1 - progress, 4);

        animatedResult.value[key] = Math.floor(
            progress * (end - start) + start
        );

        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            animatedResult.value[key] = end;
        }
    };
    window.requestAnimationFrame(step);
};

const playReference = async () => {
    if (isPlayingReference.value) return;

    isPlayingReference.value = true;
    try {
        await tts.speak2(refText.value, "zh-CN", { rate: 0.8 });
    } catch (e) {
        console.error(e);
    } finally {
        isPlayingReference.value = false;
    }
};

// ... (keep existing audio recording logic) ...

// --- Audio Recording (WAV) ---
// Helper to write string to DataView
const writeString = (view, offset, string) => {
    for (let i = 0; i < string.length; i++) {
        view.setUint8(offset + i, string.charCodeAt(i));
    }
};

// Convert Float32 audio buffer to WAV Blob
const bufferToWav = (buffer, sampleRate) => {
    const numOfChan = 1;
    const length = buffer.length * numOfChan * 2 + 44;
    const bufferArray = new ArrayBuffer(length);
    const view = new DataView(bufferArray);
    const channels = 1;
    let sample,
        offset = 0;
    let pos = 0;

    // write WAVE header
    writeString(view, 0, "RIFF");
    view.setUint32(4, 36 + buffer.length * 2, true);
    writeString(view, 8, "WAVE");
    writeString(view, 12, "fmt ");
    view.setUint32(16, 16, true);
    view.setUint16(20, 1, true);
    view.setUint16(22, channels, true);
    view.setUint32(24, sampleRate, true);
    view.setUint32(28, sampleRate * 2, true);
    view.setUint16(32, 2, true);
    view.setUint16(34, 16, true);
    writeString(view, 36, "data");
    view.setUint32(40, buffer.length * 2, true);

    // write interleaved data
    for (let i = 0; i < buffer.length; i++) {
        sample = Math.max(-1, Math.min(1, buffer[i]));
        sample = sample < 0 ? sample * 0x8000 : sample * 0x7fff;
        view.setInt16(44 + i * 2, sample, true);
    }

    return new Blob([view], { type: "audio/wav" });
};

const startRecording = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            audio: true,
        });
        const audioContext = new (window.AudioContext ||
            window.webkitAudioContext)({ sampleRate: 16000 });
        const source = audioContext.createMediaStreamSource(stream);
        const processor = audioContext.createScriptProcessor(4096, 1, 1);

        chunks = [];

        processor.onaudioprocess = (e) => {
            if (!isRecording.value) return;
            const inputData = e.inputBuffer.getChannelData(0);
            // Clone the data
            chunks.push(new Float32Array(inputData));
        };

        source.connect(processor);
        processor.connect(audioContext.destination);

        isRecording.value = true;
        statusMessage.value = "Recording...";
        result.value = null;
        animatedResult.value = { accuracy: 0, fluency: 0, completeness: 0 };

        // Store references to stop later
        mediaRecorder = { stream, audioContext, source, processor };
    } catch (e) {
        alert("Cannot access microphone: " + e.message);
    }
};

const stopRecording = async () => {
    if (!mediaRecorder) return;

    isRecording.value = false;
    statusMessage.value = "Processing...";
    isProcessing.value = true;

    // Stop tracks
    mediaRecorder.stream.getTracks().forEach((track) => track.stop());
    mediaRecorder.source.disconnect();
    mediaRecorder.processor.disconnect();
    mediaRecorder.audioContext.close();

    // Flatten chunks
    const length = chunks.reduce((acc, chunk) => acc + chunk.length, 0);
    const resultBuffer = new Float32Array(length);
    let offset = 0;
    for (const chunk of chunks) {
        resultBuffer.set(chunk, offset);
        offset += chunk.length;
    }

    // Convert to WAV
    const wavBlob = bufferToWav(resultBuffer, 16000);
    selfAudioSrc.value = URL.createObjectURL(wavBlob);

    // Send to API
    const formData = new FormData();
    formData.append("speech", wavBlob, "speech.wav");
    formData.append("text", refText.value);
    formData.append("language", "zh-CN");

    try {
        const res = await fetch("/api/speech/assess", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN":
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content") || "",
            },
            body: formData,
        });

        if (!res.ok) throw new Error("Assessment failed");

        const data = await res.json();
        result.value = data;

        // Trigger animations
        const best = data.NBest[0];
        animateValue("accuracy", 0, Math.round(best.AccuracyScore), 1500);
        animateValue("fluency", 0, Math.round(best.FluencyScore), 1500);
        animateValue(
            "completeness",
            0,
            Math.round(best.CompletenessScore),
            1500
        );

        statusMessage.value = "Done!";
    } catch (e) {
        statusMessage.value = "Error: " + e.message;
    } finally {
        isProcessing.value = false;
    }
};

// --- Helpers ---
const getColorClass = (score) => {
    if (score >= 80) return "text-success font-bold";
    if (score >= 60) return "text-warning font-bold";
    return "text-error font-bold";
};

const getScoreColor = (score) => {
    if (score >= 80) return "progress-success";
    if (score >= 60) return "progress-warning";
    return "progress-error";
};
</script>

<template>
    <Head title="Reading Game" />

    <div
        class="py-12 px-6 mx-auto max-w-screen-xl sm:px-8 md:px-12 lg:px-16 xl:px-24"
    >
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Left Column: Controls & Input -->
            <div class="lg:w-1/2 w-full space-y-4">
                <!-- Header -->
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        Reading Game
                    </h1>
                    <p class="text-xl text-gray-600">
                        ฝึกอ่านออกเสียงภาษาจีนให้ชัดเป๊ะ! 🇨🇳🎤
                    </p>
                </div>

                <!-- Filters -->
                <div
                    class="bg-white/60 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/50"
                >
                    <h3 class="text-lg font-bold mb-4 text-gray-800">
                        1. ตั้งค่าการฝึก
                    </h3>
                    <div class="flex flex-col gap-4">
                        <!-- Type Selection -->
                        <div class="flex gap-6">
                            <label
                                class="cursor-pointer flex items-center gap-2"
                                v-for="type in contentTypes"
                                :key="type.value"
                            >
                                <input
                                    type="radio"
                                    name="contentType"
                                    class="radio radio-primary"
                                    :value="type.value"
                                    v-model="selectedType"
                                    @change="fetchContent"
                                />
                                <span
                                    class="label-text font-bold text-gray-700"
                                    >{{ type.label }}</span
                                >
                            </label>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <!-- Level Selection -->
                            <select
                                class="select select-bordered w-full max-w-[150px]"
                                v-model="selectedLevel"
                                @change="fetchContent"
                            >
                                <option
                                    v-for="level in hskLevels"
                                    :key="level.value"
                                    :value="level.value"
                                >
                                    {{ level.label }}
                                </option>
                            </select>

                            <!-- Category Selection (Only for Words) -->
                            <select
                                v-if="selectedType === 'word'"
                                class="select select-bordered w-full max-w-[200px]"
                                v-model="selectedCategory"
                                @change="fetchContent"
                            >
                                <option
                                    v-for="cat in categories"
                                    :key="cat.value"
                                    :value="cat.value"
                                >
                                    {{ cat.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Example Selection -->
                <div
                    class="bg-white/60 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/50"
                >
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">
                            2. เลือก{{
                                selectedType === "word" ? "คำศัพท์" : "ประโยค"
                            }}
                        </h3>
                        <button
                            @click="fetchContent"
                            class="btn btn-ghost btn-sm btn-circle"
                            :class="{ 'animate-spin': isLoadingContent }"
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
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                />
                            </svg>
                        </button>
                    </div>

                    <div
                        v-if="isLoadingContent"
                        class="flex justify-center py-8"
                    >
                        <span
                            class="loading loading-spinner loading-lg text-primary"
                        ></span>
                    </div>

                    <div
                        v-else-if="examples.length === 0"
                        class="text-center py-8 text-gray-500"
                    >
                        ไม่พบข้อมูล
                    </div>

                    <div v-else class="flex flex-wrap gap-2">
                        <button
                            v-for="(ex, idx) in examples"
                            :key="idx"
                            @click="selectExample(ex)"
                            class="btn btn-sm h-auto py-2 px-4 rounded-xl border-0 shadow-sm transition-all duration-200"
                            :class="
                                selectedExample.text === ex.text
                                    ? 'bg-red text-white hover:bg-red-600 shadow-red/30 shadow-lg scale-105'
                                    : 'bg-white text-gray-700 hover:bg-gray-50 hover:scale-105'
                            "
                        >
                            {{ ex.text }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Results -->
            <div class="lg:w-1/2 w-full flex flex-col justify-center gap-4">
                <!-- Practice Area -->
                <div
                    class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl p-4 border border-white/50 relative group"
                >
                    <!-- Decorative background elements -->
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-red-100 rounded-full blur-3xl -z-10 opacity-50"
                    ></div>

                    <div class="text-center mb-6 relative z-10">
                        <h1
                            class="text-2xl lg:text-3xl font-bold mb-4 text-gray-900 tracking-tight"
                        >
                            {{ refText }}
                        </h1>
                        <p
                            v-if="selectedExample"
                            class="text-gray-500 text-xl font-light"
                        >
                            {{ selectedExample.pinyin }}
                        </p>
                    </div>

                    <div
                        class="flex justify-center gap-4 items-center relative z-10"
                    >
                        <button
                            @click="playReference"
                            :disabled="!selectedExample || isPlayingReference"
                            class="btn btn-circle btn-lg bg-blue-100 hover:bg-blue-200 border-0 text-blue-600 shadow-lg hover:scale-110 transition-all duration-300 w-10 h-10 disabled:opacity-50 disabled:scale-100"
                        >
                            <span
                                v-if="isPlayingReference"
                                class="loading loading-spinner loading-sm"
                            ></span>
                            <svg
                                v-else
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
                                    d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"
                                />
                            </svg>
                        </button>

                        <button
                            v-if="!isRecording"
                            @click="startRecording"
                            class="btn btn-circle btn-lg bg-red text-white border-0 shadow-xl shadow-red/30 hover:scale-110 hover:bg-red-600 transition-all duration-300 w-10 h-10 disabled:opacity-50 disabled:scale-100"
                            :disabled="isProcessing || !selectedExample"
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
                                    d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
                                />
                            </svg>
                        </button>

                        <button
                            v-else
                            @click="stopRecording"
                            class="btn btn-circle btn-lg bg-white border-4 border-red text-red shadow-xl hover:scale-110 transition-all duration-300 w-10 h-10 animate-pulse"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path d="M6 6h12v12H6z" />
                            </svg>
                        </button>
                    </div>

                    <div
                        v-if="statusMessage"
                        class="text-center mt-3 font-medium"
                        :class="
                            isRecording
                                ? 'text-red animate-pulse'
                                : 'text-gray-500'
                        "
                    >
                        {{ statusMessage }}
                    </div>
                </div>

                <!-- Audio Player -->
                <div
                    v-if="selfAudioSrc"
                    class="bg-white/60 backdrop-blur-md rounded-2xl p-4 shadow-sm border border-white/50 flex items-center gap-4"
                >
                    <span
                        class="text-sm w-2/5 font-bold text-gray-500 uppercase tracking-wider"
                        >เสียงของคุณ:</span
                    >
                    <audio
                        controls
                        :src="selfAudioSrc"
                        class="w-full h-10"
                    ></audio>
                </div>

                <div
                    v-if="result"
                    class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl p-4 border border-white/50 h-full animate-fade-in-up"
                >
                    <h3
                        class="text-xl md:text-2xl font-bold mb-4 md:mb-6 text-gray-800 flex flex-wrap justify-between items-center gap-2"
                    >
                        <span class="ml-2">📊 ผลการประเมิน</span>
                        <span
                            class="badge badge-md md:badge-lg badge-primary text-white font-bold mr-3 scale-120"
                            >{{ animatedResult.accuracy }} คะแนน</span
                        >
                    </h3>

                    <!-- Scores Grid -->
                    <div class="grid grid-cols-1 gap-4 md:gap-6 mb-3 md:mb-6">
                        <!-- Accuracy -->
                        <div
                            class="bg-white rounded-2xl p-4 md:p-5 shadow-sm border border-gray-100"
                        >
                            <div class="flex justify-between items-end mb-2">
                                <span
                                    class="text-gray-500 font-medium text-sm md:text-base"
                                    >ความถูกต้อง (Accuracy)</span
                                >
                                <span
                                    class="text-xl md:text-2xl font-bold text-gray-900"
                                    >{{ animatedResult.accuracy }}%</span
                                >
                            </div>
                            <progress
                                class="progress w-full h-3"
                                :class="getScoreColor(animatedResult.accuracy)"
                                :value="animatedResult.accuracy"
                                max="100"
                            ></progress>
                        </div>

                        <div class="grid grid-cols-2 gap-3 md:gap-6">
                            <!-- Fluency -->
                            <div
                                class="bg-white rounded-2xl p-3 md:p-5 shadow-sm border border-gray-100"
                            >
                                <div
                                    class="flex justify-between items-end mb-2"
                                >
                                    <span
                                        class="text-gray-500 font-medium text-xs md:text-sm"
                                        >ความลื่นไหล</span
                                    >
                                    <span
                                        class="text-lg md:text-xl font-bold text-gray-900"
                                        >{{ animatedResult.fluency }}%</span
                                    >
                                </div>
                                <progress
                                    class="progress w-full h-2"
                                    :class="
                                        getScoreColor(animatedResult.fluency)
                                    "
                                    :value="animatedResult.fluency"
                                    max="100"
                                ></progress>
                            </div>
                            <!-- Completeness -->
                            <div
                                class="bg-white rounded-2xl p-3 md:p-5 shadow-sm border border-gray-100"
                            >
                                <div
                                    class="flex justify-between items-end mb-2"
                                >
                                    <span
                                        class="text-gray-500 font-medium text-xs md:text-sm"
                                        >ความครบถ้วน</span
                                    >
                                    <span
                                        class="text-lg md:text-xl font-bold text-gray-900"
                                        >{{
                                            animatedResult.completeness
                                        }}%</span
                                    >
                                </div>
                                <progress
                                    class="progress w-full h-2"
                                    :class="
                                        getScoreColor(
                                            animatedResult.completeness
                                        )
                                    "
                                    :value="animatedResult.completeness"
                                    max="100"
                                ></progress>
                            </div>
                        </div>
                    </div>

                    <!-- Word Analysis -->
                    <div
                        class="bg-gray-50 rounded-2xl p-4 md:p-6 border border-gray-100"
                    >
                        <h4
                            class="font-bold text-gray-700 mb-3 md:mb-4 uppercase text-xs md:text-sm tracking-wider"
                        >
                            วิเคราะห์รายคำ
                        </h4>
                        <div
                            class="flex flex-wrap gap-2 md:gap-3 text-xl md:text-3xl leading-loose"
                        >
                            <span
                                v-for="(word, idx) in result.NBest[0].Words"
                                :key="idx"
                                class="px-2 py-1 md:px-3 md:py-1 rounded-lg bg-white shadow-sm border border-gray-100 cursor-help transition-transform hover:scale-110 tooltip tooltip-top"
                                :class="getColorClass(word.AccuracyScore)"
                                :data-tip="
                                    'คะแนน: ' + Math.round(word.AccuracyScore)
                                "
                            >
                                {{ word.Word }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="h-full flex flex-col items-center justify-center text-gray-400 p-12 border-2 border-dashed border-gray-300 rounded-3xl bg-white/30"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-24 w-24 mb-4 opacity-50"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1"
                            d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"
                        />
                    </svg>
                    <p class="text-lg font-medium">
                        กดปุ่มไมโครโฟนเพื่อเริ่มทดสอบ
                    </p>
                    <p class="text-sm">ผลคะแนนจะปรากฏที่นี่</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
