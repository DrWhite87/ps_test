<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/inertia-vue3';
import Modal from "@/Components/Modal.vue";
import MTable from "@/Components/UI/Table/MTable.vue";
import MTColumn from "@/Components/UI/Table/MTColumn.vue";
import {ref} from 'vue';

const props = defineProps({
    response: Object,
    allLessonsCount: Number,
})

const selectedStudent = ref(null);

const getProgressPercent = (viewLessonsCount) => {
    return (viewLessonsCount * 100 / props.allLessonsCount).toFixed();
}

const onClickName = (student) => {
    // fetchStudentLessons(`/api/students/${student.id}/lessons?viewOnly=1`);
    selectedStudent.value = student;
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
                    :value="response.data"
                    :paginationLinks="response.links"
                >
                    <MTColumn attribute="name" label="ФИО" :clickable="true">
                        <template #body="{row, column}">
                            <a href @click.prevent.stop="onClickName(row)">{{ row.name }}</a>
                        </template>
                    </MTColumn>
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

    <Modal :show="!!selectedStudent" @close="onModalClose">
        <MTable
            :resourceUrl="`/api/students/${selectedStudent.id}/lessons?viewOnly=1`"
            :isInModal="true"
        >
            <MTColumn attribute="name" label="Название"></MTColumn>
            <MTColumn attribute="description" label="Описание"></MTColumn>
            <MTColumn attribute="pivot_progress" label="Прогресс" :sortable="true">
                <template #body="{row}">
                    {{ row.pivot_progress }}%
                </template>
            </MTColumn>
            <MTColumn attribute="pivot_score" label="Баллы" :sortable="true"></MTColumn>
        </MTable>
    </Modal>
</template>
