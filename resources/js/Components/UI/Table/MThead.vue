<script setup>
import MTHeadSort from "./MTHeadSort.vue";

defineProps({
    columns: {
        type: Array,
        required: true,
        default: () => {
            return [];
        },
    },
})
</script>

<template>
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
    <tr>
        <th
            v-for="(column, index) in columns"
            :key="'thead-' + column.props.name"
            scope="col"
            class="px-6 py-3"
        >
            <template v-if="!column.props.sortable">{{ column.props.label }}</template>
            <MTHeadSort v-else :label="column.props.label" :attribute="column.props.attribute"/>
        </th>
    </tr>
    </thead>
</template>



<style scoped>

</style>
