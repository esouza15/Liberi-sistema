<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3'; // Adicionado router
import { ref } from 'vue';

// Componentes de UI
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue'; // Adicionado Botão Vermelho
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    course: Object
});

// --- LÓGICA DE EDIÇÃO DO CURSO (TOGGLE) ---
const isEditingCourse = ref(false);

const formCourse = useForm({
    _method: 'PUT',
    title: props.course.title,
    description: props.course.description,
    price: props.course.price,
    video_url: props.course.video_url,
    image: null
});

const submitCourseUpdate = () => {
    formCourse.post(route('courses.update', props.course.id), {
        onSuccess: () => {
            isEditingCourse.value = false;
        }
    });
};

// --- NOVA FUNÇÃO: EXCLUIR CURSO ---
const destroyCourse = () => {
    if (confirm('ATENÇÃO: Tem certeza que deseja excluir este curso?\n\nIsso apagará todas as aulas e removerá o acesso dos alunos matriculados.\n\nEsta ação não pode ser desfeita.')) {
        router.delete(route('courses.destroy', props.course.id));
    }
};

// --- LÓGICA DE NOVA AULA ---
const formLesson = useForm({
    title: '',
    video_url: '',
    position: props.course.lessons.length + 1
});

const submitLesson = () => {
    formLesson.post(route('courses.lessons.store', props.course.id), {
        onSuccess: () => formLesson.reset(),
    });
};

// Formatação de preço
const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}
</script>

<template>
    <Head :title="course.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestão do Curso
                </h2>
                <Link :href="route('courses.index')" class="text-gray-500 hover:text-gray-700 text-sm">
                    ← Voltar para Catálogo
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white p-6 shadow sm:rounded-lg relative">
                    
                    <button 
                        v-if="$page.props.auth.user.is_admin"
                        @click="isEditingCourse = !isEditingCourse"
                        class="absolute top-6 right-6 text-sm text-blue-600 hover:text-blue-800 border border-blue-200 bg-blue-50 px-3 py-1 rounded transition"
                    >
                        {{ isEditingCourse ? 'Cancelar Edição ✕' : 'Editar Dados do Curso ✏' }}
                    </button>

                    <div v-if="!isEditingCourse">
                        <div class="flex gap-6">
                            <div class="w-1/4">
                                <img v-if="course.image_url" :src="course.image_url" class="w-full rounded shadow-sm">
                                <div v-else class="w-full h-32 bg-gray-200 flex items-center justify-center rounded text-gray-400">Sem Capa</div>
                            </div>
                            
                            <div class="w-3/4 pr-20"> 
                                <h3 class="text-2xl font-bold text-gray-900">{{ course.title }}</h3>
                                <p class="text-green-600 font-bold text-lg mb-2">{{ parseFloat(course.price) === 0 ? 'Gratuito' : formatPrice(course.price) }}</p>
                                <p class="text-gray-600 whitespace-pre-line">{{ course.description }}</p>
                                
                                <div v-if="course.video_url" class="mt-4 text-sm text-gray-500">
                                    🎥 Vídeo de apresentação configurado.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else>
                        <form @submit.prevent="submitCourseUpdate" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel value="Título" />
                                    <TextInput v-model="formCourse.title" class="w-full" required />
                                </div>
                                <div>
                                    <InputLabel value="Preço (R$)" />
                                    <TextInput v-model="formCourse.price" type="number" step="0.01" class="w-full" required />
                                </div>
                            </div>

                            <div>
                                <InputLabel value="Descrição" />
                                <textarea v-model="formCourse.description" class="w-full border-gray-300 rounded shadow-sm" rows="4"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel value="Vídeo de Vendas (URL)" />
                                    <TextInput v-model="formCourse.video_url" class="w-full" />
                                </div>
                                <div>
                                    <InputLabel value="Trocar Capa (Opcional)" />
                                    <input type="file" @input="formCourse.image = $event.target.files[0]" class="block w-full text-sm text-gray-500 mt-1" />
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-4 border-t mt-4">
                                <DangerButton type="button" @click="destroyCourse">
                                    Excluir Curso
                                </DangerButton>

                                <PrimaryButton :disabled="formCourse.processing">
                                    Salvar Alterações
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Conteúdo / Aulas</h3>
                    
                    <div v-if="course.lessons.length === 0" class="text-gray-400 italic py-4 text-center">
                        Nenhuma aula cadastrada ainda. Use o formulário abaixo para começar.
                    </div>

                    <ul v-else class="space-y-3">
                        <li v-for="lesson in course.lessons" :key="lesson.id" class="flex items-center justify-between bg-gray-50 p-3 rounded border hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <span class="bg-indigo-100 text-indigo-800 font-bold py-1 px-2 rounded text-xs mr-3">
                                    #{{ lesson.position }}
                                </span>
                                <span class="text-gray-700 font-medium">{{ lesson.title }}</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <Link :href="route('lessons.show', [course.id, lesson.id])" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
                                    <span>▶</span> Assistir
                                </Link>

                                <Link 
                                    v-if="$page.props.auth.user.is_admin"
                                    :href="route('lessons.edit', [course.id, lesson.id])"
                                    class="text-xs bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-3 py-1 rounded shadow-sm"
                                >
                                    Editar Aula
                                </Link>
                            </div>
                        </li>
                    </ul>
                </div>

                <div v-if="$page.props.auth.user.is_admin" class="bg-white p-6 shadow sm:rounded-lg border-l-4 border-indigo-500">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">+ Adicionar Nova Aula</h3>
                    
                    <form @submit.prevent="submitLesson" class="flex gap-4 items-end flex-wrap">
                        <div class="flex-grow min-w-[200px]">
                            <InputLabel value="Título da Aula" />
                            <TextInput v-model="formLesson.title" class="w-full" required placeholder="Ex: Introdução ao Módulo" />
                        </div>
                        
                        <div class="flex-grow min-w-[200px]">
                            <InputLabel value="Link do Vídeo (YouTube)" />
                            <TextInput v-model="formLesson.video_url" class="w-full" required placeholder="https://youtube.com/..." />
                        </div>

                        <div class="w-24">
                             <InputLabel value="Ordem" />
                             <TextInput v-model="formLesson.position" type="number" class="w-full" />
                        </div>

                        <PrimaryButton :disabled="formLesson.processing">
                            Salvar Aula
                        </PrimaryButton>
                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>