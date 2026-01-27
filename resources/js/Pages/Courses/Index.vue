<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Recebe a lista de cursos enviada pelo Controller
defineProps({
    courses: Array
});
</script>

<template>
    <Head title="Cursos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Cursos Disponíveis
                </h2>
                <Link :href="route('courses.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                    + Novo Curso
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <div v-if="courses.length === 0" class="text-gray-500 text-center">
                        Nenhum curso cadastrado ainda.
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Link :href="route('courses.show', course.id)" class="block border p-4 rounded-lg hover:shadow-lg transition bg-white cursor-pointer">
                            <h3 class="font-bold text-lg text-indigo-600">{{ course.title }}</h3>
                            <p class="text-gray-600 mt-2">{{ course.description }}</p>
                            <span class="text-xs text-gray-400 mt-4 block">Clique para gerenciar aulas →</span>
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>