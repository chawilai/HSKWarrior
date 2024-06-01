<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed, onMounted, onBeforeUnmount, onUnmounted } from "vue";
import HanziWriter from "hanzi-writer";
import tts2 from "@/tts.js";
import warrior_logo from "@/../images/warrior_logo.png";
import QRCode from "qrcode";

const mainUrl = `${window.location.protocol}//${window.location.hostname}${
    window.location.port ? ":" + window.location.port : ""
}`;

const props = defineProps({
    hanzi_list_data: Object,
});

const hanzi_list = ref(props.hanzi_list_data);

const url = `${mainUrl}/hanzi_list?reference=${hanzi_list.reference}`;

let qrImage = ref("");
let animateHanzi = [];
let shouldStop = ref(false);
let playingWord = ref("");
let currentPlaybackId = ref(0);
const startPoints = ref([]);
let hanzi_start = ref([]);

// add circle with number
let addStrokeNumber = () => {
    // <circle cx="25" cy="421" r="150" fill="red" />
    //             <!-- Number inside the circle with flip -->
    //             <text
    //                 x="25"
    //                 y="421"
    //                 font-size="150"
    //                 text-anchor="middle"
    //                 fill="white"
    //                 dy=".3em"
    //                 transform="scale(1,-1) translate(0,-842)"
    //             >
    //                 1
    //             </text>
};

// add number to SVG
let svgElements = ref(null);
// add number to SVG

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

let writeAnimate = (no) => {
    animateHanzi[no - 1].animateCharacter();
    // animateHanzi[no].loopCharacterAnimation()
};

QRCode.toDataURL(url)
    .then((res) => {
        qrImage.value = res;
    })
    .catch((err) => {
        console.error(err);
    });

// onMounted(() => {
//     document.body.classList.add("A4");
//     document.body.classList.add("bg-gradient-to-br");
//     document.body.classList.add("from-red-100");
//     document.body.classList.add("to-red-300");

//     hanzi_list.value.words.forEach((item, index) => {
//         animateHanzi[index] = writeHanzi(
//             `hanzi_${item.character}`,
//             item.character,
//             {
//                 width: 50,
//                 height: 50,
//                 strokeAnimationSpeed: 2,
//                 delayBetweenStrokes: 150,
//                 radicalColor: "#B71F1F",
//                 padding: 3,
//             }
//         );
//     });

//     HanziWriter.loadCharacterData("二").then((character) => {
//         // console.log(character.strokes);
//         // console.log("Stroke count:", character.strokes.length);

//         svgElements = document.querySelectorAll("svg");

//         console.log(svgElements[1].innerHTML);

//         const gs = svgElements[1].querySelector("g");

//         console.log(gs.innerHTML);

//         let startPoints = character.strokes.map((path) => {
//             const match = path.match(/M (\d+) (\d+)/);
//             return match
//                 ? { x: parseInt(match[1]), y: parseInt(match[2]) }
//                 : null;
//         });

//         console.log(startPoints);

//         return;

//         startPoints.forEach((point) => {
//             console.log(point);

//             const circle = document.createElementNS(
//                 "http://www.w3.org/2000/svg",
//                 "circle"
//             );
//             circle.setAttribute("cx", point.x);
//             circle.setAttribute("cy", point.y);
//             circle.setAttribute("r", 70); // Set radius of the circle
//             circle.setAttribute("fill", "red"); // Set fill color of the circle

//             gs.appendChild(circle);
//         });
//     });

//     setTimeout(() => {
//         // const gs = svgElements[1].querySelector("g");
//         // console.log(gs.innerHTML);
//         /////
//         // const circle = document.createElementNS(
//         //     "http://www.w3.org/2000/svg",
//         //     "circle"
//         // );
//         // circle.setAttribute("cx", point[0]);
//         // circle.setAttribute("cy", point[1]);
//         // circle.setAttribute("r", 70); // Set radius of the circle
//         // circle.setAttribute("fill", "red"); // Set fill color of the circle
//         // gs.appendChild(circle);
//         // const text = document.createElementNS(
//         //     "http://www.w3.org/2000/svg",
//         //     "text"
//         // );
//         // text.textContent = 1;
//         // text.setAttribute("x", 25);
//         // text.setAttribute("y", 421);
//         // text.setAttribute("font-size", 130);
//         // text.setAttribute("text-anchor", "middle");
//         // text.setAttribute("fill", "white");
//         // text.setAttribute("dy", ".3em");
//         // text.setAttribute("transform", "scale(1,-1) translate(0,-842)");
//         // paths[0].appendChild(text);
//         /////
//         // svgElements.forEach((svg) => {
//         //     const gs = svg.querySelectorAll("g")
//         //     console.log(gs[0].innerHTML)
//         //     // gs.forEach(g => console.log(g.innerHTML))
//         // });
//     }, 500);

//     // console.log(svgElements)
// });

