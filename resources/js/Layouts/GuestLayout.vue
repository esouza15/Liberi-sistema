<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        
        <header class="bg-white shadow-sm relative z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link href="/">
                                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                            </Link>
                        </div>
                    </div>

                    <nav class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                        <Link 
                            v-if="$page.props.auth.user" 
                            :href="route('dashboard')" 
                            class="text-gray-600 hover:text-indigo-600 font-medium"
                        >
                            Meu Painel
                        </Link>
                        
                        <div v-else class="space-x-4 flex items-center">
                            <Link :href="route('login')" class="text-gray-600 hover:text-gray-900 font-medium transition">
                                Entrar
                            </Link>
                            <Link :href="route('register')" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                                Criar Conta
                            </Link>
                        </div>
                    </nav>

                    <div class="-mr-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div
                :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="sm:hidden border-t border-gray-200"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <div v-if="$page.props.auth.user">
                        <Link
                            :href="route('dashboard')"
                            class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300"
                        >
                            Ir para o Painel
                        </Link>
                    </div>
                    <div v-else class="space-y-1">
                        <Link
                            :href="route('login')"
                            class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300"
                        >
                            Entrar
                        </Link>
                        <Link
                            :href="route('register')"
                            class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 hover:border-indigo-300"
                        >
                            Criar Conta
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <slot />
        </main>

        <footer class="bg-gray-800 text-white py-8 mt-auto text-center text-sm text-gray-400">
            &copy; {{ new Date().getFullYear() }} Liberi Cursos. Todos os direitos reservados.
        </footer>
    </div>
</template>