<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/inertia-vue3';
import Modal from "@/Components/Modal.vue";
import MTable from "@/Components/UI/Table/MTable.vue";
import MTColumn from "@/Components/UI/Table/MTColumn.vue";
import {ref} from "vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    response: Object,
})

const selectedLesson = ref(null);

const onModalClose = () => {
    selectedLesson.value = null
}

const onClickName = (lesson) => {
    selectedLesson.value = lesson;
}

const destroy = (lesson) => {
    if (confirm("Are you sure you want to Delete")) {
        Inertia.delete(route('lessons.destroy', {id: lesson.id}), {
            onSuccess: (data) => {
                // Inertia.reload();
            }
        });
    }
}
</script>

<template>
    <Head title="Студенты"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Уроки</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <MTable
                    :value="response.data"
                    :paginationLinks="response.links"
                >
                    <MTColumn attribute="name" label="Название" :clickable="true">
                        <template #body="{row, column}">
                            <a href @click.prevent.stop="onClickName(row)">{{ row.name }}</a>
                        </template>
                    </MTColumn>
                    <MTColumn attribute="description" label="Описание"></MTColumn>
                    <MTColumn attribute="view_users_count" label="Просмотров" :sortable="true"></MTColumn>
                    <MTColumn attribute="actions" label="Дествия">
                        <template #body="{row}">
                            <Link :href="route('lessons.edit', {id: row.id})" class="mr-1">Ред.</Link>
                            <a href @click.prevent="destroy(row)">Уд.</a>
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
            <MTColumn attribute="name" label="Название"></MTColumn>
            <MTColumn attribute="email" label="Email"></MTColumn>
            <MTColumn attribute="pivot_progress" label="Прогресс" :sortable="true">
                <template #body="{row}">
                    {{ row.pivot_progress }}%
                </template>
            </MTColumn>
            <MTColumn attribute="pivot_score" label="Баллы" :sortable="true"></MTColumn>
        </MTable>
    </Modal>
</template>