onMounted(() => {
    document.body.classList.add("A4");
    document.body.classList.add("bg-gradient-to-br");
    document.body.classList.add("from-red-100");
    document.body.classList.add("to-red-300");

    hanzi_list.value.words.forEach((item, index) => {
        hanzi_start[index] = [];
        animateHanzi[index] = writeHanzi(
            `hanzi_${item.character}`,
            item.character,
            {
                width: 50,
                height: 50,
                strokeAnimationSpeed: 2,
                delayBetweenStrokes: 150,
                radicalColor: "#B71F1F",
                padding: 3,
            }
        );
    });

    // setTimeout(() => {

    //     svgElements = document.querySelectorAll("svg");

    //     // const gs = svgElements[0].querySelector("g");
    //     // let point = {x: 518, y: 382}
    //     // let point = {x: 25, y: 421}


    //     const gs = svgElements[1].querySelector("g");
    //     // let point = {x: 308, y: 603}
    //     // let point = {x: 517, y: 212}
    //     // let point = {x: 193.2, y: 593.4}
    //     let point = {x: 29.6, y: 245.5}

    //     const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
    //     circle.setAttribute("cx", point.x);
    //     circle.setAttribute("cy", point.y);
    //     circle.setAttribute("r", 80); // Set radius of the circle
    //     circle.setAttribute("fill", "red"); // Set fill color of the circle

    //     gs.appendChild(circle);

    //     const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
    //     text.textContent = 1;
    //     text.setAttribute("x", point.x);
    //     text.setAttribute("y", point.y);
    //     text.setAttribute("font-size", 160);
    //     text.setAttribute("text-anchor", "middle");
    //     text.setAttribute("fill", "white");
    //     text.setAttribute("dy", ".3em");
    //     text.setAttribute("transform", "scale(1,-1) translate(0,-842)");
    //     gs.appendChild(text);

    //     const svgs = document.querySelectorAll("svg");
    //     svgs.forEach((svg, index) => {
    //         const paths = svg.querySelectorAll("path");
    //         paths.forEach((path) => {
    //             const d = path.getAttribute("d");
    //             const commands = d.split(" ");
    //             for (let i = 0; i < commands.length; i++) {
    //                 if (commands[i] === "M") {
    //                     const x = parseFloat(commands[i + 1]);
    //                     const y = parseFloat(commands[i + 2]);
    //                     // startPoints.value.push({ x, y });

    //                     if (!hanzi_start[index].includes({ x, y }))
    //                         hanzi_start[index].push({ x, y });

    //                     break;
    //                 }
    //             }
    //         });
    //     });

    //     const arrayOfArrays = Object.values(hanzi_start);

    //     arrayOfArrays.forEach(item => {
    //         let uniqueArray = item.filter((obj, index, self) =>
    //             index === self.findIndex((o) => o.x === obj.x && o.y === obj.y)
    //         );

    //         console.log(uniqueArray)
    //     })

    //     return

    //     const uniqueArray = arrayOfArrays.filter((obj, index, self) =>
    //         index === self.findIndex((o) => o.x === obj.x && o.y === obj.y)
    //     );

    //     console.log(uniqueArray);
    //     // console.log(hanzi_start);
    // }, 500);
});

onUnmounted(() => {
    document.body.classList.remove("A4");
    document.body.classList.remove("bg-gradient-to-br");
    document.body.classList.remove("from-red-100");
    document.body.classList.remove("to-red-300");
});
</script>

<template>
    <Head title="Home" />

    <section class="sheet p-[5mm] flex flex-col justify-between">
        <div class="w-full">
            <div class="flex justify-between mb-2">
                <a href="/warrior_home" class="text-xl font-bold tracking-wide">
                    <div class="flex items-center pl-3 py-3">
                        <img class="w-14 h-auto" :src="warrior_logo" alt="" />
                        <span class="text-red">HSK</span>
                        <span class="text-black">Warrior</span>
                    </div>
                </a>
                <img class="w-20 h-20" :src="qrImage" alt="" />
            </div>
            <hr class="border-1 border-red" />
        </div>
        <div class="p-5 flex-1 flex flex-col gap-y-3 w-full">
            <div v-for="(word, word_index) in hanzi_list.words">
                <div
                    class="text-xs pl-4"
                    v-html="
                        `${word.character} (${word.pinyin}) : ${word.definition} `
                    "
                ></div>
                <div class="relative flex justify-start">
                    <div
                        class="w-15 h-15 border-t border-b border-r border-red first:border-l last:border-r"
                        v-for="(box, index) in 12"
                    >
                        <div
                            class="absolute -top-2 -left-3 w-5 h-5 text-sm bg-red rounded-full text-white font-bold flex justify-center items-center"
                            v-text="word_index + 1"
                            v-if="index == 0"
                        ></div>
                        <div
                            v-if="index == 0"
                            :id="`hanzi_${word.character}`"
                            @click="
                                writeAnimate(word_index + 1),
                                    playSound(word.character)
                            "
                            class="w-14 h-14 mx-auto my-auto flex justify-center items-center cursor-pointer"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="border-1 border-red" />
        <div class="w-full p-3 text-xs">
            <strong
                >Copyright &copy; 2023-2024
                <a target="_blank" href="https://hskwarrior.com">HSK Warrior</a
                >.</strong
            >
            All rights reserved.
        </div>
    </section>
</template>

<style scoped>
@page {
    size: A4;
}

.sheet {
    page-break-after: always;
}

.sheet:last-of-type {
    page-break-after: auto;
}
</style>
