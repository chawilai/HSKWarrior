<script setup>
import Layout from "@/Layouts/OrganicLayout.vue";
import { Head, router, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import { debounce } from "lodash";
import Pagination from "@/Pages/Shared/Pagination.vue";

defineOptions({ layout: Layout });

// const props = defineProps({
//     filters: Object,
//     hanzi_list: Object,
// });

const pages = usePage();

let hanzi_list = pages.props.hanzi_list;

let search = ref(pages.props.filters.search);

const fetchData = () => {
    router.get(
        "/warrior_writehanzi",
        { search: search.value },
        {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                console.log('enter')
                console.log(page.props)
                    hanzi_list = pages.props.hanzi_list;
                    search.value = pages.props.filters.search;

                console.log(hanzi_list)
            },
        }
    );
};

watch(
    search,
    debounce((value) => {
        console.log(value);
        router.get(
            "/warrior_writehanzi",
            { search: value },
            {
                preserveState: true,
                replace: true,
                onSuccess: (page) => {
                    console.log('watch')
                    // console.log(page.props)
                    hanzi_list = pages.props.hanzi_list;
                    search.value = pages.props.filters.search;

                    console.log(hanzi_list)
                },
            }
        );
    }, 500)
);

watch(() => pages.props,
    (data) => hanzi_list = data.hanzi_list
);

onMounted(() => {
    console.log('mounted')
})
</script>

<template>
    <Head title="Home" />
    <div class="flex flex-col gap-y-2 w-full -mt-10">
        <div class="flex justify-end gap-x-2">
            <div class="flex flex-col justify-center">
                <span class="text-xl font-bold">ค้นหา</span>
                <span class="text-based">(Hanzi, Pinyin, Meaning)</span>
            </div>
            <input
                type="text"
                name="search"
                v-model="search"
                @keydown.enter="fetchData()"
                class="border border-gray-300 px-2 rounded-lg text-center w-52"
                autocomplete="off"
            />
        </div>
        <div class="relative z-10">
            <table class="w-full border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th
                            class="w-24 bg-red text-white border border-gray-200 py-1 px-2 text-center"
                        >
                            id
                        </th>
                        <th
                            class="w-24 bg-red text-white border border-gray-200 py-1 px-2 text-center"
                        >
                            set
                        </th>
                        <th
                            class="w-24 bg-red text-white border border-gray-200 py-1 px-2 text-center"
                        >
                            hanzi
                        </th>
                        <th
                            class="w-24 bg-red text-white border border-gray-200 py-1 px-2 text-center"
                        >
                            pinyin
                        </th>
                        <th
                            class="bg-red text-white border border-gray-200 py-1 px-2 text-center"
                        >
                            meaning
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(hanzi, index) in hanzi_list.data"
                        :key="hanzi.id"
                    >
                        <td
                            class="w-24 border border-gray-200 py-1 px-2 text-center"
                            v-text="hanzi_list.from + index"
                        ></td>
                        <td
                            class="w-24 border border-gray-200 py-1 px-2 text-center"
                            v-text="hanzi.set"
                        ></td>
                        <td
                            class="w-24 border border-gray-200 py-1 px-2 text-center"
                            v-text="hanzi.character"
                        ></td>
                        <td
                            class="w-24 border border-gray-200 py-1 px-2 text-center"
                            v-text="hanzi.pinyin"
                        ></td>
                        <td
                            class="border border-gray-200 py-1 px-2"
                            v-text="hanzi.definition"
                        ></td>
                    </tr>
                </tbody>
            </table>

            <!-- pagination -->
            <Pagination class="m-3 w-fit" :links="hanzi_list.links" />
        </div>
    </div>
</template>
