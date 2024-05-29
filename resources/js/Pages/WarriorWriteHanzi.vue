<script setup>
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, router, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, watch, onMounted, onBeforeUnmount } from "vue";
import { debounce } from "lodash";
import Pagination from "@/Pages/Shared/Pagination.vue";

import HanziWriter from "hanzi-writer";

import tts2 from "@/tts.js";

defineOptions({ layout: OrganicLayout });

// const props = defineProps({
//     filters: Object,
//     hanzi_list: Object,
// });

let hanziWriterList = ref([]);
let shouldStop = ref(false);
let playingWord = ref("");
let currentPlaybackId = ref(0);

const pages = usePage();

let hanzi_list = pages.props.hanzi_list;

let search = ref(pages.props.filters.search);
let s_set = ref(pages.props.filters.s_set);
let s_hanzi = ref(pages.props.filters.s_hanzi);
let s_pinyin = ref(pages.props.filters.s_pinyin);
let s_mean = ref(pages.props.filters.s_mean);

const fetchData = () => {
    router.get(
        "/warrior_writehanzi",
        {
            s_set: s_set.value ?? null,
            s_hanzi: s_hanzi.value ?? null,
            s_pinyin: s_pinyin.value ?? null,
            s_mean: s_mean.value ?? null,
            search: search.value ?? null,
        },
        {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                hanzi_list = pages.props.hanzi_list;
                search.value = pages.props.filters.search;
                s_set.value = pages.props.filters.s_set;
                s_hanzi.value = pages.props.filters.s_hanzi;
                s_pinyin.value = pages.props.filters.s_pinyin;
                s_mean.value = pages.props.filters.s_mean;
            },
        }
    );
};

