<script setup>
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

import StarIcon from "@/../icons/star.svg";
import FuelerIcon from "@/../icons/fueler.svg";
import ChefPromote from "@/Components/ChefPromote.vue";

// avatar images
const avatarImports = import.meta.glob("../../images/img/avatar-*.jpeg", {
    eager: true,
});

const avatars = ref({});

Object.keys(avatarImports).forEach((path) => {
    const match = path.match(/avatar-(\d+)\.jpeg$/);
    if (match) {
        avatars.value[match[1]] = avatarImports[path].default;
    }
});

const getAvatarSrc = (index) => avatars.value[index] || "";
// avatar images

</script>

<template>
    <Head title="Home" />
    <OrganicLayout>
        <div class="relative z-10 md:w-1/2 w-full">
            <img
                class="absolute top-0 right-0 md:-top-4 md:-right-8 w-24 h-auto"
                src="@/../images/img/leaf.png"
                alt=""
            />
            <span class="flex items-center px-1 text-xl text-green">
                <span class="font-medium">100% Organic food</span>
                <img
                    class="w-auto h-8"
                    src="@/../images/img/vegetable.png"
                    alt=""
                />
            </span>
            <h1
                class="pt-4 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tighter leading-tight whitespace-nowrap"
            >
                Healthy Lifestyle <br />
                is your only <br />
                <span class="whitespace-nowrap text-green">
                    unfair advantage
                </span>
            </h1>
            <p
                class="pt-8 sm:text-lg max-w-md font-normal text-gray-600 leading-relaxed"
            >
                Choose healthy habits with Organo daily meal prepared by our
                expert nutritionist and chef
            </p>
            <div class="flex pt-8 space-x-4 sm:space-x-6">
                <button
                    class="flex justify-center items-center w-full sm:w-auto h-13 px-8 bg-green font-medium text-white rounded-xl whitespace-nowrap hover:shadow-primary transition-shadow duration-300"
                >
                    Get started
                </button>
                <button
                    class="flex justify-center items-center w-full sm:w-auto h-13 px-8 font-medium text-gray-900 border border-gray-900 rounded-xl whitespace-nowrap hover:shadow-xl transition-shadow duration-300"
                >
                    Explore menu
                </button>
            </div>

            <ChefPromote />

            <div v-if="false">
                <div
                    class="flex md:hidden pt-8 justify-end space-x-1 font-bold"
                >
                    <span>Powered by</span>
                    <FuelerIcon class="w-6 h-6 text-gray-900 fill-current" />
                    <span>Fueler</span>
                </div>
            </div>
        </div>

        <div class="relative md:w-1/2 w-full flex flex-col justify-between">
            <img
                class="w-96 lg:w-full drop-shadow-2xl self-center lg:self-end"
                src="@/../images/img/dish.png"
                alt=""
            />
            <div
                class="absolute right-0 lg:-right-6 top-0 lg:top-28 flex flex-col py-5 px-7 rounded-2xl shadow-xl bg-white/80 backdrop-blur-xl hover:-translate-y-2 hover:shadow-2xl transition-all duration-300"
            >
                <div class="flex -space-x-3">
                    <div
                        v-for="i in 3"
                        class="w-13 h-13 rounded-full border-4 border-white object-cover overflow-hidden"
                    >
                        <img :src="getAvatarSrc(i)" alt="" />
                    </div>
                </div>
                <div class="pt-3 font-bold">Happy customers</div>
                <div class="flex items-center text-gray-600 leading-relaxed">
                    <StarIcon class="w-5 h-5" />
                    <span class="pl-1">4.9 (+2.5k Ratings)</span>
                </div>
            </div>
            <div
                class="absolute left-0 bottom-0 md:bottom-32 lg:bottom-16 flex bg-white/80 rounded-2xl shadow-xl backdrop-blur-xl hover:-translate-y-2 hover:shadow-2xl transition-all duration-300"
            >
                <img
                    class="w-auto h-20 self-end"
                    src="@/../images/img/driver.png"
                    alt=""
                />
                <div class="pr-7 pl-2 py-5">
                    <div class="font-bold">Fast delivery</div>
                    <div class="text-gray-600 leading-relaxed">
                        30 mins delivery 🚀
                    </div>
                </div>
            </div>
            <div
                class="hidden md:flex justify-end space-x-1 font-bold"
                v-if="false"
            >
                <span>Powered by</span>
                <FuelerIcon class="w-6 h-6 text-gray-900 fill-current" />
                <span>Fueler</span>
            </div>
        </div>
    </OrganicLayout>
</template>
