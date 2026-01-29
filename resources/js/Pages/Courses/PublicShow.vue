<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue'; // <--- Importamos o Layout

const props = defineProps({
    course: Object,
    userHasCourse: Boolean,
    isLoggedIn: Boolean
});

const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}

const getYoutubeEmbed = (url) => {
    if (!url) return null;
    const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
    return match ? `https://www.youtube.com/embed/${match[1]}` : null;
}

const form = useForm({});
const buyCourse = () => {
    form.post(route('checkout.store', props.course.id));
}
</script>

<template>
    <Head :title="course.title" />

    <GuestLayout>
        
        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <div class="aspect-video bg-black rounded-xl overflow-hidden shadow-lg relative">
                            <iframe 
                                v-if="course.video_url" :src="getYoutubeEmbed(course.video_url)" 
                                title="YouTube video player" frameborder="0" allowfullscreen class="w-full h-full"
                            ></iframe>
                            <img v-else-if="course.image_url" :src="course.image_url" class="w-full h-full object-cover opacity-80">
                            <div v-else class="w-full h-full flex items-center justify-center text-white text-lg">Sem vídeo</div>
                        </div>

                        <div class="bg-white p-8 rounded-xl shadow-sm">
                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ course.title }}</h1>
                            <p class="text-gray-600 whitespace-pre-line leading-relaxed text-lg">{{ course.description }}</p>
                        </div>

                        <div class="bg-white p-8 rounded-xl shadow-sm">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">O que você vai aprender</h3>
                            <ul class="space-y-3">
                                <li v-for="lesson in course.lessons" :key="lesson.id" class="flex items-center p-3 bg-gray-50 rounded border border-gray-100 text-gray-500">
                                    <span class="mr-3">🔒</span><span>{{ lesson.title }}</span>
                                </li>
                                <li v-if="course.lessons.length === 0" class="text-gray-400 italic">Grade sendo atualizada.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-white p-6 rounded-xl shadow-lg sticky top-8 border border-indigo-50">
                            <img v-if="course.image_url" :src="course.image_url" class="w-full h-32 object-cover rounded-lg mb-6">
                            <div class="mb-6 text-center">
                                <p class="text-gray-500 text-sm mb-1">Investimento Total</p>
                                <p class="text-4xl font-extrabold text-indigo-600">
                                    {{ parseFloat(course.price) === 0 ? 'GRÁTIS' : formatPrice(course.price) }}
                                </p>
                            </div>
                            <div class="space-y-3">
                                <div v-if="userHasCourse">
                                    <Link :href="route('courses.show', course.id)" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg text-lg shadow">Acessar Curso</Link>
                                </div>
                                <div v-else>
                                    <button v-if="isLoggedIn" @click="buyCourse" :disabled="form.processing" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg text-lg shadow-lg">
                                        {{ form.processing ? 'Processando...' : 'Comprar Agora (Pix)' }}
                                    </button>
                                    <Link v-else :href="route('login')" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg text-lg shadow-lg">
                                        Fazer Login para Comprar
                                    </Link>
                                    <p class="text-xs text-center text-gray-400 mt-2">Garantia de 7 dias • Certificado incluso</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </GuestLayout>
</template>