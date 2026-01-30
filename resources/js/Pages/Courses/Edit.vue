<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// 1. Recebe as props do Laravel
const props = defineProps({
    course: {
        type: Object,
        required: true
    },
    lesson: {
        type: Object,
        required: true
    }
});

// 2. Inicializa o formulário COM os dados da prop
const form = useForm({
    title: props.lesson.title || '',
    video_url: props.lesson.video_url || '', // Assegura que não seja null
    position: props.lesson.position || 0,
});

// 3. Função de envio
const submit = () => {
    form.put(route('lessons.update', [props.course.id, props.lesson.id]), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Editar Aula" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Aula: {{ lesson.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div>
                                <InputLabel for="title" value="Título da Aula" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                    autofocus
                                    autocomplete="title"
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <InputLabel for="video_url" value="URL do YouTube" />
                                <TextInput
                                    id="video_url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    v-model="form.video_url"
                                    required
                                    placeholder="https://www.youtube.com/watch?v=..."
                                />
                                <InputError class="mt-2" :message="form.errors.video_url" />
                            </div>

                            <div>
                                <InputLabel for="position" value="Ordem (Posição)" />
                                <TextInput
                                    id="position"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.position"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.position" />
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('courses.show', course.id)"
                                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Cancelar
                                </Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Salvar Alterações
                                </PrimaryButton>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>