let playSound = (input) => {
    const tts = new tts2();

    currentPlaybackId.value++;
    const playbackId = currentPlaybackId.value;
    shouldStop.value = false;

    tts.rate(0.1); // 1.2 (0.1-10)
    tts.volume(0.8); // 0.5 (0-1)
    tts.pitch(1.2); // 1 (0.1-2) gooe 0.8 - 2

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

// watch(search, debounce((value) => fetchData(), 500));
// watch(s_set, debounce((value) => fetchData(), 500));
watch(s_hanzi, debounce((value) => fetchData(), 500));
watch(s_pinyin, debounce((value) => fetchData(), 500));
watch(s_mean, debounce((value) => fetchData(), 500));
watch(() => pages.props, (data) => (hanzi_list = data.hanzi_list));

watch(
    () => pages.url,
    () => {

        setTimeout(() => {
            hanzi_list.data.forEach((item) => {
                let animate_hanzi = writeHanzi(
                    `hanzi_${item.id}`,
                    item.character
                );

                hanziWriterList.value.push(animate_hanzi);
                animate_hanzi.loopCharacterAnimation();
            });
        }, 1000);
    }
);

let writeHanzi = (id, letter, option = null) => {
    return HanziWriter.create(id, letter, {
        // width: 100,
        // height: 100,
        // padding: 5,
        // strokeColor: '#EE00FF',

        character: null, // (The character to be rendered must be provided by the user)
        width: 50, // 300, // (Default width of the rendering area in pixels)
        height: 50, // 300, // (Default height of the rendering area in pixels)
        padding: 0, // 20, // (Padding around the character in pixels)
        strokeColor: "#555", // (Color of the strokes)
        showOutline: true, // (Whether to show the outline of the character)
        strokeAnimationSpeed: 1, // 1 (Multiplier for the speed of stroke animations)
        delayBetweenStrokes: 200, // 1000 (Delay between stroke animations in milliseconds)
        radicalColor: "#168F16", // null (Color for radical strokes, if any)

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
    });
};

onMounted(() => {
    setTimeout(() => {
        hanzi_list.data.forEach((item) => {
            let animate_hanzi = writeHanzi(`hanzi_${item.id}`, item.character);

            hanziWriterList.value.push(animate_hanzi);
            animate_hanzi.loopCharacterAnimation();
        });
    }, 1000);
});

onBeforeUnmount(() => {
    stopPlayback();
});
</script>

<template>
    <Head>
        <title>Hànzì</title>
        <!-- <link rel="icon" type="image/svg+xml" href="@/../images/warrior_logo.png" /> -->
    </Head>

    <div
        class="flex flex-wrap-reverse gap-y-24 justify-between py-14 px-6 mx-auto max-w-screen-xl sm:px-8 md:px-12 lg:px-16 xl:px-24"
    >
        <div class="flex flex-col gap-y-2 w-full -mt-10">
            <!-- <div class="flex justify-end gap-x-2">
            <div class="flex flex-col justify-center">
                <span class="text-xl font-bold">ค้นหา</span>
                <span class="text-based">(Hanzi, Pinyin, Meaning)</span>
            </div>
            <input
                type="text"
                name="search"
                v-model="search"
                @keydown.enter="fetchData()"
                class="border border-gray-300 px-2 rounded-lg text-center w-52"
                autocomplete="off"
            />
        </div> -->
            <div class="overflow-x-auto w-full">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                <select
                                    class="text-center h-10 w-12/12 rounded-lg"
                                    name="s_set"
                                    v-model="s_set"
                                    @change="fetchData()"
                                >
                                    <option></option>
                                    <option>hsk1</option>
                                    <option>hsk2</option>
                                    <option>hsk3</option>
                                    <option>hsk4</option>
                                    <option>hsk5</option>
                                    <option>hsk6</option>
                                    <option>hsk7</option>
                                    <option>hsk8</option>
                                </select>
                            </th>
                            <th>
                                <input
                                    class="text-center h-10 w-10/12 rounded-lg"
                                    type="text"
                                    name="s_hanzi"
                                    v-model="s_hanzi"
                                    autocomplete="off"
                                />
                            </th>
                            <th></th>
                            <th>
                                <input
                                    class="text-center h-10 w-10/12 rounded-lg"
                                    type="text"
                                    name="s_pinyin"
                                    v-model="s_pinyin"
                                    autocomplete="off"
                                />
                            </th>
                            <th></th>
                            <th>
                                <input
                                    class="text-center h-10 w-10/12 rounded-lg"
                                    type="text"
                                    name="s_mean"
                                    v-model="s_mean"
                                    autocomplete="off"
                                />
                            </th>
                        </tr>
                        <tr class="h-2"></tr>
                    </thead>
                    <thead>
                        <tr>
                            <th
                                class="w-24 bg-red text-white border border-gray-300 py-1 px-2 text-center"
                            >
                                id
                            </th>
                            <th
                                class="w-24 bg-red text-white border border-gray-300 py-1 px-2 text-center"
                            >
                                set
                            </th>
                            <th
                                class="w-24 bg-red text-white border border-gray-300 py-1 px-2 text-center"
                            >
                                hanzi
                            </th>
                            <th
                                class="w-24 bg-red text-white border border-gray-300 py-1 px-2 text-center"
                            >
                                hanzi
                            </th>
                            <th
                                class="w-24 bg-red text-white border border-gray-300 py-1 px-2 text-center"
                            >
                                pinyin
                            </th>
                            <th
                                class="w-24 bg-red text-white border border-gray-300 py-1 px-2 text-center"
                            >
                                play
                            </th>
                            <th
                                class="bg-red text-white border border-gray-300 py-1 px-2 text-center"
                            >
                                meaning
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(hanzi, index) in hanzi_list.data"
                            :key="hanzi.id"
                        >
                            <td
                                class="w-24 border border-gray-300 py-1 px-2 text-center"
                                v-text="hanzi_list.from + index"
                            ></td>
                            <td
                                class="w-24 border border-gray-300 py-1 px-2 text-center"
                                v-text="hanzi.set"
                            ></td>
                            <td
                                class="w-24 text-2xl font-medium border border-gray-300 py-1 px-2 text-center"
                                v-text="hanzi.character"
                            ></td>
                            <td
                                class="w-24 border border-gray-300 py-1 px-2 text-center"
                            >
                                <div
                                    :id="`hanzi_${hanzi.id}`"
                                    class="w-10 mx-auto"
                                ></div>
                            </td>
                            <td
                                class="w-24 border border-gray-300 py-1 px-2 text-center"
                                v-text="hanzi.pinyin"
                            ></td>
                            <td
                                class="w-24 border border-gray-300 py-1 px-2 text-center"
                            >
                                <i
                                    class="pi pi-volume-up text-lg cursor-pointer hover:text-red hover:font-bold hover:scale-125"
                                    @click="playSound(hanzi.character)"
                                ></i>
                            </td>
                            <td
                                class="border border-gray-300 py-1 px-2"
                                v-text="hanzi.definition"
                            ></td>
                        </tr>
                    </tbody>
                </table>

                <!-- pagination -->
                <Pagination class="w-fit mt-2" :links="hanzi_list.links" />
            </div>
        </div>
    </div>
</template>
