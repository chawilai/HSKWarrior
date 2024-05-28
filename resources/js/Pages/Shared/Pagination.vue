<template>
    <div class="flex justify-between gap-x-2">
        <Component
            v-for="link in links"
            :key="link.label"
            :is="link.url ? Link : 'span'"
            preserve-state
            class="border border-gray-2 py-1 px-3 rounded whitespace-nowrap"
            :class="{
                'bg-red text-white font-bold': link.label == page.props.page,
                'text-gray-400 cursor-not-allowed': !link.url,
            }"
            v-bind="link.url ? { href: link.url } : {}"
            v-html="link.label"
        />
    </div>
</template>

<script setup>
// import { ref, computed, watch } from 'vue'
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage()

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

</script>

<style scoped></style>
