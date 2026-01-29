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
                        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-[#F0FDF9]">
                            <svg 
                                xmlns="http://www.w3.org/2000/svg" 
                                viewBox="0 0 48 48"
                                class="w-12 h-12"
                            >
                                <path fill="#4db6ac" d="M11.9,12h-0.68l8.04-8.04c2.62-2.61,6.86-2.61,9.48,0L36.78,12H36.1c-1.6,0-3.11,0.62-4.24,1.76 l-6.8,6.77c-0.59,0.59-1.53,0.59-2.12,0l-6.8-6.77C15.01,12.62,13.5,12,11.9,12z"></path>
                                <path fill="#4db6ac" d="M36.1,36h0.68l-8.04,8.04c-2.62,2.61-6.86,2.61-9.48,0L11.22,36h0.68c1.6,0,3.11-0.62,4.24-1.76 l6.8-6.77c0.59-0.59,1.53-0.59,2.12,0l6.8,6.77C32.99,35.38,34.5,36,36.1,36z"></path>
                                <path fill="#4db6ac" d="M44.04,28.74L38.78,34H36.1c-1.07,0-2.07-0.42-2.83-1.17l-6.8-6.78c-1.36-1.36-3.58-1.36-4.94,0 l-6.8,6.78C13.97,33.58,12.97,34,11.9,34H9.22l-5.26-5.26c-2.61-2.62-2.61-6.86,0-9.48L9.22,14h2.68c1.07,0,2.07,0.42,2.83,1.17 l6.8,6.78c0.68,0.68,1.58,1.02,2.47,1.02s1.79-0.34,2.47-1.02l6.8-6.78C34.03,14.42,35.03,14,36.1,14h2.68l5.26,5.26 C46.65,21.88,46.65,26.12,44.04,28.74z"></path>
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