<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object
});

// Preenche o formulário com os dados atuais
const form = useForm({
    _method: 'PUT', // Truque para enviar arquivo na edição
    title: props.course.title,
    description: props.course.description,
    price: props.course.price,
    image: null, 
    video_url: props.course.video_url,
});

const submit = () => {
    // Usamos POST com _method: PUT por limitação do envio de arquivos via XHR
    form.post(route('courses.update', props.course.id));
};
</script>

<template>
    <Head title="Editar Curso" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800">Editar Curso</h2>
        </template>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        
                        <div class="mb-4">
                            <InputLabel for="title" value="Título" />
                            <TextInput id="title" v-model="form.title" type="text" class="block w-full mt-1" required />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="description" value="Descrição" />
                            <textarea id="description" v-model="form.description" class="w-full border-gray-300 rounded-md shadow-sm" rows="4"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <InputLabel for="price" value="Preço (R$)" />
                                <TextInput id="price" v-model="form.price" type="number" step="0.01" class="block w-full mt-1" />
                            </div>
                            <div>
                                <InputLabel for="video_url" value="Vídeo URL" />
                                <TextInput id="video_url" v-model="form.video_url" type="url" class="block w-full mt-1" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <InputLabel value="Capa Atual" />
                            <img v-if="course.image_url" :src="course.image_url" class="w-32 h-20 object-cover rounded mb-2 border" />
                            
                            <InputLabel for="image" value="Trocar Imagem (Opcional)" />
                            <input type="file" @input="form.image = $event.target.files[0]" class="block w-full text-sm text-gray-500" />
                        </div>

                        <div class="flex justify-end gap-4">
                            <Link :href="route('courses.index')" class="text-gray-600 underline self-center">Cancelar</Link>
                            <PrimaryButton :disabled="form.processing">Salvar Alterações</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>