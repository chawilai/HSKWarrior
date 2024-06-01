<script setup>
import HanziStrokeList from "@/Components/HanziStrokeList.vue";
import { onMounted } from "vue";
</script>

<template>
    <div class="container flex justify-center items-center mx-auto mt-4">
        <HanziStrokeList
            hanzi="你"
            frame-size="100"
            hanzi-size="95"
            class="flex gap-x-2"
        ></HanziStrokeList>
    </div>
</template>

<style scoped></style>
