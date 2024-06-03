<script setup>
import { onMounted, ref } from "vue";
import SignaturePad from "@/Components/SignaturePad.vue";

import { useCounterStore } from "@/Stores/counter";

const counterStore = useCounterStore();

console.log(counterStore)
</script>

<template>
    <div>
        <h1>Home Page</h1>
        <p>Count: {{ counterStore.count }}</p>
        <p>Double Count: {{ counterStore.doubleCount }}</p>
        <button class="p-2 border border-gray-400 rounded-lg" @click="counterStore.increment">Increment</button>
        <button class="p-2 border border-gray-400 rounded-lg" @click="counterStore.decrement">Decrement</button>
    </div>
</template>

<style scoped></style>
