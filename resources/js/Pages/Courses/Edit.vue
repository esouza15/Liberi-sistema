<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object
});

const form = useForm({
    title: props.lesson.title,
    video_url: props.lesson.video_url,
    position: props.lesson.position
});

const submit = () => {
    form.put(route('lessons.update', [props.course.id, props.lesson.id]));
};
</script>

<template>
    <Head title="Editar Aula" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800">Editar Aula: {{ lesson.title }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit">
                        
                        <div class="mb-4">
                            <InputLabel for="title" value="Título da Aula" />
                            <TextInput id="title" v-model="form.title" type="text" class="block w-full mt-1" required />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="video_url" value="URL do YouTube" />
                            <TextInput id="video_url" v-model="form.video_url" type="url" class="block w-full mt-1" required />
                            <InputError class="mt-2" :message="form.errors.video_url" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="position" value="Ordem (Posição)" />
                            <TextInput id="position" v-model="form.position" type="number" class="block w-full mt-1" required />
                        </div>

                        <div class="flex justify-end gap-4 mt-6">
                            <Link :href="route('courses.show', course.id)" class="text-gray-600 underline self-center">Cancelar</Link>
                            <PrimaryButton :disabled="form.processing">Salvar Alterações</PrimaryButton>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>