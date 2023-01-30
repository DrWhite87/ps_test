<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Link, Head, useForm} from '@inertiajs/inertia-vue3';
import Modal from "@/Components/Modal.vue";
import MTable from "@/Components/UI/Table/MTable.vue";
import MTColumn from "@/Components/UI/Table/MTColumn.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {ref} from 'vue';

const props = defineProps({
    response: Object,
    allLessonsCount: Number,
})

const form = useForm({});
const selectedStudent = ref(null);

/* calculate percent of view lessons */
const getProgressPercent = (viewLessonsCount) => {
    return (viewLessonsCount * 100 / props.allLessonsCount).toFixed();
}

/* select selectedStudent and open modal */
const onClickName = (student) => {
    selectedStudent.value = student;
}

/* clear selectedStudent and close modal */
const onModalClose = () => {
    selectedStudent.value = null
}

/* destroy student */
const destroy = (student) => {
    if (confirm("Are you sure you want to Delete")) {
        form.delete(route('students.destroy', {id: student.id}), {preserveScroll: true});
    }
}
</script>

<template>
    <Head title="Students"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Students</h2>
                <Link class="ml-4" :href="route('students.create')">
                    <PrimaryButton>
                        Add
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <MTable
                    :value="response.data"
                    :paginationLinks="response.links"
                >
                    <MTColumn attribute="name" label="Name" :clickable="true">
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
                    <MTColumn attribute="position" label="Place" :sortable="true">
                        <template #body="{row}">
                            {{ row.position || '-' }} ({{ row.score }})
                        </template>
                    </MTColumn>
                    <MTColumn attribute="actions" label="Actions">
                        <template #body="{row}">
                            <Link :href="route('students.edit', {id: row.id})" class="mr-1">Edit</Link>
                            <a href @click.prevent="destroy(row)">Delete</a>
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
            <MTColumn attribute="name" label="Name"></MTColumn>
            <MTColumn attribute="description" label="Description"></MTColumn>
            <MTColumn attribute="pivot_progress" label="Progress" :sortable="true">
                <template #body="{row}">
                    {{ row.pivot_progress }}%
                </template>
            </MTColumn>
            <MTColumn attribute="pivot_score" label="Score" :sortable="true"></MTColumn>
        </MTable>
    </Modal>
</template>
