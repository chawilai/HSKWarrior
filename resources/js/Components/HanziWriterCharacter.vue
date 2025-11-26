<script setup>
import { onMounted, ref, watch, onBeforeUnmount } from "vue";
import HanziWriter from "hanzi-writer";

const props = defineProps({
    character: {
        type: String,
        required: true,
    },
    size: {
        type: Number,
        default: 60,
    },
    id: {
        type: String,
        required: true,
    },
});

const writerInstance = ref(null);
const containerId = `hanzi-writer-${props.id}-${Math.random()
    .toString(36)
    .substr(2, 9)}`;

const initWriter = () => {
    // Clean up existing instance if any
    if (writerInstance.value) {
        // HanziWriter doesn't have a destroy method in v3, but we can clear the container
        const container = document.getElementById(containerId);
        if (container) container.innerHTML = "";
    }

    writerInstance.value = HanziWriter.create(containerId, props.character, {
        width: props.size,
        height: props.size,
        padding: 2,
        strokeColor: "#333333",
        radicalColor: "#166534", // green-800 for radical
        showOutline: true,
        strokeAnimationSpeed: 1.5,
        delayBetweenStrokes: 50,
        charDataLoader: (char, onComplete) => {
            // Use local data or CDN
            fetch(
                `https://cdn.jsdelivr.net/npm/hanzi-writer-data@2.0/${char}.json`
            )
                .then((res) => res.json())
                .then(onComplete)
                .catch(() => {
                    // Fallback or error handling
                    console.error(`Failed to load data for ${char}`);
                });
        },
    });
};

const animate = () => {
    if (writerInstance.value) {
        writerInstance.value.animateCharacter();
    }
};

onMounted(() => {
    initWriter();
});

watch(
    () => props.character,
    () => {
        initWriter();
    }
);

defineExpose({
    animate,
});
</script>

<template>
    <div
        :id="containerId"
        class="inline-block cursor-pointer hover:scale-110 transition-transform"
        @click="animate"
    ></div>
</template>
