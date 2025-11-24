```vue
<script setup>
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeUnmount } from "vue";
import tts2 from "@/azure_tts.js";

import { usePlaySound } from "@/composables/usePlaySound.js";

// defineOptions({ layout: Layout });

const pages = usePage();

let words_list = pages.props.words_list;
const { playSound, stopPlayback, playingWord } = usePlaySound();

onBeforeUnmount(() => {
    stopPlayback();
});
</script>

<template>
    <OrganicLayout>
        <Head title="คัดศัพท์จีน" />

        <div class="flex justify-center items-center">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                  <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Word</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Pinyin</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Meaning</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Example</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Example Pinyin</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Example Meaning</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50" v-for="word in words_list" :key="word.id">
                        <td class="px-4 py-3 text-sm text-gray-800" v-html="word.id"></td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                            {{ word.word }}
                            <i
                            class="pi pi-volume-up text-md text-gray-500 cursor-pointer hover:text-red hover:font-bold hover:scale-125"
                            @click="playSound(word.word)"></i>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800" v-html="word.pinyin"></td>
                        <td class="px-4 py-3 text-sm text-gray-800" v-html="word.meaning_thai"></td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                        {{ word.example }}
                        <i
                        class="pi pi-volume-up text-md text-gray-500 cursor-pointer hover:text-red hover:font-bold hover:scale-125"
                        @click="playSound(word.example)"></i>
                    </td>
                        <td class="px-4 py-3 text-sm text-gray-800" v-html="word.example_pinyin"></td>
                        <td class="px-4 py-3 text-sm text-gray-800" v-html="word.example_thai"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div>

    </OrganicLayout>

    <!-- <NotFound /> -->
</template>
