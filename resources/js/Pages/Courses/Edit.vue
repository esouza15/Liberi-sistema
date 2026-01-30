<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue'; // Botão Vermelho
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    course: { type: Object, required: true }
});

const form = useForm({
    title: props.course.title || '',
    description: props.course.description || '',
    price: props.course.price || '',
});

const submit = () => {
    form.put(route('courses.update', props.course.id));
};

// Função de Excluir Curso
const destroy = () => {
    if (confirm('ATENÇÃO: Excluir o curso apagará todas as aulas e removerá o acesso dos alunos. Tem certeza?')) {
        router.delete(route('courses.destroy', props.course.id));
    }
};
</script>

<template>
    <Head title="Editar Curso" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800">
                Editar Curso: {{ course.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div>
                                <InputLabel for="title" value="Título do Curso" />
                                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Descrição" />
                                <textarea id="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" v-model="form.description" rows="4"></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <InputLabel for="price" value="Preço (R$)" />
                                <TextInput id="price" type="number" step="0.01" class="mt-1 block w-full" v-model="form.price" required />
                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>

                            <div class="flex items-center justify-between mt-8 pt-6 border-t">
                                <DangerButton type="button" @click="destroy">
                                    Excluir Curso
                                </DangerButton>

                                <div class="flex items-center gap-4">
                                    <Link :href="route('courses.index')" class="text-gray-600 underline text-sm">Cancelar</Link>
                                    <PrimaryButton :disabled="form.processing">Salvar Curso</PrimaryButton>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>