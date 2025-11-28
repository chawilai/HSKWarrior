<script setup lang="ts">
// import GuestLayout from "@/Layouts/GuestLayout.vue";
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, nextTick } from 'vue'

// 1. สร้าง State สำหรับควบคุมการเปิด/ปิด Drawer ด้วย ref
const isDrawerOpen = ref(false);

// 2. ฟังก์ชันสำหรับเปิด Drawer
const openDrawer = () => {
    isDrawerOpen.value = true;
};

// 3. ฟังก์ชันสำหรับปิด Drawer
const closeDrawer = () => {
    isDrawerOpen.value = false;
};

</script>

<template>
    <Head title="Drawer" />
    <div class="min-h-screen p-8">
            <h1 class="text-4xl font-bold mb-8">หน้าหลักของการผจญภัย</h1>
            
            <div class="flex sm:ml-24 pt-8 space-x-4 sm:space-x-6">
                <button
                    @click="openDrawer" role="button"
                    class="flex justify-center items-center w-full sm:w-auto h-16 px-7 py-2 text-xl font-medium hover:-rotate-3 transition-all ease-out duration-300 text-base font-semibold leading-7 text-white bg-red border border-red rounded-lg focus:outline-red focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 hover:bg-transparent hover:text-red sm:w-auto hover:scale-[1.01] focus:bg-transparent focus:text-red hover:shadow-hsk whitespace-nowrap"
                >
                    เริ่มผจญภัย
                </button>
            </div>
            
            <p class="mt-10">เมื่อกดปุ่ม Drawer จะเลื่อนขึ้นมาเต็มจอ</p>
        </div>

        <div 
            class="fixed inset-0 z-40 transform transition-transform duration-500"
            :class="isDrawerOpen ? 'translate-y-0' : 'translate-y-full'"
            aria-modal="true"
            role="dialog"
        >
            
            <div 
                @click="closeDrawer" class="absolute inset-0 bg-black transition-opacity duration-300"
                :class="isDrawerOpen ? 'opacity-50' : 'opacity-0 pointer-events-none'"
            ></div>

            <div class="absolute bottom-0 w-full h-full bg-base-200 text-base-content shadow-2xl overflow-y-auto">
                <div class="p-8">
                    <h2 class="text-4xl font-extrabold mb-6">🚪 รายละเอียดการผจญภัย</h2>
                    <p class="mb-4">
                      นี่คือ Full-Screen Drawer ที่ควบคุมด้วย Vue `ref` และเลื่อนขึ้นจากด้านล่าง
                    </p>
                    
                    <ul class="menu bg-base-100 w-full rounded-box p-4">
                        <li><a class="text-lg">🗺️ แผนที่โลก</a></li>
                        <li><a class="text-lg">💰 ระบบเศรษฐกิจ</a></li>
                        <li><a class="text-lg">🛡️ ข้อมูลตัวละคร</a></li>
                    </ul>

                    <button 
                        @click="closeDrawer" 
                        class="btn btn-error btn-lg w-full mt-8"
                    >
                        ปิด Drawer
                    </button>
                </div>
            </div>
        </div>
</template>

<style scoped>

</style>
