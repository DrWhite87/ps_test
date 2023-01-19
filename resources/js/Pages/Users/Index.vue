<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/inertia-vue3';
import Sort from "@/Components/Sort.vue";
import Modal from "@/Components/Modal.vue";
import MTable from "@/Components/UI/Table/MTable.vue";
import MTColumn from "@/Components/UI/Table/MTColumn.vue";
import {ref} from 'vue';

const props = defineProps({
    students: Array,
    allLessonsCount: Number,
})

console.log('props', props);

const studentLessons = ref(null);

const getProgressPercent = (viewLessonsCount) => {
    return (viewLessonsCount * 100 / props.allLessonsCount).toFixed();
}

const onModalClose = () => {
    studentLessons.value = null
}


const uploadData = async (student) => {
    studentLessons.value = await (await fetch(`/api/students/${student.id}/lessons?viewOnly=1`)).json();
}

</script>

<template>
    <Head title="Студенты"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Студенты</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <MTable
                    :value="students.data"
                    :paginationLinks="students.links"
                >
                    <MTColumn attribute="name" label="ФИО"></MTColumn>
                    <MTColumn attribute="email" label="Email"></MTColumn>
                    <MTColumn attribute="view_lessons_count" label="Прогресс" :sortable="true">
                        <template #body="{row}">
                            {{ getProgressPercent(row.view_lessons_count) }}%
                        </template>
                    </MTColumn>
                    <MTColumn attribute="position" label="Место в рейтинге" :sortable="true">
                        <template #body="{row}">
                            {{ row.position || '-' }} ({{ row.score }})
                        </template>
                    </MTColumn>
                </MTable>
            </div>
        </div>
    </AuthenticatedLayout>

    <Modal :show="!!studentLessons" @close="onModalClose">

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Название
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Описание
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Прогресс
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Баллы
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="lesson in studentLessons"
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                >
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ lesson.name }}
                    </th>
                    <td class="px-6 py-4">
                        {{ lesson.description }}
                    </td>
                    <td class="px-6 py-4">
                        {{ lesson.pivot.progress }}%
                    </td>
                    <td class="px-6 py-4">
                        {{ lesson.pivot.score }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </Modal>
</template>
