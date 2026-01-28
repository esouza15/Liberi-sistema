<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

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
                
                <Link 
                    v-if="$page.props.auth.user.is_admin" 
                    :href="route('courses.create')" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm"
                >
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
                        
                        <div 
                            v-for="course in courses" 
                            :key="course.id" 
                            class="flex flex-col justify-between border rounded-lg hover:shadow-lg transition bg-white p-4 h-full relative"
                        >
                            <Link :href="course.target_route" class="block flex-grow">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold text-lg text-indigo-600 hover:underline">{{ course.title }}</h3>
                                    
                                    <span v-if="course.progress_percent === 100" class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded">
                                        Concluído
                                    </span>
                                </div>

                                <p class="text-gray-600 mt-2 text-sm line-clamp-3">{{ course.description || 'Sem descrição' }}</p>
                                
                                <div class="mt-4">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Progresso</span>
                                        <span>{{ course.progress_percent }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div 
                                            class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" 
                                            :style="{ width: course.progress_percent + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </Link>

                            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                                <Link :href="course.target_route" class="text-xs text-indigo-600 font-bold hover:text-indigo-800">
                                    {{ course.progress_percent === 0 ? 'Começar Curso →' : (course.progress_percent === 100 ? 'Revisar Curso ↺' : 'Continuar Assistindo →') }}
                                </Link>

                                <Link 
                                    v-if="$page.props.auth.user.is_admin" 
                                    :href="route('courses.show', course.id)" 
                                    class="text-xs text-gray-400 hover:text-gray-700 border border-gray-300 rounded px-2 py-1 transition"
                                >
                                    ⚙ Gerenciar
                                </Link>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>