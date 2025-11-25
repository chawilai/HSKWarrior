<script setup>
import FlipCard from "@/Components/FlipCard.vue";
import Layout from "@/Layouts/OrganicLayout.vue";

import NotFound from "@/Pages/404.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

import HanziWriter from "hanzi-writer";
import tts2 from "@/azure_tts.js";

defineOptions({ layout: Layout });

let shouldStop = ref(false);
let playingWord = ref("");
let currentPlaybackId = ref(0);
let toggle_text = ref("เปิด");
let selectedAnswer = ref(null);

const props = defineProps({
    hanzi_list_data: Object
});

let hanzi_list = ref(props.hanzi_list_data);

console.log(hanzi_list.value.words)

let animateHanzi = [];

let writeHanzi = (id, letter, option = null) => {
    option = option ?? {
        // width: 100,
        // height: 100,
        // padding: 5,
        // strokeColor: '#EE00FF',

        character: null, // (The character to be rendered must be provided by the user)
        width: 50, // 300, // (Default width of the rendering area in pixels)
        height: 50, // 300, // (Default height of the rendering area in pixels)
        padding: 0, // 20, // (Padding around the character in pixels)
        strokeColor: "#B71F1F", // (Color of the strokes)
        showOutline: true, // (Whether to show the outline of the character)
        strokeAnimationSpeed: 1, // 1 (Multiplier for the speed of stroke animations)
        delayBetweenStrokes: 200, // 1000 (Delay between stroke animations in milliseconds)
        radicalColor: "#B71F1F", // null (Color for radical strokes, if any)

        // // highlightColor: null, // (Color for highlighting strokes)
        // outlineColor: '#DDD', // (Color for the outline of strokes)
        // drawingWidth: 2, // (Width of the drawing strokes)
        // drawingColor: '#333', // (Color of the drawing strokes)
        // showCharacter: true, // (Whether to show the character itself)
        // showHintAfterMisses: 3, // (Number of misses before showing a hint)
        // highlightOnComplete: true, // (Whether to highlight the character on completion)
        // drawingFadeDuration: 400, // (Duration for fading out drawing strokes in milliseconds)
        // animationDuration: 200, // (Default animation duration in milliseconds)
        // strokeOrder: true, // (Whether to show stroke order animations)
        // strokeOrderSpeed: 2, // (Speed multiplier for stroke order animations)
        // strokeOrderColors: null, // (Colors for the stroke order)
    };

    return HanziWriter.create(id, letter, option);
};

let playSound = (input) => {
    const tts = new tts2();

    currentPlaybackId.value++;
    const playbackId = currentPlaybackId.value;
    shouldStop.value = false;

    // Using default settings from azure_tts.js
    // To customize: tts.speak2(text, 'zh-CN', { rate: 0.5, pitch: 1.2 })

    // Function to process a single sound
    function processSound(sound) {
        return new Promise((resolve) => {
            tts.speak2(sound);

            playingWord.value = sound;

            resolve();
        });
    }

    // Function to handle delay
    function sleep(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms));
    }

    // Async function to handle the array processing with delay
    async function processArray(sounds) {
        for (const sound of sounds) {
            if (shouldStop.value || playbackId !== currentPlaybackId.value) {
                break;
            }

            await processSound(sound);
            await sleep(1000);
        }
    }

    // Check if the input is an array
    if (Array.isArray(input)) {
        // Process each sound in the array with a delay
        processArray(input);
    } else {
        // Process the single sound
        processSound(input);
    }
};

let stopPlayback = () => {
    shouldStop.value = true;
};

let writeAnimate = (no) => {
    animateHanzi[no - 1].animateCharacter();
    // animateHanzi[no].loopCharacterAnimation()
};

const leftWords = computed(() => {
    const halfLength = Math.ceil(hanzi_list.value.words.length / 2);
    return hanzi_list.value.words.slice(0, halfLength);
});

const rightWords = computed(() => {
    const halfLength = Math.ceil(hanzi_list.value.words.length / 2);
    return hanzi_list.value.words.slice(halfLength);
});

