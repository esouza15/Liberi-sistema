<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    price: '',       // Novo: Preço
    image: null,     // Novo: Arquivo de Imagem
    video_url: '',
});

const submit = () => {
    // O Inertia detecta automaticamente que tem arquivo e envia como Multipart form data
    form.post(route('courses.store'));
};
</script>

<template>
    <Head title="Criar Curso" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Novo Curso
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        
                        <div class="mb-4">
                            <InputLabel for="title" value="Título do Curso" />
                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.title"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="description" value="Descrição" />
                            <textarea
                                id="description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                v-model="form.description"
                                rows="4"
                                required
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <InputLabel for="price" value="Preço (R$)" />
                                <TextInput
                                    id="price"
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="mt-1 block w-full"
                                    v-model="form.price"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>

                            <div>
                                <InputLabel for="video_url" value="Vídeo de Apresentação (YouTube URL)" />
                                <TextInput
                                    id="video_url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    v-model="form.video_url"
                                />
                                <InputError class="mt-2" :message="form.errors.video_url" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <InputLabel for="image" value="Imagem de Capa" />
                            <input 
                                type="file" 
                                @input="form.image = $event.target.files[0]"
                                class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100"
                            />
                            <p class="text-xs text-gray-500 mt-1">Recomendado: JPG ou PNG (1200x600px)</p>
                            <InputError class="mt-2" :message="form.errors.image" />
                            
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full mt-2">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <Link :href="route('courses.index')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                Cancelar
                            </Link>

                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Salvar Curso
                            </PrimaryButton>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>