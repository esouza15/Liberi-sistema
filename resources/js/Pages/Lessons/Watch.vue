<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    currentLesson: Object
});
</script>

<template>
    <Head :title="currentLesson.title" />

    <AuthenticatedLayout>
        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex flex-col lg:flex-row gap-6">
                
                <div class="lg:w-2/3">
                    <div class="bg-black rounded-lg overflow-hidden shadow-xl aspect-video">
                        <iframe 
                            class="w-full h-full"
                            :src="currentLesson.embed_url" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>

                    <div class="mt-4 bg-white p-6 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-4">
                            <h1 class="text-2xl font-bold text-gray-900">{{ currentLesson.title }}</h1>
                            <Link :href="route('courses.index')" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                Voltar aos Cursos
                            </Link>
                        </div>
                        <p class="text-gray-600">Aula {{ currentLesson.position }} do curso {{ course.title }}</p>
                    </div>
                </div>

                <div class="lg:w-1/3">
                    <div class="bg-white rounded-lg shadow p-4 h-fit max-h-screen overflow-y-auto">
                        <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Conteúdo do Curso</h3>
                        
                        <ul class="space-y-2">
                            <li v-for="lesson in course.lessons" :key="lesson.id">
                                <Link 
                                    :href="route('lessons.show', [course.id, lesson.id])"
                                    class="flex items-center p-3 rounded transition"
                                    :class="lesson.id === currentLesson.id ? 'bg-indigo-100 border-l-4 border-indigo-600' : 'hover:bg-gray-50'"
                                >
                                    <span class="text-xs font-bold mr-3" 
                                          :class="lesson.id === currentLesson.id ? 'text-indigo-700' : 'text-gray-500'">
                                        #{{ lesson.position }}
                                    </span>
                                    <span class="text-sm" :class="lesson.id === currentLesson.id ? 'font-bold text-indigo-900' : 'text-gray-700'">
                                        {{ lesson.title }}
                                    </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>