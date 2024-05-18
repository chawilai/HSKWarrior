<script setup>
import { onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
// import Badge from '@/Components/Badge.vue';
import Typewriter from "typewriter-effect/dist/core";
import HanziWriter from "hanzi-writer";

let typeWords = (el_id, text_arr = []) => {
    let typewriter = new Typewriter(document.getElementById(el_id), {
        loop: true, // false,
        delay: 50, // 50,
        deleteSpeed: 30, // 50,
        cursor: "|", // "|",
        cursorClassName: "typewriter-cursor", // "typewriter-cursor",
        autoStart: false, // false,
        strings: [], // [],
    });

    text_arr.forEach((word, index) => {
        typewriter.typeString(word).pauseFor(500).deleteAll().pauseFor(500);
    });
    typewriter.start();
};

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
        strokeColor: '#555', // (Color of the strokes)
        showOutline: true, // (Whether to show the outline of the character)
        strokeAnimationSpeed: 1, // 1 (Multiplier for the speed of stroke animations)
        delayBetweenStrokes: 200, // 1000 (Delay between stroke animations in milliseconds)
        radicalColor: '#168F16', // null (Color for radical strokes, if any)

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
}

onMounted(() => {
    typeWords("service_typing", [
        "แบบเรียน HSK 1-6",
        "แบบฝึกหัด HSK 1-6",
        "Flash Card ทายคำ",
        "ฝึก Pīnyīn",
        "หมวดหมู่คำศัพท์",
        "ทายประโยค",
        "ฝีกเขียนจีน Hànzì",
        "แข่งกับเพื่อน",
    ]);

    writeHanzi('hanzi', '我').loopCharacterAnimation();
    writeHanzi('hanzi2', '爱').loopCharacterAnimation();
    writeHanzi('hanzi3', '学').loopCharacterAnimation();
    writeHanzi('hanzi4', '习').loopCharacterAnimation();
    writeHanzi('hanzi5', '中').loopCharacterAnimation();
    writeHanzi('hanzi6', '文').loopCharacterAnimation();

    // writer.animateCharacter();
    // writer.loopCharacterAnimation();
});
</script>

