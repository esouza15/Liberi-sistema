<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    order: Object
});

const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}
</script>

<template>
    <Head title="Pagamento" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                    
                    <div class="mb-6">
                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gray-50">
                            <svg 
                                xmlns="http://www.w3.org/2000/svg" 
                                viewBox="0 0 512 512" 
                                class="h-12 w-12"
                                fill="#4DB6AC" 
                            >
                                <path d="M410.7 131.7L282.6 3.6c-15-15-39.3-15-54.3 0l-45.7 45.7-45.7-45.7c-15-15-39.3-15-54.3 0L3.6 131.7c-4.8 4.8-4.8 12.6 0 17.4l45.7 45.7-45.7 45.7c-4.8 4.8-4.8 12.6 0 17.4l79.1 128.1c15 15 39.3 15 54.3 0l45.7-45.7 45.7 45.7c15 15 39.3 15 54.3 0l128.1-128.1c4.8-4.8 4.8-12.6 0-17.4l-45.7-45.7 45.7-45.7c4.8-4.8 4.8-12.6 0-17.4zm-155.1 82l-51.4 51.4-51.4-51.4 51.4-51.4 51.4 51.4z"/>
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Finalize seu Pedido</h2>
                    <p class="text-gray-600 mb-8">
                        Você está adquirindo: <strong class="text-indigo-600">{{ order.course.title }}</strong>
                    </p>

                    <div class="border-2 border-dashed border-[#4DB6AC] rounded-lg p-6 bg-[#F0FDF9] mb-8">
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">Valor a Pagar via Pix</p>
                        <p class="text-4xl font-extrabold text-[#4DB6AC] mb-6">{{ formatPrice(order.amount) }}</p>

                        <div class="bg-white p-4 rounded border inline-block mb-4 shadow-sm">
                            <div class="w-48 h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-xs text-center p-2 rounded">
                                (O QR Code do Asaas<br>aparecerá aqui)
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500">
                            Status atual: <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 font-bold uppercase text-xs">{{ order.status }}</span>
                        </p>
                    </div>

                    <Link :href="route('dashboard')" class="text-gray-500 hover:text-gray-800 underline text-sm">
                        Cancelar e Voltar ao Painel
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>