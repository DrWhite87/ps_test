<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/inertia-vue3';
import {reactive} from 'vue'
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from "@/Components/TextAreaInput.vue";

const props = defineProps({
    lesson: Object,
});

const form = useForm({
    name: props.lesson.name,
    description: props.lesson.description,
    duration: props.lesson.duration,
})

function submit() {
    form.patch(route('lessons.update', {id: props.lesson.id}))
}
</script>

<template>
    <Head title="Edit student"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit student</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Name" />

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    autofocus
                                />

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>
                            <div class="mt-4">
                                <InputLabel for="description" value="Description"/>

                                <TextAreaInput
                                    id="description"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                />

                                <InputError class="mt-2" :message="form.errors.description"/>
                            </div>
                            <div class="mt-4">
                                <InputLabel for="duration" value="Duration"/>

                                <TextInput
                                    id="duration"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.duration"
                                />

                                <InputError class="mt-2" :message="form.errors.duration"/>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Save
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
