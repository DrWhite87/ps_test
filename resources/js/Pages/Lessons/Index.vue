<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Link, Head, useForm} from '@inertiajs/inertia-vue3';
import Modal from "@/Components/Modal.vue";
import MTable from "@/Components/UI/Table/MTable.vue";
import MTColumn from "@/Components/UI/Table/MTColumn.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {ref} from "vue";

const props = defineProps({
    response: Object,
})

const form = useForm({});
const selectedLesson = ref(null);

/* select selectedLesson and open modal */
const onClickName = (lesson) => {
    selectedLesson.value = lesson;
}

/* clear selectedLesson and close modal */
const onModalClose = () => {
    selectedLesson.value = null
}

/* destroy lesson */
const destroy = (lesson) => {
    if (confirm("Are you sure you want to Delete")) {
        form.delete(route('lessons.destroy', {id: lesson.id}), {preserveScroll: true});
    }
}
</script>

<template>
    <Head title="Lessons"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lessons</h2>
                <Link class="ml-4" :href="route('lessons.create')">
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
                    <MTColumn attribute="description" label="Description"></MTColumn>
                    <MTColumn attribute="duration" label="Duration" :sortable="true"></MTColumn>
                    <MTColumn attribute="view_users_count" label="Views" :sortable="true"></MTColumn>
                    <MTColumn attribute="actions" label="Actions">
                        <template #body="{row}">
                            <Link :href="route('lessons.edit', {id: row.id})" class="mr-1">Edit</Link>
                            <a href @click.prevent="destroy(row)">Delete</a>
                        </template>
                    </MTColumn>
                </MTable>
            </div>
        </div>
    </AuthenticatedLayout>

    <Modal :show="!!selectedLesson" @close="onModalClose">
        <MTable
            :resourceUrl="`/api/lessons/${selectedLesson.id}/students?viewOnly=1`"
            :isInModal="true"
        >
            <MTColumn attribute="name" label="Name"></MTColumn>
            <MTColumn attribute="email" label="Email"></MTColumn>
            <MTColumn attribute="pivot_progress" label="Progress" :sortable="true">
                <template #body="{row}">
                    {{ row.pivot_progress }}%
                </template>
            </MTColumn>
            <MTColumn attribute="pivot_score" label="Score" :sortable="true"></MTColumn>
        </MTable>
    </Modal>
</template>
