<script setup>
import HanziCard from '@/Components/HanziCard.vue';
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, nextTick } from "vue";

</script>

<template>
    <Head title="Hanzi Card Test" />
    <OrganicLayout>
       <div class=" min-h-screen flex items-center justify-center p-8">
         <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <HanziCard 
                hanzi="汉" 
                pinyin="hàn" 
                explanation="ชาวฮั่น / (หั้น)" 
                hskLevel="3" 
            />
                        
            <HanziCard 
                hanzi="字" 
                pinyin="zì" 
                explanation="อักษร / (จื้อ)" 
            />
                        
            <HanziCard 
                hanzi="卡" 
                pinyin="kǎ" 
                explanation="บัตร / (ข่า)" 
            />
                        
            <HanziCard 
                hanzi="学" 
                pinyin="xué" 
                explanation="เรียน / (เสวีย)" 
            />
         </div>
    </div>
    </OrganicLayout>
</template>
