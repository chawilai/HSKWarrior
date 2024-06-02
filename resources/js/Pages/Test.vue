<script setup>
import { onMounted, ref } from "vue";
import SignaturePad from "@/Components/SignaturePad.vue";

</script>

<template>
        <SignaturePad
        :have-btn="false"
        canvas-class="bg-transparent rounded-lg border-2 border-gray-400"
        :min-width="0.5"
        :max-width="3.5"
        :dot-size="3"
        :width="100"
        :height="100"
        canvasId="pad1" />
</template>

<style scoped>
</style>
