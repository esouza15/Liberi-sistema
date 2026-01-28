<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    courses: Array
});
</script>

<template>
    <Head title="Meus Cursos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Painel do Aluno</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Olá, {{ $page.props.auth.user.name }}! 👋</h3>
                    <p class="text-gray-600">Continue estudando de onde parou.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link 
                        v-for="course in courses" 
                        :key="course.id" 
                        :href="course.target_route" 
                        class="block border p-4 rounded-lg hover:shadow-lg transition bg-white cursor-pointer"
                    >
                        <div class="flex justify-between items-start">
                            <h3 class="font-bold text-lg text-indigo-600">{{ course.title }}</h3>
                            <span v-if="course.progress_percent === 100" class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded">
                                Concluído
                            </span>
                        </div>

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

                        <span class="text-xs text-gray-400 mt-4 block">
                            {{ course.progress_percent === 0 ? 'Iniciar Curso ▶' : 'Continuar Assistindo ▶' }}
                        </span>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>