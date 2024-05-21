<script setup>
import Main from "@/Layouts/Main.vue";
import { Head } from "@inertiajs/vue3";

import warrior_exam from '/resources/images/warrior_exam.png';

</script>

<template>
    <Head title="Home" />

    <Main>
        <div
            class="flex flex-col-reverse items-center justify-center md:flex-row"
        >
            <div
                class="relative z-10 -mt-48 flex flex-col items-center justify-center min-h-screen pb-5 md:-mt-48 md:pt-48 md:py-24 xl:pb-0"
            >
                <div class="z-30 w-full text-center mt-14">
                    <p
                        class="flex items-center justify-center w-full h-10 px-4 text-xs font-medium text-white md:h-8 sm:text-sm bg-gradient-to-br from-red-500 md:w-auto to-red-400 md:rounded-full"
                    >
                        🚀🇨🇳 ผจญภัยในดินแดนมังกร ฝึกฝนจนเป็นจอมยุทธ! 🇹🇭💪

                        <!-- popup test -->

                        <!-- popup test -->
                    </p>
                </div>
                <div class="relative px-6 mx-auto md:px-8 max-w-7xl">
                    <div class="max-w-3xl mx-auto text-center">
                        <h1 class="mt-5 text-3xl font-bold text-gray-900">
                            เรียนภาษาจีนสุดสนุกด้วย <br />
                        </h1>
                        <div
                            class="text-5xl font-bold mt-5 text-transparent bg-gradient-to-r bg-clip-text from-red-500 to-gray-900"
                        >
                            HSK Warrior
                        </div>
                        <p
                            class="mx-auto mt-6 text-base leading-7 text-gray-600 sm:max-w-md"
                        >
                            สนุกกับการเรียนรู้ภาษาจีน ด้วยตนเอง ผ่านแบบเรียน
                            แบบฝึกหัด และเกมสนุกๆ พร้อมบันทึกประวัติการฝึกฝน
                            เพิ่มระดับเลเวลของคุณ จนเป็นนักรบที่แข็งแกร่ง
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
                                    href="/login"
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
            <div class="w-96 mt-14 bg-gredreen-500 md:-mt-10 md:block">
                <img
                    :src="warrior_exam"
                    alt=""
                    class="w-full animate-up-down"
                />
            </div>
        </div>
    </Main>
</template>
