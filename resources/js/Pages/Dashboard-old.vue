<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    courses: Array
});

// Verifica se tem progresso
const hasStartedAnyCourse = computed(() => {
    return props.courses.some(course => course.progress_percent > 0);
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
                    <p class="text-gray-600">
                        {{ hasStartedAnyCourse ? 'Continue estudando de onde parou.' : 'Bem-vindo! Que tal começar um curso novo hoje?' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div 
                        v-for="course in props.courses" 
                        :key="course.id" 
                        class="flex flex-col border rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition bg-white h-full"
                    >
                        <div class="h-40 bg-gray-200 w-full relative">
                            <img 
                                v-if="course.image_url" 
                                :src="course.image_url" 
                                class="w-full h-full object-cover" 
                                alt="Capa do curso"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                <span class="text-4xl">📚</span>
                            </div>
                        </div>

                        <div class="p-4 flex flex-col flex-grow justify-between">
                            <div>
                                <h3 class="font-bold text-lg text-indigo-600 mb-2">{{ course.title }}</h3>
                                
                                <div class="mb-2">
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
                            </div>

                            <Link 
                                :href="course.target_route" 
                                class="mt-4 block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 rounded transition"
                            >
                                {{ course.progress_percent === 0 ? 'Iniciar Curso ▶' : 'Continuar Assistindo ▶' }}
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>