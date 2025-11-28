<script setup lang="ts">
import { computed, reactive, watch } from "vue";
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head } from "@inertiajs/vue3";

defineOptions({ layout: OrganicLayout });

interface Question {
    id: number;
    key: string;
    text: string;
}

interface Option {
    value: number;
    label: string;
    color: string;
}

interface ScoreRange {
    label: string;
    min: number;
    max: number;
    suggestion: string;
    level: "low" | "medium" | "high" | "severe";
    color: string;
    icon: string;
}

const questions: Question[] = [
    {
        id: 1,
        key: "st5_q1",
        text: "นอนไม่หลับ หรือหลับๆ ตื่นๆ",
    },
    {
        id: 2,
        key: "st5_q2",
        text: "หงุดหงิดง่าย",
    },
    {
        id: 3,
        key: "st5_q3",
        text: "กระวนกระวาย อยู่ไม่สุข",
    },
    {
        id: 4,
        key: "st5_q4",
        text: "ไม่มีสมาธิ",
    },
    {
        id: 5,
        key: "st5_q5",
        text: "รู้สึกเบื่อ เซ็ง ไม่อยากทำอะไร",
    },
];

const options: Option[] = [
    { value: 0, label: "ไม่เคย", color: "btn-ghost" },
    { value: 1, label: "เป็นบางครั้ง", color: "btn-info" },
    { value: 2, label: "บ่อยครั้ง", color: "btn-warning" },
    { value: 3, label: "เป็นประจำ", color: "btn-error" },
];

const ranges: ScoreRange[] = [
    {
        label: "ความเครียดน้อย",
        min: 0,
        max: 4,
        suggestion:
            "ดูแลตนเองต่อเนื่อง นอนหลับพักผ่อนให้เพียงพอ ออกกำลังกาย ทำกิจกรรมผ่อนคลายที่ชอบ",
        level: "low",
        color: "text-success",
        icon: "pi-smile",
    },
    {
        label: "ความเครียดปานกลาง",
        min: 5,
        max: 7,
        suggestion:
            "เริ่มใช้เทคนิคผ่อนคลายความเครียด เช่น หายใจลึกๆ ฝึกสติ หากอาการไม่ดีขึ้นควรปรึกษาบุคลากรสาธารณสุข",
        level: "medium",
        color: "text-info",
        icon: "pi-meh",
    },
    {
        label: "ความเครียดสูง",
        min: 8,
        max: 9,
        suggestion:
            "ควรเข้ารับการประเมินเพิ่มเติมโดยบุคลากรด้านสุขภาพจิต เพื่อตรวจดูภาวะซึมเศร้าหรือปัญหาทางใจอื่นๆ",
        level: "high",
        color: "text-warning",
        icon: "pi-frown",
    },
    {
        label: "ความเครียดรุนแรง",
        min: 10,
        max: 15,
        suggestion:
            "ควรพบแพทย์หรือนักจิตวิทยาโดยเร็ว อาจพิจารณาประเมินด้วยแบบประเมินอื่นเพิ่มเติม เช่น 2Q / 9Q",
        level: "severe",
        color: "text-error",
        icon: "pi-exclamation-circle",
    },
];

// state responses
const responses = reactive<Record<string, number | null>>({});
questions.forEach((q) => {
    responses[q.key] = null;
});

const isComplete = computed(() =>
    questions.every((q) => responses[q.key] !== null)
);

const totalScore = computed(() => {
    return questions.reduce((sum, q) => {
        const val = responses[q.key];
        return sum + (typeof val === "number" ? val : 0);
    }, 0);
});

const currentRange = computed<ScoreRange | null>(() => {
    if (!isComplete.value) return null;
    return (
        ranges.find(
            (r) => totalScore.value >= r.min && totalScore.value <= r.max
        ) || null
    );
});

const alertClass = computed(() => {
    if (!currentRange.value) return "alert-info";

    switch (currentRange.value.level) {
        case "low":
            return "alert-success";
        case "medium":
            return "alert-info";
        case "high":
            return "alert-warning";
        case "severe":
            return "alert-error";
        default:
            return "alert-info";
    }
});