const handleFlip = (index) => {
    hanzi_list.value.words[index].isFlipped =
        !hanzi_list.value.words[index].isFlipped;
    if (hanzi_list.value.words[index].isFlipped) {
        setTimeout(() => {
            playSound(hanzi_list.value.words[index].character);
        }, 500);
    }

    if (hanzi_list.value.words.filter((item) => !item.isFlipped).length == 0) {
        toggle_text.value = "ปิด";
    }
};

const unflipAll = () => {
    hanzi_list.value.words.forEach((word, index) => {
        word.isFlipped = false;
        word.selectedAnswer = null;
    });

    setTimeout(() => {
        toggle_text.value = "เปิด";
        shuffle(hanzi_list.value.words);
    }, 600);
};

const cardToggle = () => {
    if (toggle_text.value === "เปิด") {
        hanzi_list.value.words.forEach((word, index) => {
            word.isFlipped = true;
            // word.selectedAnswer = null
        });
        toggle_text.value = "ปิด";
    } else {
        hanzi_list.value.words.forEach((word, index) => {
            word.isFlipped = false;
            // word.selectedAnswer = null
        });
        toggle_text.value = "เปิด";
    }
};

const shuffle = (array) => {
    array.forEach((word) => (word.selectedAnswer = null));
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]]; // ES6 destructuring assignment
    }
};

const submitGuess = async (word, isCorrect) => {

    router.post('/word-guess', {
        dictionary_zh_hans_id: word.id,
        is_correct: isCorrect,
    }, {
        preserveScroll: true,
        onSuccess: (page) => {

            const found = hanzi_list.value.words.find(w => w.id === word.id);
            if (found) {
                found.latest_guess = isCorrect ? 1 : 0;
            }

            console.log('Guess recorded!');
        }
    });
};

const markCorrect = (word) => {
    word.selectedAnswer = "correct";
    word.lastAnswer = "correct";
    submitGuess(word, true);
};

const markWrong = (word) => {
    word.selectedAnswer = "wrong";
    word.lastAnswer = "wrong";
    submitGuess(word, false);
};

const correctCount = computed(
    () =>
        hanzi_list.value.words.filter(
            (word) => word.selectedAnswer === "correct"
        ).length
);

const totalCount = computed(() => hanzi_list.value.words.length);

onMounted(() => {

    hanzi_list.value.words.forEach((item, index) => {
        item.selectedAnswer = null;
        animateHanzi[index] = writeHanzi(
            `hanzi_${item.character}`,
            item.character,
            {
                width: 100,
                height: 100,
                strokeAnimationSpeed: 2,
                delayBetweenStrokes: 150,
                radicalColor: "#B71F1F",
                padding: 3,
            }
        );
    });
});

onBeforeUnmount(() => {
    stopPlayback();
});
</script>

