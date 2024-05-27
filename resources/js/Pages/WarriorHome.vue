<script setup>
import Layout from "@/Layouts/OrganicLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
// import { ref } from "vue";

defineOptions({ layout: Layout });

// import StarIcon from "@/../icons/star.svg";
// import FuelerIcon from "@/../icons/fueler.svg";
// import ChefPromote from "@/Components/ChefPromote.vue";
// import warrior_exam from "@/../images/warrior_exam.png";
</script>

<template>
    <Head title="Home" />

    <div
        class="relative z-10 w-full flex flex-col items-center justify-center gap-y-4 -mt-12"
    >
        <img
            class="w-72 md:w-96 h-auto"
            src="@/../images/img/object/page_not_found.png"
            alt=""
        />
        <h1 class="text-4xl font-sans font-extrabold">Page Not Found</h1>

        <Link
            href="/warrior_home"
            class="flex justify-center items-center w-full sm:w-auto h-16 px-7 py-2 text-xl font-medium hover:-rotate-3 transition-all ease-out duration-300 text-base font-semibold leading-7 text-white bg-red border border-red rounded-lg focus:outline-red focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 hover:bg-transparent hover:text-red sm:w-auto hover:scale-[1.01] focus:bg-transparent focus:text-red hover:shadow-hsk whitespace-nowrap"
        >
            กลับหน้าหลัก
        </Link>
    </div>
</template>
