<script setup>
defineProps({
    columns: {
        type: Array,
        required: true,
        default: () => {
            return [];
        },
    },
    resources: {
        type: Array,
        required: true,
        default: () => {
            return [];
        },
    },
})
</script>

<template>
    <tbody>
    <tr
        v-for="(row, rowIndex) in resources"
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
    >
        <td class="px-6 py-4" v-for="(column, columnIndex) in columns" @click="$emit('clickRow', {row, event: $event})">
            <component v-if="column.children && column.children.body" :is="column.children.body" :row="row" :column="column" :attribute="column.props.attribute" :label="column.props.label"/>
            <template v-else>
                {{ row[column.props.attribute] }}
            </template>
        </td>
    </tr>
    </tbody>
</template>

<style scoped>

</style>