<template>
    <Head title="Welcome" />

    <div class="">
        <img
            class="absolute w-full"
            src="/storage/images/intersect.svg"
            alt=""
        />

        <section
            class="h-auto md:h-svh font-sans bg-gradient-to-b from-gray-50 to-white"
        >
            <header
                class="relative z-50 h-24 md:mt-0 md:translate-y-0 bg-gray-50"
            >
                <div
                    class="flex items-center w-full h-full px-6 mx-auto max-w-7xl md:px-8"
                >
                    <div
                        class="relative flex items-center justify-between w-full h-auto"
                    >
                        <div class="flex-shrink-0">
                            <a
                                href="/"
                                class="flex items-center justify-center rounded outline-none active:outline-none"
                            >
                                <div class="relative w-12 h-12 mr-2">
                                    <img
                                        src="/storage/images/warrior_logo.png"
                                        alt=""
                                    />
                                </div>
                                <span
                                    class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-red-500 to-gray-900"
                                    >HSK Warrior</span
                                >
                            </a>
                        </div>
                        <div class="flex md:hidden">
                            <button
                                id="open-menu"
                                onclick="document.getElementById('menu').classList.remove('hidden'); document.getElementById('close-menu').classList.remove('hidden'); this.classList.add('hidden');"
                                type="button"
                                class="text-gray-900"
                            >
                                <svg
                                    class="w-7 h-7"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    ></path>
                                </svg>
                            </button>
                            <button
                                id="close-menu"
                                onclick="document.getElementById('menu').classList.add('hidden'); document.getElementById('open-menu').classList.remove('hidden'); this.classList.add('hidden');"
                                type="button"
                                class="hidden text-gray-900"
                            >
                                <svg
                                    class="w-7 h-7"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </button>
                        </div>
                        <div
                            id="menu"
                            class="fixed top-0 left-0 z-20 flex-col hidden w-full h-auto translate-y-24 shadow-sm md:absolute md:inset-y-0 md:flex md:flex-row md:top-auto md:translate-y-0 md:h-full md:w-auto bg-gray-50 md:shadow-none md:bg-transparent md:items-center md:justify-center md:space-x-12 md:-translate-x-1/2 md:left-1/2"
                        >
                            <Link
                                href="/"
                                class="relative flex items-center justify-center w-full h-16 px-1 text-base text-gray-900 text-opacity-100 transition-all duration-300 rounded outline-none cursor-pointer group md:w-auto md:h-auto md:inline-block md:hover:-rotate-3 active:ring-0 active:outline-none"
                            >
                                <span class="relative z-20 whitespace-nowrap"
                                    >Home</span
                                >
                                <span
                                    class="absolute bottom-0 left-0 z-10 w-0 h-2 transition-all duration-300 ease-out skew-x-12 group-hover:w-full bg-red-400"
                                ></span>
                            </Link>

                            <Link
                                href="#partner"
                                class="relative flex items-center justify-center w-full h-16 px-1 text-base text-gray-900 transition-all duration-300 rounded outline-none cursor-pointer text-opacity-80 hover:text-opacity-100 group md:w-auto md:h-auto md:inline-block md:hover:-rotate-3 active:outline-none"
                            >
                                <span class="relative z-20 whitespace-nowrap"
                                    >แบบเรียน HSK</span
                                >
                                <span
                                    class="absolute bottom-0 left-0 z-10 w-0 h-2 transition-all duration-300 ease-out skew-x-12 group-hover:w-full bg-red-400"
                                ></span>
                            </Link>
                            <!-- <ul
                                class="hidden lg:flex lg:items-center lg:w-auto lg:space-x-12"
                            >
                                <li class="group relative pt-4 pb-4 has-child">
                                    <a
                                        href="index.html"
                                        class="text-sm font-semibold text-blueGray-600 hover:text-blueGray-500"
                                        >HSK Textbook</a
                                    >
                                    <ul class="drop-down-menu min-w-200">
                                        <li>
                                            <a
                                                href="index.html"
                                                class="menu-sub-item text-sm text-blueGray-600 hover:text-blueGray-500"
                                                >แบบเรียน HSK 1</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="index-2.html"
                                                class="menu-sub-item text-sm text-blueGray-600 hover:text-blueGray-500"
                                                >แบบเรียน HSK 2</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="index-3.html"
                                                class="menu-sub-item text-sm text-blueGray-600 hover:text-blueGray-500"
                                                >แบบเรียน HSK 3</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="index-4.html"
                                                class="menu-sub-item text-sm text-blueGray-600 hover:text-blueGray-500"
                                                >แบบเรียน HSK 4</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="index-5.html"
                                                class="menu-sub-item text-sm text-blueGray-600 hover:text-blueGray-500"
                                                >แบบเรียน HSK 5</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="index-6.html"
                                                class="menu-sub-item text-sm text-blueGray-600 hover:text-blueGray-500"
                                                >แบบเรียน HSK 6</a
                                            >
                                        </li>
                                    </ul>
                                </li>
                            </ul> -->
                            <Link
                                href="#about"
                                class="relative flex items-center justify-center w-full h-16 px-1 text-base text-gray-900 transition-all duration-300 rounded outline-none cursor-pointer text-opacity-80 hover:text-opacity-100 group md:w-auto md:h-auto md:inline-block md:hover:-rotate-3 active:outline-none"
                            >
                                <span class="relative z-20 whitespace-nowrap"
                                    >แบบฝึกหัด HSK</span
                                >
                                <span
                                    class="absolute bottom-0 left-0 z-10 w-0 h-2 transition-all duration-300 ease-out skew-x-12 group-hover:w-full bg-red-400"
                                ></span>
                            </Link>
                            <Link
                                href="/documentation"
                                class="relative flex items-center justify-center w-full h-16 px-1 text-base text-gray-900 transition-all duration-300 rounded outline-none cursor-pointer text-opacity-80 hover:text-opacity-100 group md:w-auto md:h-auto md:inline-block md:hover:-rotate-3 active:outline-none"
                            >
                                <span class="relative z-20 whitespace-nowrap"
                                    >คัดจีน (Hànzì)</span
                                >
                                <span
                                    class="absolute bottom-0 left-0 z-10 w-0 h-2 transition-all duration-300 ease-out skew-x-12 group-hover:w-full bg-red-400"
                                ></span>
                            </Link>
                            <Link
                                href="/login_selfcare"
                                target="_blank"
                                title=""
                                class="flex items-center justify-center h-16 px-5 text-base font-semibold leading-7 text-center text-white transition-all duration-200 duration-300 ease-out md:hidden bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 hover:bg-red-500 hover:text-white focus:bg-red-500 focus:text-white"
                                role="button"
                                >เข้าสู่ระบบ</Link
                            >
                        </div>
                        <div
                            class="hidden md:flex md:items-center md:justify-center"
                        >
                            <Link
                                href="/login_selfcare"
                                title=""
                                class="px-5 py-2 hover:-rotate-3 transition-all ease-out duration-300 text-base font-semibold leading-7 text-gray-900 duration-200 bg-transparent border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 hover:bg-red-500 hover:text-white sm:w-auto hover:scale-[1.01] focus:bg-red-500 focus:text-white"
                                role="button"
                                >เข้าสู่ระบบ</Link
                            >
                        </div>
                    </div>
                </div>
            </header>
            <div class="overflow-x-hidden">
                <div
                    class="flex flex-col-reverse items-center justify-center md:flex-row"
                >
                    <div
                        class="relative z-10 -mt-48 flex flex-col items-center justify-center min-h-screen pb-5 md:-mt-48 md:pt-48 md:py-24 xl:pb-0"
                    >
                        <div
                            class="z-30 w-full text-center mt-14"
                        >
                            <p
                                class="flex items-center justify-center w-full h-10 px-4 text-xs font-medium text-white md:h-8 sm:text-sm bg-gradient-to-br from-red-500 md:w-auto to-red-400 md:rounded-full"
                            >
                                🚀🇨🇳 ผจญภัยในดินแดนมังกร ฝึกฝนจนเป็นจอมยุทธ!
                                🇹🇭💪

                                <!-- popup test -->

                                <!-- popup test -->
                            </p>
                        </div>
                        <div
                            class="relative px-6 mx-auto md:px-8 max-w-7xl"
                        >
                            <div class="max-w-3xl mx-auto text-center">
                                <h1
                                    class="mt-5 text-3xl font-bold text-gray-900"
                                >
                                    เรียนภาษาจีนสุดสนุกด้วย <br />
                                </h1>
                                <div class="text-5xl font-bold mt-5 text-transparent bg-gradient-to-r bg-clip-text from-red-500 to-gray-900">HSK Warrior</div>
                                <p
                                    class="mx-auto mt-6 text-base leading-7 text-gray-600 sm:max-w-md"
                                >
                                    สนุกกับการเรียนรู้ภาษาจีน ด้วยตนเอง
                                    ผ่านแบบเรียน แบบฝึกหัด และเกมสนุกๆ
                                    พร้อมบันทึกประวัติการฝึกฝน
                                    เพิ่มระดับเลเวลของคุณ
                                    จนเป็นนักรบที่แข็งแกร่ง
                                </p>
                                <div
                                    id="service_typing"
                                    class="mt-5 text-2xl text-center text-green-600"
                                ></div>

                                <div class="flex items-center justify-center mt-14">
                                    <div id="hanzi" class="w-14 h-auto"></div>
                                    <div id="hanzi2" class="w-14 h-auto"></div>
                                    <div id="hanzi3" class="w-14 h-auto"></div>
                                    <div id="hanzi4" class="w-14 h-auto"></div>
                                    <div id="hanzi5" class="w-14 h-auto"></div>
                                    <div id="hanzi6" class="w-14 h-auto"></div>
                                </div>

                                <div
                                    class="flex flex-col items-center justify-center gap-2 md:flex-row"
                                >
                                    <div
                                        class="relative inline-flex w-full mt-10 group sm:w-auto"
                                    >
                                        <div
                                            class="absolute duration-1000 transitiona-all opacity-70 -inset-px bg-gradient-to-r from-sky-400 via-blue-400 to-teal-400 rounded-xl blur-lg group-hover:opacity-100 group-hover:-inset-1 group-hover:duration-200 animate-tilt"
                                        ></div>
                                        <a
                                            href="/login_selfcare"
                                            class="relative sm:w-auto w-full hover:-rotate-3 transition-all ease-out duration-300 inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white duration-200 bg-gray-900 hover:bg-red-500 hover:scale-[1.01] rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500"
                                            role="button"
                                            ><span class="mr-2"
                                                >เข้าสู่ระบบ HSK Warrior</span
                                            ></a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-96 mt-14 md:-mt-10 md:block">
                        <img
                            src="/storage/images/warrior_exam.png"
                            alt=""
                            class="w-full animate-up-down"
                        />
                    </div>
                </div>
            </div>
            <!-- Powered By Tails Badge -->
            <!-- <Badge>Chawilai</Badge> -->
        </section>

        <section>
            <footer class="bg-gray-100 text-sm py-3">
                <div
                    class="container mx-auto flex justify-between items-center px-4"
                >
                    <p>&copy; 2024 Chawilai. All rights reserved.</p>
                    <div>
                        <!-- <a href="#" class="hover:underline">Privacy Policy</a>
                    <span class="mx-2">|</span>
                    <a href="#" class="hover:underline">Terms of Service</a>
                    <span class="mx-2">|</span> -->
                        <a href="#" class="hover:underline">Contact Us</a>
                    </div>
                </div>
            </footer>
        </section>
    </div>
</template>

<style>
html {
    scroll-behavior: smooth;
}

.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}

.typewriter-cursor {
    color: black; /* Change this to your desired cursor color */
    animation: blink 1s step-end infinite;
}

@keyframes blink {
    from,
    to {
        color: transparent;
    }
    50% {
        color: black;
    } /* Change this to match the cursor color */
}
</style>
