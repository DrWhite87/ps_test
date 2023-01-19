<script setup>

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
    <tfoot class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
    <tr>
        <th
            v-for="(column, index) in columns"
            :key="'tfoot-' + column.props.name"
            scope="col"
            class="px-6 py-3"
        ></th>
    </tr>
    </tfoot>
</template>



<style scoped>

</style>
