```
<script setup>
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
import { Head, router, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, watch, onMounted, onBeforeUnmount } from "vue";
import { debounce } from "lodash";
import Pagination from "@/Pages/Shared/Pagination.vue";
import tts2 from "@/azure_tts.js";
import HanziWriterCharacter from "@/Components/HanziWriterCharacter.vue";

import { usePlaySound } from "@/composables/usePlaySound.js";

defineOptions({ layout: OrganicLayout });

const { playSound, stopPlayback, playingWord } = usePlaySound();

const hanziRefs = ref({});

const setHanziRef = (el, id) => {
    if (el) {
        hanziRefs.value[id] = el;
    }
};

const playWordWithAnimation = (hanzi) => {
    playSound(hanzi.character);
    
    const charComp = hanziRefs.value[hanzi.id];
    if (charComp && charComp.animate) {
        charComp.animate();
    }
};

const pages = usePage();
// ... (rest of script)

let hanzi_list = pages.props.hanzi_list;
let hanzi_list_arr = pages.props.hanzi_list_arr;

let search = ref(pages.props.filters.search);
let s_set = ref("hsk1");
let s_hanzi = ref(pages.props.filters.s_hanzi);
let s_pinyin = ref(pages.props.filters.s_pinyin);
let s_mean = ref(pages.props.filters.s_mean);

const fetchData = () => {
    router.get(
        "/warrior_writehanzi",
        {
            s_set: s_set.value ?? null,
            s_hanzi: s_hanzi.value ?? null,
            s_pinyin: s_pinyin.value ?? null,
            s_mean: s_mean.value ?? null,
            search: search.value ?? null,
        },
        {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                hanzi_list = pages.props.hanzi_list;
                search.value = pages.props.filters.search;
                s_set.value = pages.props.filters.s_set;
                s_hanzi.value = pages.props.filters.s_hanzi;
                s_pinyin.value = pages.props.filters.s_pinyin;
                s_mean.value = pages.props.filters.s_mean;
            },
        }
    );
};

let addToHanziList = (input, list_name = null) => {
    let found = false;

    hanzi_list_arr.forEach((list) => {
        if (list.hanzi.includes(input)) {
            list.hanzi = list.hanzi.filter((item) => item !== input);
            found = true;
        }
    });

    // If input was not found in any list, add it to the specified list or default list
    if (!found) {
        if (list_name) {
            // Find the list by name
            let list = hanzi_list_arr.find((list) => list.name === list_name);
            if (list) {
                list.hanzi.push(input);
            } else {
                console.log("List not found.");
            }
        } else {
            // Add input to the default list
            if (hanzi_list_arr.length == 0) {
                hanzi_list_arr.push({
                    name: "my_list",
                    reference: makeid(6),
                    hanzi: [input],
                });
            } else {
                hanzi_list_arr[0].hanzi.push(input);
            }
        }
    }

    console.log(hanzi_list_arr);
};

let makeid = (
    length = 4,
    letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"
) => {
    var result = [];
    var characters = letters;
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result.push(
            characters.charAt(Math.floor(Math.random() * charactersLength))
        );
    }
    return result.join("");
};

let saveHanziList = () => {
    let list_name = hanzi_list_arr[0].name;
    let list_reference = hanzi_list_arr[0].reference;
    let list = hanzi_list_arr[0].hanzi;

    router.post("/save_hanzi_list", { list_name, list_reference, list });
};

// watch(search, debounce((value) => fetchData(), 500));
// watch(s_set, debounce((value) => fetchData(), 500));
watch(
    s_hanzi,
    debounce((value) => fetchData(), 500)
);
watch(
    s_pinyin,
    debounce((value) => fetchData(), 500)
);
watch(
    s_mean,
    debounce((value) => fetchData(), 500)
);
watch(
    () => pages.props,
    (data) => (hanzi_list = data.hanzi_list)
);

onMounted(() => {
    fetchData();
});

onBeforeUnmount(() => {
    stopPlayback();
});
</script>

<template>
    <Head>
        <title>Hànzì</title>
    </Head>

    <div class="container mx-auto px-4 py-8 min-h-screen">
            <!-- Header & Filter Section -->
            <div class="flex flex-col gap-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">ฝึกเขียนอักษรจีน</h1>
                        <p class="text-gray-500 mt-1">เลือกตัวอักษรที่ต้องการฝึกเขียนแล้วบันทึกลงรายการ</p>
                    </div>
                    
                    <!-- Basket / My List Button -->
                    <div 
                        class="relative group cursor-pointer"
                        @click="saveHanziList()"
                    >
                        <button class="btn btn-primary btn-lg gap-3 shadow-lg hover:scale-105 transition-transform">
                            <i class="pi pi-inbox text-xl"></i>
                            <span>รายการที่เลือก</span>
                            <div class="badge badge-white text-primary font-bold border-none">
                                {{ hanzi_list_arr[0] ? hanzi_list_arr[0].hanzi.length : 0 }}
                            </div>
                        </button>
                        <div class="absolute top-full right-0 mt-2 w-64 p-2 bg-white rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 text-sm text-gray-600 border border-gray-100">
                            คลิกเพื่อบันทึกและเริ่มฝึกเขียน
                        </div>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- HSK Level -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-bold text-gray-600">ระดับ (HSK)</span>
                            </label>
                            <select 
                                class="select select-bordered w-full focus:border-primary focus:ring-2 focus:ring-primary/20" 
                                v-model="s_set"
                                @change="fetchData()"
                            >
                                <option value="">ทั้งหมด</option>
                                <option value="hsk1">HSK 1</option>
                                <option value="hsk2">HSK 2</option>
                                <option value="hsk3">HSK 3</option>
                                <option value="hsk4">HSK 4</option>
                                <option value="hsk5">HSK 5</option>
                                <option value="hsk6">HSK 6</option>
                                <option value="hsk7">HSK 7</option>
                                <option value="hsk8">HSK 8</option>
                            </select>
                        </div>

                        <!-- Hanzi Filter -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-bold text-gray-600">ตัวอักษร (Hanzi)</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    placeholder="ค้นหาตัวอักษร..." 
                                    class="input input-bordered w-full pl-10 focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    v-model="s_hanzi"
                                />
                                <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Pinyin Filter -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-bold text-gray-600">พินอิน (Pinyin)</span>
                            </label>
                            <input 
                                type="text" 
                                placeholder="ค้นหาพินอิน..." 
                                class="input input-bordered w-full focus:border-primary focus:ring-2 focus:ring-primary/20"
                                v-model="s_pinyin"
                            />
                        </div>

                        <!-- Meaning Filter -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-bold text-gray-600">ความหมาย</span>
                            </label>
                            <input 
                                type="text" 
                                placeholder="ค้นหาความหมาย..." 
                                class="input input-bordered w-full focus:border-primary focus:ring-2 focus:ring-primary/20"
                                v-model="s_mean"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Grid -->
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-3 mb-8">
                <div 
                    v-for="(hanzi, index) in hanzi_list.data" 
                    :key="hanzi.id"
                    class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group"
                >
                    <div class="card-body p-2 items-center text-center relative">
                        <!-- Selection Toggle (Top Right) -->
                        <button 
                            class="absolute top-1 right-1 btn btn-circle btn-xs transition-colors duration-200"
                            :class="hanzi_list_arr[0] && hanzi_list_arr[0].hanzi.includes(hanzi.character) 
                                ? 'btn-primary text-white' 
                                : 'btn-ghost text-gray-400 hover:bg-red-600 hover:text-white hover:border-red-600'"
                            @click="addToHanziList(hanzi.character)"
                            :title="hanzi_list_arr[0] && hanzi_list_arr[0].hanzi.includes(hanzi.character) ? 'ลบออกจากรายการ' : 'เพิ่มลงรายการ'"
                        >
                            <i class="pi text-[10px]" :class="hanzi_list_arr[0] && hanzi_list_arr[0].hanzi.includes(hanzi.character) ? 'pi-check' : 'pi-plus'"></i>
                        </button>

                        <!-- HSK Badge (Top Left) -->
                        <div class="absolute top-1 left-1 badge badge-ghost badge-xs text-[10px] font-mono opacity-50 px-1">
                            {{ hanzi.set }}
                        </div>

                        <!-- Character -->
                        <div 
                            class="mt-2 mb-1 cursor-pointer hover:scale-110 transition-transform"
                            @click="playWordWithAnimation(hanzi)"
                        >
                            <HanziWriterCharacter 
                                :ref="(el) => setHanziRef(el, hanzi.id)"
                                :id="`hanzi-${hanzi.id}`"
                                :character="hanzi.character"
                                :size="45"
                                :strokeColor="'#1f2937'"
                                :radicalColor="'#B71F1F'"
                            />
                        </div>

                        <!-- Pinyin -->
                        <div class="text-sm font-bold text-primary font-serif italic mb-0.5">
                            {{ hanzi.pinyin }}
                        </div>

                        <!-- Meaning -->
                        <div class="text-[10px] text-gray-600 line-clamp-2 h-8 flex items-center justify-center leading-tight px-1">
                            {{ hanzi.definition }}
                        </div>

                        <!-- Audio Button (Bottom) -->
                        <button 
                            class="btn btn-circle btn-ghost btn-xs text-gray-400 hover:text-primary mt-1"
                            @click="playWordWithAnimation(hanzi)"
                        >
                            <i class="pi pi-volume-up text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center pb-12">
                <Pagination :links="hanzi_list.links" />
            </div>
        </div>
</template>
