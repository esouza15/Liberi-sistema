<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    order: Object
});

const formatPrice = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}

const copied = ref(false);

const copyCode = () => {
    navigator.clipboard.writeText(props.order.qr_code_payload);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
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
                                fill="#4DB6AC" 
                                viewBox="0 0 16 16" 
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-12 h-12"
                            >
                                <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                <g id="SVGRepo_iconCarrier">
                                    <path d="M11.917 11.71a2.046 2.046 0 0 1-1.454-.602l-2.1-2.1a.4.4 0 0 0-.551 0l-2.108 2.108a2.044 2.044 0 0 1-1.454.602h-.414l2.66 2.66c.83.83 2.177.83 3.007 0l2.667-2.668h-.253zM4.25 4.282c.55 0 1.066.214 1.454.602l2.108 2.108a.39.39 0 0 0 .552 0l2.1-2.1a2.044 2.044 0 0 1 1.453-.602h.253L9.503 1.623a2.127 2.127 0 0 0-3.007 0l-2.66 2.66h.414z"/>
                                    <path d="m14.377 6.496-1.612-1.612a.307.307 0 0 1-.114.023h-.733c-.379 0-.75.154-1.017.422l-2.1 2.1a1.005 1.005 0 0 1-1.425 0L5.268 5.32a1.448 1.448 0 0 0-1.018-.422h-.9a.306.306 0 0 1-.109-.021L1.623 6.496c-.83.83-.83 2.177 0 3.008l1.618 1.618a.305.305 0 0 1 .108-.022h.901c.38 0 .75-.153 1.018-.421L7.375 8.57a1.034 1.034 0 0 1 1.426 0l2.1 2.1c.267.268.638.421 1.017.421h.733c.04 0 .079.01.114.024l1.612-1.612c.83-.83.83-2.178 0-3.008z"/>
                                </g>
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

                        <div class="bg-white p-4 rounded border inline-block mb-4 shadow-sm relative group">
                            <img 
                                v-if="order.qr_code_image" 
                                :src="'data:image/png;base64,' + order.qr_code_image" 
                                class="w-48 h-48 object-contain"
                            />
                            
                            <div v-else class="w-48 h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-xs text-center p-2 rounded">
                                Gerando QR Code...<br>Atualize a página.
                            </div>
                        </div>

                        <div v-if="order.qr_code_payload" class="mb-4">
                            <div class="flex items-center justify-center gap-2">
                                <input 
                                    type="text" 
                                    readonly 
                                    :value="order.qr_code_payload" 
                                    class="text-xs text-gray-500 border-gray-300 rounded w-64 bg-gray-50"
                                >
                                <button 
                                    @click="copyCode" 
                                    class="bg-indigo-100 text-indigo-700 px-3 py-2 rounded text-xs font-bold hover:bg-indigo-200 transition"
                                >
                                    Copiar
                                </button>
                            </div>
                            <p v-if="copied" class="text-green-600 text-xs mt-1 font-bold">Copiado!</p>
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