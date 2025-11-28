<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const monsterImages = [
    '/images/atk1.png',
    '/images/atk2.png',
];
const currentMonsterImageIndex = ref(0);
let intervalId;

onMounted(() => {
    intervalId = setInterval(() => {
        currentMonsterImageIndex.value = (currentMonsterImageIndex.value + 1) % monsterImages.length;
    }, 800); 
});

onUnmounted(() => {
    clearInterval(intervalId);
});

const monsterImageSrc = () => {
    return monsterImages[currentMonsterImageIndex.value];
};
</script>

<template>
    <div class="flex justify-between items-center w-full h-screen p-4">
        <!-- Person on the left -->
        <div class="flex-1 flex justify-center">
            <img src="/images/mouse.png" alt="Person" class="h-64">
        </div>

        <!-- Animated Monster on the right -->
        <div class="flex-1 flex justify-center">
            <img :src="monsterImageSrc()" alt="Monster" class="h-64">
        </div>
    </div>
</template>

<style scoped>
/* Scoped styles for Scene1 component if needed */
</style>