// ส่งค่ากลับให้ parent ถ้าต้องการใช้
const emit = defineEmits<{
    (e: "update:score", value: number): void;
    (
        e: "completed",
        payload: {
            score: number;
            range: ScoreRange | null;
            responses: Record<string, number | null>;
        }
    ): void;
}>();

watch(
    () => totalScore.value,
    (val) => {
        emit("update:score", val);
    }
);

watch(
    () => isComplete.value,
    (done) => {
        if (done) {
            emit("completed", {
                score: totalScore.value,
                range: currentRange.value,
                responses: { ...responses },
            });
        }
    }
);

function resetForm() {
    questions.forEach((q) => {
        responses[q.key] = null;
    });
}
</script>

<template>
    <Head title="แบบประเมินความเครียด ST-5" />

    <div class="container mx-auto px-4 py-12 min-h-screen max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">
                แบบประเมินความเครียด <span class="text-primary">ST-5</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                โปรดเลือกคำตอบที่ตรงกับอาการของท่านมากที่สุดในช่วง
                <span class="font-bold text-primary">2 สัปดาห์ที่ผ่านมา</span>
                เพื่อประเมินระดับความเครียดเบื้องต้น
            </p>
            <div class="mt-4 badge badge-neutral">กรมสุขภาพจิต</div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <!-- Questions Column -->
            <div class="lg:col-span-2 space-y-6">
                <div
                    v-for="(q, index) in questions"
                    :key="q.key"
                    class="card bg-white border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300"
                    :class="{ 'border-primary ring-1 ring-primary/20': responses[q.key] !== null }"
                >
                    <div class="card-body p-6">
                        <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm">
                                {{ index + 1 }}
                            </span>
                            {{ q.text }}
                        </h3>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <label
                                v-for="opt in options"
                                :key="opt.value"
                                class="cursor-pointer relative group"
                            >
                                <input
                                    type="radio"
                                    class="peer sr-only"
                                    :name="q.key"
                                    :value="opt.value"
                                    v-model.number="responses[q.key]"
                                />
                                <div class="p-3 rounded-lg border border-gray-200 text-center text-sm transition-all duration-200 peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary hover:bg-gray-50 peer-checked:hover:bg-primary peer-checked:shadow-md">
                                    {{ opt.label }}
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Column (Sticky) -->
            <div class="lg:col-span-1 lg:sticky lg:top-8 space-y-6">
                <!-- Score Card -->
                <div class="card bg-white shadow-lg border border-gray-100 overflow-hidden">
                    <div class="card-body p-6 text-center">
                        <h3 class="text-gray-500 font-medium mb-2">คะแนนรวม</h3>
                        <div class="text-6xl font-black text-gray-800 mb-2">
                            {{ totalScore }}
                            <span class="text-xl text-gray-400 font-normal">/ 15</span>
                        </div>
                        
                        <div class="divider my-2"></div>

                        <div v-if="currentRange" class="animate-fade-in">
                            <div class="text-sm text-gray-500 mb-2">ระดับความเครียด</div>
                            <div class="text-2xl font-bold mb-4 flex items-center justify-center gap-2" :class="currentRange.color">
                                <i class="pi text-2xl" :class="currentRange.icon"></i>
                                {{ currentRange.label }}
                            </div>
                            
                            <div class="alert text-left text-sm" :class="alertClass">
                                <i class="pi pi-info-circle"></i>
                                <span>{{ currentRange.suggestion }}</span>
                            </div>
                        </div>

                        <div v-else class="text-gray-400 italic py-4">
                            <i class="pi pi-check-circle text-4xl mb-2 block opacity-20"></i>
                            ตอบคำถามให้ครบทุกข้อ<br>เพื่อดูผลการประเมิน
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="bg-gray-100 h-2 w-full mt-auto">
                        <div 
                            class="h-full bg-primary transition-all duration-500"
                            :style="{ width: `${(Object.values(responses).filter(v => v !== null).length / 5) * 100}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Reset Button -->
                <button
                    v-if="Object.values(responses).some(v => v !== null)"
                    class="btn btn-ghost btn-block text-gray-500 hover:text-error"
                    @click="resetForm"
                >
                    <i class="pi pi-refresh"></i>
                    ล้างคำตอบทั้งหมด
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
