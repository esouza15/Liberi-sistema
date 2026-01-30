<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue'; // Importado
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3'; // router importado

const props = defineProps({
    course: { type: Object, required: true },
    lesson: { type: Object, required: true }
});

const form = useForm({
    title: props.lesson.title || '',
    video_url: props.lesson.video_url || '',
    position: props.lesson.position || 0,
});

const submit = () => {
    form.put(route('lessons.update', [props.course.id, props.lesson.id]));
};

// Função de Excluir Aula
const destroy = () => {
    if (confirm('Tem certeza que deseja excluir esta aula? Não há como desfazer.')) {
        router.delete(route('lessons.destroy', [props.course.id, props.lesson.id]));
    }
};
</script>

<template>
    <Head title="Editar Aula" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800">
                Editar Aula: {{ lesson.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div>
                            <InputLabel for="title" value="Título da Aula" />
                            <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div>
                            <InputLabel for="video_url" value="URL do YouTube" />
                            <TextInput id="video_url" v-model="form.video_url" type="url" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.video_url" />
                        </div>

                        <div>
                            <InputLabel for="position" value="Ordem" />
                            <TextInput id="position" v-model="form.position" type="number" class="mt-1 block w-full" required />
                        </div>

                        <div class="flex items-center justify-between mt-8 pt-6 border-t">
                            <DangerButton type="button" @click="destroy">
                                Excluir Aula
                            </DangerButton>

                            <div class="flex items-center gap-4">
                                <Link :href="route('courses.show', course.id)" class="text-gray-600 underline text-sm">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Salvar Alterações
                                </PrimaryButton>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>