<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    courses: Array
});

// Função para formatar preço (R$ 97,00)
const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}
</script>

<template>
    <Head title="Cursos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Cursos
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
                
                <div v-if="courses.length === 0" class="text-gray-500 text-center">
                    Nenhum curso disponível no momento.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <div 
                        v-for="course in courses" 
                        :key="course.id" 
                        class="flex flex-col border rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition bg-white h-full"
                    >
                        <div class="h-48 bg-gray-200 w-full relative">
                            <img 
                                v-if="course.image_url" 
                                :src="course.image_url" 
                                class="w-full h-full object-cover" 
                                alt="Capa do curso"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                <span class="text-4xl">📚</span>
                            </div>

                            <div class="absolute top-2 right-2 bg-white px-2 py-1 rounded shadow text-sm font-bold text-gray-800">
                                {{ parseFloat(course.price) === 0 ? 'GRÁTIS' : formatPrice(course.price) }}
                            </div>
                        </div>

                        <div class="p-4 flex flex-col flex-grow justify-between">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800 mb-1">{{ course.title }}</h3>
                                <p class="text-gray-600 text-sm line-clamp-3 mb-4">{{ course.description }}</p>
                            </div>

                            <div class="mt-2">
                                
                                <div v-if="course.is_enrolled || $page.props.auth.user.is_admin">
                                    <div class="mb-3">
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            <span>Seu Progresso</span>
                                            <span>{{ course.progress_percent }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" :style="{ width: course.progress_percent + '%' }"></div>
                                        </div>
                                    </div>

                                    <Link :href="course.target_route" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded transition">
                                        {{ $page.props.auth.user.is_admin ? 'Gerenciar Curso ⚙️' : (course.progress_percent === 0 ? 'Começar Aula' : 'Continuar') }}
                                    </Link>
                                </div>

                                <div v-else>
                                    <Link 
                                        :href="route('courses.public', course.id)"
                                        class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded transition"
                                    >
                                        Saiba Mais / Matricular
                                    </Link>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>