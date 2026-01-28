<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    currentLesson: Object,
    isCompleted: Boolean,
    prevLesson: Object,
    nextLesson: Object
});

// Formulário simples para o botão de concluir
const form = useForm({});

const toggleComplete = () => {
    form.post(route('lessons.complete', props.currentLesson.id), {
        preserveScroll: true // Não rola a tela para o topo
    });
};
</script>

<template>
    <Head :title="currentLesson.title" />

    <AuthenticatedLayout>
        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                
                <div class="lg:w-2/3">
                    <div class="bg-black rounded-lg overflow-hidden shadow-xl aspect-video">
                        <iframe class="w-full h-full" :src="currentLesson.embed_url" frameborder="0" allowfullscreen></iframe>
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        
                        <div v-if="prevLesson">
                            <Link :href="route('lessons.show', [course.id, prevLesson.id])" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded inline-flex items-center transition">
                                ← Anterior
                            </Link>
                        </div>
                        <div v-else></div> <button 
                            @click="toggleComplete" 
                            class="font-bold py-2 px-6 rounded shadow transition flex items-center gap-2"
                            :class="isCompleted ? 'bg-green-100 text-green-700 border border-green-500' : 'bg-indigo-600 text-white hover:bg-indigo-700'"
                            :disabled="form.processing"
                        >
                            <span v-if="isCompleted">✓ Concluída</span>
                            <span v-else>Marcar como Concluída</span>
                        </button>

                        <div v-if="nextLesson">
                            <Link :href="route('lessons.show', [course.id, nextLesson.id])" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded inline-flex items-center transition">
                                Próxima →
                            </Link>
                        </div>
                        <div v-else></div>

                    </div>

                    <div class="mt-6 bg-white p-6 rounded-lg shadow">
                        <h1 class="text-2xl font-bold text-gray-900">{{ currentLesson.title }}</h1>
                        <p class="text-gray-600 mt-2">Aula {{ currentLesson.position }} do curso {{ course.title }}</p>
                    </div>
                </div>

                <div class="lg:w-1/3">
                    <div class="bg-white rounded-lg shadow p-4 h-fit max-h-screen overflow-y-auto">
                        <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Conteúdo do Curso</h3>
                        <ul class="space-y-2">
                            <li v-for="lesson in course.lessons" :key="lesson.id">
                                <Link 
                                    :href="route('lessons.show', [course.id, lesson.id])"
                                    class="flex items-center p-3 rounded transition justify-between"
                                    :class="lesson.id === currentLesson.id ? 'bg-indigo-50 border-l-4 border-indigo-600' : 'hover:bg-gray-50'"
                                >
                                    <div class="flex items-center">
                                        <span class="text-xs font-bold mr-3 w-6" :class="lesson.id === currentLesson.id ? 'text-indigo-700' : 'text-gray-500'">
                                            #{{ lesson.position }}
                                        </span>
                                        <span class="text-sm truncate w-40" :class="lesson.id === currentLesson.id ? 'font-bold text-indigo-900' : 'text-gray-700'">
                                            {{ lesson.title }}
                                        </span>
                                    </div>
                                    
                                    <span v-if="lesson.is_completed" class="text-green-500 text-lg">✓</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>