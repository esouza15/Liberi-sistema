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
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100">
                            <span class="text-3xl">💸</span>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Finalize seu Pedido</h2>
                    <p class="text-gray-600 mb-8">
                        Você está adquirindo: <strong class="text-indigo-600">{{ order.course.title }}</strong>
                    </p>

                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 mb-8">
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">Valor a Pagar</p>
                        <p class="text-4xl font-extrabold text-gray-900 mb-6">{{ formatPrice(order.amount) }}</p>

                        <div class="bg-white p-4 rounded border inline-block mb-4">
                            <div class="w-48 h-48 bg-gray-200 flex items-center justify-center text-gray-400 text-xs text-center p-2">
                                (Integração Pix Asaas<br>será configurada aqui)
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500">
                            Status atual: <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 font-bold uppercase text-xs">{{ order.status }}</span>
                        </p>
                    </div>

                    <Link :href="route('dashboard')" class="text-indigo-600 hover:underline">
                        Voltar ao Painel
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>