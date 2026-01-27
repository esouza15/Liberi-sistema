<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// Recebe o curso (com as aulas dentro) vindo do Controller
const props = defineProps({
    course: Object
});

// Formulário para nova aula
const form = useForm({
    title: '',
    video_url: '',
    position: props.course.lessons.length + 1 // Sugere a próxima posição
});

const submit = () => {
    form.post(route('courses.lessons.store', props.course.id), {
        onSuccess: () => form.reset(), // Limpa o form se der certo
    });
};
</script>

<template>
    <Head :title="course.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ course.title }}
                </h2>
                <Link :href="route('courses.index')" class="text-gray-500 hover:text-gray-700 text-sm">
                    ← Voltar para Cursos
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <p class="text-gray-600">{{ course.description }}</p>
                </div>

                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="text-lg font-bold mb-4">Grade de Aulas</h3>
                    
                    <div v-if="course.lessons.length === 0" class="text-gray-400 italic">
                        Nenhuma aula cadastrada ainda.
                    </div>

                    <ul v-else class="space-y-3">
                        <li v-for="lesson in course.lessons" :key="lesson.id" class="flex items-center justify-between bg-gray-50 p-3 rounded border">
                            <div class="flex items-center">
                                <span class="bg-indigo-100 text-indigo-800 font-bold py-1 px-2 rounded text-xs mr-3">
                                    Aula {{ lesson.position }}
                                </span>
                                <span class="text-gray-700">{{ lesson.title }}</span>
                            </div>
                            <a :href="lesson.video_url" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                Ver Vídeo ↗
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="bg-white p-6 shadow sm:rounded-lg border-l-4 border-indigo-500">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Adicionar Nova Aula</h3>
                    
                    <form @submit.prevent="submit" class="flex gap-4 items-end">
                        <div class="flex-grow">
                            <label class="block text-gray-700 text-sm font-bold mb-1">Título da Aula</label>
                            <input v-model="form.title" type="text" class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: Introdução ao Módulo">
                        </div>
                        
                        <div class="flex-grow">
                            <label class="block text-gray-700 text-sm font-bold mb-1">Link do Vídeo</label>
                            <input v-model="form.video_url" type="text" class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="https://youtube.com/...">
                        </div>

                        <div class="w-24">
                             <label class="block text-gray-700 text-sm font-bold mb-1">Ordem</label>
                             <input v-model="form.position" type="number" class="w-full border-gray-300 rounded shadow-sm">
                        </div>

                        <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700" :disabled="form.processing">
                            Salvar
                        </button>
                    </form>
                    <div v-if="form.errors.video_url" class="text-red-500 text-xs mt-2">{{ form.errors.video_url }}</div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>