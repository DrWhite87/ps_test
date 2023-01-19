<script setup>
import MThead from "./MThead.vue";
import MTBody from "./MTBody.vue";
import MTFooter from "./MTFooter.vue";
import MTPagination from "./MTPagination.vue";
import {computed, useSlots} from 'vue';

defineProps({
    value: {
        type: Array,
        required: true,
        default: () => {
            return [];
        },
    },
    paginationLinks: {
        type: Array,
        required: false,
        default: () => {
            return [];
        },
    },
});

const slots = useSlots();

const columns = computed(() => {
    let result = [];
    console.log('slots.default', slots.default());
    if (slots.default) {
        slots.default().forEach(item => {
            result.push(item);
        })
    }

    return result;
});

</script>

<template>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <slot></slot>
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <MThead :columns="columns"/>
            <MTBody :columns="columns" :value="value"/>
            <MTFooter :columns="columns"/>
        </table>
    </div>
    <MTPagination class="mt-6" :links="paginationLinks" />
</template>


<style scoped>

</style>
