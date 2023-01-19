<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/inertia-vue3';
import Sort from "@/Components/Sort.vue";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";

const props = defineProps({
    lessons: Array
})

const lessonStudents = ref(null);

const onModalClose = () => {
    console.log('onModalClose', onModalClose);
    lessonStudents.value = null
}


const uploadData = async (student) => {
    lessonStudents.value = await (await fetch(`/api/lessons/${student.id}/students?viewOnly=1`)).json();
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
                                <Sort label="Просмотров" attribute="view_users_count"/>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="lesson in lessons"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                        >
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <a href @click.prevent="uploadData(lesson)">{{ lesson.name }}</a>
                            </th>
                            <td class="px-6 py-4">
                                {{ lesson.description }}
                            </td>
                            <td class="px-6 py-4">
                                {{ lesson.view_users_count }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <Modal :show="!!lessonStudents" @close="onModalClose">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        ФИО
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Email
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
                    v-for="student in lessonStudents"
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                >
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ student.name }}
                    </th>
                    <td class="px-6 py-4">
                        {{ student.email }}
                    </td>
                    <td class="px-6 py-4">
                        {{ student.pivot.progress }}%
                    </td>
                    <td class="px-6 py-4">
                        {{ student.pivot.score }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </Modal>
</template>
