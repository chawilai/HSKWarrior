<script setup>
import Main from "@/Layouts/Main.vue";
import { Head } from "@inertiajs/vue3";

// - intro with image hanzi -> scroll down to use the page
// - can set number of practice
// - select characters
//     -> pick one, list -> if one start drow
//     -> type -> add if list
//     -> select from the group level 1-6, common ... this list will show all letter with detail (pinyin, meaning, thai, english and adding button at the back) -> can save list if login
    // - setting
    //     - list name
    //     - user -> null -> will set if login and want share or print
    //     - number of practise box
    // - adding word
    //     - input for typing
    //         - if it is long word will split to one next row
    //     - search in category
    //         ** have search box that can search chinese, pinyin (use english letter), thai mean, english mean -> it will filter the list to add
    //         - HSK 1 174
    //         - HSK 2 173
    //         - HSK 3 270
    //         - HSK 4 447
    //         - HSK 5 621
    //         - HSK 6 978
    //         - Frequently Hanzi 875
    //         - Common Hanzi 3462
    //         - Traditional Hanzi 236
    // - the list will have
    //     - top title
    //         - list name
    //         - the user who create list
    //         - the created_at time
    //         - words count
    //         - empty box for practice count
    //     - table list
    //         - number
    //         - chinese character with animation drawing
    //             - add speaker icon to play sound
    //             - tag for category
    //         - pinyin
    //         - type of word -> adv, adj
    //         - thai translation, english translation (if select Eng version)
    //         - add button
    //         // - optional: example sentence with chinese, pinyin, translate -> paid plan
    //     - share list -> have to login
    //         - will save the list
    //             - hanzi_list
    //                 - id
    //                 - user_id
    //                 - list_reference
    //                 - box_number
    //                 - created_at
    //                 - updated_at
    //             - hanzi_list_words
    //                 - id
    //                 - hanzi_list_id
    //                 - hanzi_id
    //                 - created_at
    //                 - updated_at
    //             - hanzi
    //                 - id
    //                 - hanzi
    //                 - pinyin
    //                 - pinyin_eng
    //                 - thai_meaning
    //                 - english_meaning
    //                 // - created_at
    //                 // - updated_at

    //             - hanzi_categories
    //                 - id
    //                 - hanzi_id
    //                 - category_id
    //                 // - created_at
    //                 // - updated_at
    //             - category
    //                 - id
    //                 - title
    //                     - HSK 1 174
    //                     - HSK 2 173
    //                     - HSK 3 270
    //                     - HSK 4 447
    //                     - HSK 5 621
    //                     - HSK 6 978
    //                     - Frequently Hanzi 875
    //                     - Common Hanzi 3462
    //                     - Traditional Hanzi 236
    //                 // - created_at
    //                 // - updated_at
    //             - radical
    //                 - id
    //                 - radical
    //                     - radical character
    //                 - pinyin
    //                 - pinyin_eng
    //                 - thai_meaning
    //                 - english_meaning
    //                 - thai_description // history of the radical
    //                 - english_description // history of the radical
    //             - hanzi_radical
    //                 - id
    //                 - hanzi_id
    //                 - radical_id
    //             - hanzi_parts_of_speech
    //                 - id
    //                 - hanzi_id
    //                 - parts_of_speech_id
    //             - parts_of_speech
    //                 - id
    //                 - title
    //                     - Noun
    //                     - Pronoun
    //                     - Verb
    //                     - Adjective
    //                     - Adverb
    //                     - Preposition
    //                     - Conjunction
    //                     - Interjection
    //                     - Article
    //                 - Abbreviation
    //                     - n.
    //                     - pron.
    //                     - v.
    //                     - adj.
    //                     - adv.
    //                     - prep.
    //                     - conj.
    //                     - interj.
    //                     - art.
    //                 - meaning
    //                     - A word that names a person, place, thing, or idea.
    //                     - A word that takes the place of a noun.
    //                     - A word that expresses an action or state of being.
    //                     - A word that modifies a noun or pronoun.
    //                     - A word that modifies a verb, adjective, or another adverb.
    //                     - A word that shows the relationship between a noun or pronoun and other words in the sentence.
    //                     - A word that connects words, phrases, or clauses.
    //                     - A word that expresses strong emotion and is often followed by an exclamation point.
    //                     - A word used before a noun to indicate whether it refers to something specific (the) or something general (a, an). Sometimes included as a type of adjective.
    //                 - created_at
    //                 - updated_at

</script>

<template>
    <Head title="คัดจีน (Hànzì)" />

    <Main>

    </Main>
</template>