<template>
    <Head title="การ์ดคำ" />

    <div
        class="pt-3 pb-10 px-6 mx-auto max-w-screen-xl sm:px-8 md:px-12 lg:px-16 xl:px-24"
    >
        <div class="flex flex-col justify-center items-center">
            <div
                class="w-full sm:px-20 flex flex-col gap-3 sm:flex-row sm:justify-between"
            >
                <div class="flex justify-center items-center flex-wrap gap-3">
                    <Link
                        preserve-scroll
                        as="button"
                        :href="`/warrior_flip_card?set=hsk1&word_count=10`"
                        class="flex justify-center items-center gap-x-1 mb-4 px-3 py-2 border-2 border-gray-300 rounded-md text-lg hover:font-bold hover:bg-red hover:text-white"
                    >
                        <span>สุ่ม HSK 1</span>
                    </Link>
                    <Link
                        preserve-scroll
                        as="button"
                        :href="`/warrior_flip_card?set=hsk2&word_count=10`"
                        class="flex justify-center items-center gap-x-1 mb-4 px-3 py-2 border-2 border-gray-300 rounded-md text-lg hover:font-bold hover:bg-red hover:text-white"
                    >
                        <span>สุ่ม HSK 2</span>
                    </Link>
                    <Link
                        preserve-scroll
                        as="button"
                        :href="`/warrior_flip_card?set=hsk3&word_count=10`"
                        class="flex justify-center items-center gap-x-1 mb-4 px-3 py-2 border-2 border-gray-300 rounded-md text-lg hover:font-bold hover:bg-red hover:text-white"
                    >
                        <span>สุ่ม HSK 3</span>
                    </Link>
                </div>

                <div class="flex justify-center items-center flex-wrap gap-3">
                    <button
                        class="flex justify-center items-center gap-x-1 mb-4 px-3 py-2 border-2 border-gray-300 rounded-md text-lg hover:font-bold hover:bg-red hover:text-white"
                        @click="unflipAll"
                    >
                        <i class="pi pi-replay"></i>
                        <span>สลับ</span>
                    </button>
                    <button
                        class="flex justify-center items-center gap-x-1 mb-4 px-3 py-2 border-2 border-gray-300 rounded-md text-lg hover:font-bold hover:bg-red hover:text-white"
                        @click="cardToggle"
                    >
                        <i class="pi pi-eye" v-if="toggle_text === 'เปิด'"></i>
                        <i
                            class="pi pi-eye-slash"
                            v-if="toggle_text === 'ปิด'"
                        ></i>
                        <span v-text="toggle_text"></span>
                    </button>
                </div>
            </div>

            <!-- Score for mobile -->
            <div
                class="my-4 font-bold text-lg text-center text-gray-700"
            >
                คะแนน: {{ correctCount }} / {{ totalCount }}
            </div>

            <div class="flex justify-center items-center flex-wrap gap-3">
                <FlipCard
                    :width="160"
                    :height="150"
                    v-for="(word, index) in hanzi_list.words"
                    :key="word"
                    :word="word.character"
                    :isFlipped="word.isFlipped"
                    :guessResult="word.latest_guess"
                    @flip="handleFlip(index)"
                >
                    <template #front>
                        <div class="absolute top-3 left-3 text-red-500 text-sm">
                            {{ index + 1 }}
                        </div>

                        <div
                        class="absolute bottom-2 right-2 text-lg"
                        v-if="word.lastAnswer || word.latest_guess !== null"
                        :class="{
                            'text-green-500': word.lastAnswer === 'correct' || (!word.lastAnswer && word.latest_guess === 1),
                            'text-red-500': word.lastAnswer === 'wrong' || (!word.lastAnswer && word.latest_guess === 0),
                        }"
                        >
                            <i
                                :class="{
                                    'pi pi-check-circle': word.lastAnswer === 'correct' || (!word.lastAnswer && word.latest_guess === 1),
                                    'pi pi-times-circle': word.lastAnswer === 'wrong' || (!word.lastAnswer && word.latest_guess === 0),
                                }"
                            ></i>
                        </div>

                        <div
                            :id="`hanzi_${word.character}`"
                            class="w-32 h-32 mx-auto my-auto flex justify-center items-center cursor-pointer"
                        ></div>
                    </template>
                    <template v-slot:back>
                        <div class="absolute top-3 left-3 text-red-500 text-sm">
                            {{ index + 1 }}
                        </div>
                        <div class="flex gap-2 absolute top-3 right-3">
                            <button
                                @click.stop="markCorrect(word)"
                                class="text-gray-500 text-sm hover:text-green-600 hover:scale-125 hover:font-bold"
                                :class="{
                                    'text-green-600 font-bold scale-125':
                                        word.selectedAnswer === 'correct',
                                }"
                            >
                                <i class="pi pi-check-circle"></i>
                            </button>

                            <button
                                @click.stop="markWrong(word)"
                                class="text-gray-500 text-sm hover:text-red-600 hover:scale-125 hover:font-bold"
                                :class="{
                                    'text-red-600 font-bold scale-125':
                                        word.selectedAnswer === 'wrong',
                                }"
                            >
                                <i class="pi pi-times-circle"></i>
                            </button>
                        </div>
                        <div
                            class="flex flex-col gap-y-2 justify-center items-center h-full w-full"
                        >
                            <div class="font-bold text-5xl">
                                {{ word.pinyin }}
                            </div>
                            <div class="font-bold text-base">
                                {{ word.meaning_thai ?? word.definition }}
                            </div>
                        </div>
                    </template>
                </FlipCard>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
